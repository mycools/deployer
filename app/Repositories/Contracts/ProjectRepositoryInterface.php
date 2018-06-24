<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProjectRepositoryInterface
{
    /**
     * @param string $hash
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function getByHash(string $hash): Model;

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

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function refreshBranches(int $model_id);

    /**
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk(int $count, callable $callback);

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated);

    /**
     * @param Carbon   $last_mirrored_since
     * @param int      $count
     * @param callable $callback
     *
     * @return Collection
     */
    public function getLastMirroredBefore(Carbon $last_mirrored_since, int $count, callable $callback): Collection;
}
