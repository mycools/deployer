<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TemplateRepositoryInterface
{
    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function getById(int $model_id): Model;

    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param array $fields
     *
     * @return Model
     */
    public function create(array $fields): Model;

    /**
     * @param array $fields
     * @param int   $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function updateById(array $fields, int $model_id): Model;

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById(int $model_id);
}
