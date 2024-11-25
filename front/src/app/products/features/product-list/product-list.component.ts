import { Component, OnInit, inject, signal } from "@angular/core";
import { Product } from "app/products/data-access/product.model";
import { ProductsService } from "app/products/data-access/products.service";
import { ProductFormComponent } from "app/products/ui/product-form/product-form.component";
import { ButtonModule } from "primeng/button";
import { CardModule } from "primeng/card";
import { DataViewModule } from "primeng/dataview";
import { DialogModule } from "primeng/dialog";
import { TagModule } from "primeng/tag";
import { CommonModule } from "@angular/common";
import { FileUploadModule } from "primeng/fileupload";
import { ConfirmDialogModule } from "primeng/confirmdialog";
import { ConfirmationService, SelectItem } from "primeng/api";
import { InputTextModule } from "primeng/inputtext";
import { FormsModule } from "@angular/forms";
import { FloatLabelModule } from "primeng/floatlabel";
import { debounceTime, Subject, switchMap } from "rxjs";
import { DropdownModule } from "primeng/dropdown";

const emptyProduct: Product = {
  id: 0,
  code: "",
  name: "",
  description: "",
  image: "",
  imageFile: null,
  category: "",
  price: 0,
  quantity: 0,
  internalReference: "",
  shellId: 0,
  inventoryStatus: "INSTOCK",
  rating: 0,
  createdAt: 0,
  updatedAt: 0,
};

@Component({
  selector: "app-product-list",
  templateUrl: "./product-list.component.html",
  styleUrls: ["./product-list.component.scss"],
  standalone: true,
  imports: [
    DataViewModule,
    CardModule,
    ButtonModule,
    DialogModule,
    ProductFormComponent,
    TagModule,
    CommonModule,
    FileUploadModule,
    ConfirmDialogModule,
    InputTextModule,
    FormsModule,
    FloatLabelModule,
    DropdownModule,
  ],
})
export class ProductListComponent implements OnInit {
  private readonly productsService = inject(ProductsService);

  public readonly products = this.productsService.products;

  private filter: String[] = [];

  private searchSubject = new Subject<String>();

  public readonly categories: SelectItem[] = [
    { value: "Accessories", label: "Accessories" },
    { value: "Fitness", label: "Fitness" },
    { value: "Clothing", label: "Clothing" },
    { value: "Electronics", label: "Electronics" },
  ];

  public readonly statuses: SelectItem[] = [
    { value: "INSTOCK", label: "En stock" },
    { value: "LOWSTOCK", label: "Stock faible" },
    { value: "OUTOFSTOCK", label: "En rupture" },
  ];

  public keyWord: string = "";
  public category: string | null = null;
  public status: string | null = null;
  public isDialogVisible = false;
  public isCreation = false;
  public readonly editedProduct = signal<Product>(emptyProduct);

  constructor(private confirmationService: ConfirmationService) {}

  ngOnInit() {
    this.productsService.get(this.filter.join("&")).subscribe();
    this.searchSubject
      .pipe(
        debounceTime(500),
        switchMap((term: String) =>
          this.productsService.get(this.filter.join("&"))
        )
      )
      .subscribe();
  }

  onSearch() {
    this.filter = [];
    if (this.keyWord) {
      this.filter.push(`keyword=${this.keyWord}`);
    }
    if (this.category) {
      this.filter.push(`category=${this.category}`);
    }
    if (this.status) {
      this.filter.push(`status=${this.status}`);
    }
    this.searchSubject.next(this.filter.join("&"));
  }

  public onCreate() {
    this.isCreation = true;
    this.isDialogVisible = true;
    this.editedProduct.set(emptyProduct);
  }

  public onUpdate(product: Product) {
    this.isCreation = false;
    this.isDialogVisible = true;
    this.editedProduct.set(product);
  }

  public onDelete(product: Product) {
    this.confirmationService.confirm({
      message: `Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`,
      header: "Confirmation de suppression",
      icon: "pi pi-exclamation-triangle",
      acceptButtonStyleClass: "p-button-danger p-button-outlined",
      rejectButtonStyleClass: "p-button-secondary p-button-outlined",
      accept: () => {
        this.productsService.delete(product.id).subscribe();
      },
    });
  }

  public onSave(product: Product) {
    if (this.isCreation) {
      this.productsService.create(product).subscribe();
    } else {
      this.productsService.update(product).subscribe();
    }
    this.closeDialog();
  }

  public onCancel() {
    this.closeDialog();
  }

  private closeDialog() {
    this.isDialogVisible = false;
  }

  getSeverity(product: Product) {
    switch (product.inventoryStatus) {
      case "INSTOCK":
        return "success";

      case "LOWSTOCK":
        return "warning";

      case "OUTOFSTOCK":
        return "danger";

      default:
        return undefined;
    }
  }

  getSeverityLabel(product: Product) {
    switch (product.inventoryStatus) {
      case "INSTOCK":
        return "En stock";

      case "LOWSTOCK":
        return "Stock faible";

      case "OUTOFSTOCK":
        return "En rupture";

      default:
        return undefined;
    }
  }
}
