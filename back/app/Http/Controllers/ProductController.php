<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    protected $product;

    public function __construct(ProductRepository $product)
    {
        $this->product = $product;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ProductCollection($this->product->getAllProducts());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            Helper::formatDataNullable($data);
            $rules = [
                'name' => ["required", "unique:products"],
                'description' => ["present"],
                'category' => ["present"],
                'price' => ["required", "decimal:0"],
                'imageFile' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            $messages = [
                "required" => "Ce champ est requis",
                "unique" => "Ce champ est déjà affecté à un autre produit",
                "present" => "Ce champ doit être présent",
                "decimal" => "Ce champ doit contenir un nombre décimal",
            ];

            $validator = validator($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->hasFile("imageFile")) {
                $directory = '/images/products';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                $file = $request->file("imageFile");
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($directory, $fileName, "public");
                $data["image"] = '/images/products/' . $fileName;
            }

            $product = $this->product->create($data);
            DB::commit();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["errors" => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            Helper::formatDataNullable($data);
            $rules = [
                'name' => ["required", "unique:products,name," . $product->id],
                'description' => ["present"],
                'category' => ["present"],
                'price' => ["required", "decimal:0"],
                'imageFile' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            $messages = [
                "required" => "Ce champ est requis",
                "unique" => "Ce champ est déjà affecté à un autre produit",
                "present" => "Ce champ doit être présent",
                "decimal" => "Ce champ doit contenir un nombre décimal",
            ];

            $validator = validator($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if ($request->hasFile("imageFile")) {
                if ($product->image && Storage::disk("public")->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $directory = '/images/products';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                $file = $request->file("imageFile");
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($directory, $fileName, "public");
                $data["image"] = '/images/products/' . $fileName;
            }
            $product->update($data);
            DB::commit();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["errors" => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $imgPath = $product->image;
        $product->delete();
        if ($imgPath && Storage::disk("public")->exists($imgPath)) {
            Storage::disk('public')->delete($imgPath);
        }
        return response()->json(['message' => 'Le produit a bien été supprimé'], 201);
    }
}
