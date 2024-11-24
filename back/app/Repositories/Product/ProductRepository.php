<?php
namespace App\Repositories\Product;

use App\Repositories\Repository;

interface ProductRepository extends Repository{
    public function getAllProducts(array $relations = []);
    public function create(array $data);
}