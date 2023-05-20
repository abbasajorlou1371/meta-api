<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    protected Model $model;

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getByIds(array $ids): Collection
    {
        return $this->model->find($ids);
    }

    public function getOneById($id): Model
    {
        return $this->model->find($id);
    }
}
