<?php
namespace App\Repositories;

abstract class DbRepository{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
    
    public function getAll(array $relations = [])
    {
        return $this->model->with($relations)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function first(array $relations = [])
    {
        return $this->model->with($relations)->first();
    }
}