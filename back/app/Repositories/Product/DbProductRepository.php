<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\DbRepository;

class DbProductRepository extends DbRepository implements ProductRepository
{

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function getAllProducts(array $relations = [])
    {
        $query = $this->model
            ->filterByKeyword(request('keyword'))
            ->filterByStatus(request('status'))
            ->filterByCategory(request('category'))
            ->with($relations)
            ->orderBy("id", "DESC");
        if (request("count")) {
            return $query->paginate(request("count"));
        }
        return $query->get();
    }

    public function create(array $data)
    {
        $data["code"] = substr(uniqid(), 0, 9);
        $data["internalReference"] = substr(uniqid("REF-"), 0, 9);
        $data["shellId"] = mt_rand(1, 1000000);
        $product = $this->model->create($data);
        return $product;
    }
}
