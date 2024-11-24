<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return new ProductCollection($this->product->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /* Le démarrage d'une transaction de base des données dans 
            ce cas n'est pas vraiment très utile vu qu'il s'agit d'une seul opération
            DB::beginTransaction(); 
            DB::commit(); */

            $data = $request->all();

            $rules = [
                'name' => ["required", "unique:products"],
                'description' => ["present"],
                'category' => ["present"],
                'price' => ["required", "decimal:0"],
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

            $product = $this->product->create($data);

            return new ProductResource($product);
        } catch (\Throwable $th) {
            return response()->json(["errors" => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {

            $data = $request->all();

            $rules = [
                'name' => ["required", "unique:products,name,".$product->id],
                'description' => ["present"],
                'category' => ["present"],
                'price' => ["required", "decimal:0"],
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

            $product->update($data);

            return new ProductResource($product);
        } catch (\Throwable $th) {
            return response()->json(["errors" => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Le produit a bien été supprimé'], 201);
    }
}
