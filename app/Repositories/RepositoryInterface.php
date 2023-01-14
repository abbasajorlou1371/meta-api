<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface {
    public function getAll(): Collection;
    public function getByIds(array $ids): Collection;
    public function getOneById($id): Model;
}
