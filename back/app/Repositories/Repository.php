<?php
namespace App\Repositories;

interface Repository{
    public function create(array $data);
    public function getAll(array $relations = []);
}