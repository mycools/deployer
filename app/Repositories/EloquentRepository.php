<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract class for eloquent repositories.
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class EloquentRepository
{
    /**
     * An instance of the model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Get's all records from the model.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get's an item from the repository.
     *
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function getById(int $model_id): Model
    {
        return $this->model->findOrFail($model_id);
    }

    /**
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return Model
     */
    public function create(array $fields): Model
    {
        return $this->model->create($fields);
    }

    /**
     * Updates an instance by it's ID.
     *
     * @param array $fields
     * @param int   $model_id
     *
     * @return Model
     */
    public function updateById(array $fields, int $model_id): Model
    {
        $model = $this->getById($model_id);

        $model->update($fields);

        return $model;
    }

    /**
     * Delete an instance by it's ID.
     *
     * @param int $model_id
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteById(int $model_id)
    {
        $model = $this->getById($model_id);

        return $model->delete();
    }

    /**
     * Chunk the results of the query.
     *
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk(int $count, callable $callback): bool
    {
        return $this->model->chunk($count, $callback);
    }

    /**
     * Runs where query in chunk.
     *
     * @param string   $field
     * @param array    $values
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunkWhereIn(string $field, array $values, int $count, callable $callback)
    {
        // FIXME: This is only used in the CheckUrl repository so move it
        return $this->model->whereIn($field, $values, 'and', false)
                           ->chunk($count, $callback);
    }

    /**
     * Updates all instances with a specific status.
     *
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated): bool
    {
        return $this->model->where('status', '=', $original)
                           ->update(['status' => $updated]);
    }
}
