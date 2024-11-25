import { Injectable, inject, signal } from "@angular/core";
import { Product } from "./product.model";
import { HttpClient } from "@angular/common/http";
import { catchError, Observable, of, tap } from "rxjs";

@Injectable({
  providedIn: "root",
})
export class ProductsService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = "http://kata.alten";
  private readonly path = `${this.baseUrl}/api/products`;

  private readonly _products = signal<Product[]>([]);

  public readonly products = this._products.asReadonly();

  public get(filter: String): Observable<Product[]> {
    return this.http.get<Product[]>(`${this.path}?${filter}`).pipe(
      catchError((error) => {
        return this.http.get<Product[]>("assets/products.json");
      }),
      tap((products) => this._products.set(products))
    );
  }

  public create(product: Product): Observable<Product | null> {
    return this.http.post<Product>(this.path, this.getFormData(product)).pipe(
      catchError((err) => {
        return of(null);
      }),
      tap((createdProduct) => {
        if (createdProduct) {
          this._products.update((products) => [createdProduct, ...products]);
        }
      })
    );
  }

  public update(product: Product): Observable<Product | null> {
    return this.http
      .post<Product>(
        `${this.path}/${product.id}`,
        this.getFormData(product, "update")
      )
      .pipe(
        catchError((err) => {
          return of(null);
        }),
        tap((updatedProduct) => {
          if (updatedProduct) {
            this._products.update((products) => {
              return products.map((p) =>
                p.id === updatedProduct.id ? updatedProduct : p
              );
            });
          }
        })
      );
  }

  public delete(productId: number): Observable<boolean> {
    return this.http.delete<boolean>(`${this.path}/${productId}`).pipe(
      catchError(() => {
        return of(true);
      }),
      tap(() =>
        this._products.update((products) =>
          products.filter((product) => product.id !== productId)
        )
      )
    );
  }

  private getFormData(product: Product | null, mode = "create") {
    if (product) {
      const formData = new FormData();
      if (mode == "update") {
        formData.append("_method", "PUT");
      }
      formData.append("name", product.name);
      formData.append("description", product.description);
      formData.append("price", product.price.toString());
      formData.append("category", product.category);
      formData.append("quantity", product.quantity.toString());
      formData.append("inventoryStatus", product.inventoryStatus);
      formData.append("rating", product.rating.toString());
      if (product.imageFile) {
        formData.append("imageFile", product.imageFile);
      }
      return formData;
    }
    return null;
  }
}
