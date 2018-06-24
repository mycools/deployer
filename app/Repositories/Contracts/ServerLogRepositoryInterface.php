<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ServerLogRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return Model
     */
    public function create(array $fields): Model;

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function getById(int $model_id): Model;

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated);
}
