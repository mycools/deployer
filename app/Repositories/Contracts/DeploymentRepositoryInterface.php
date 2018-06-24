<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Deployment;

interface DeploymentRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields): Model;

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById(int $model_id): Model;

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function abort(int $model_id);

    /**
     * @param int $project_id
     */
    public function abortQueued(int $project_id);

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated): bool;

    /**
     * @param int    $model_id
     * @param string $reason
     * @param array  $optional
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function rollback(int $model_id, string $reason = '', array $optional = []): Model;

    /**
     * @param int $project_id
     * @param int $paginate
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatest(int $project_id, int $paginate = 15): Collection;

    /**
     * @param int $project_id
     *
     * @return Deployment
     */
    public function getLatestSuccessful(int $project_id): Model;

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getTodayCount(int $project_id): int;

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getLastWeekCount(int $project_id): int;

    /**
     * @return Collection
     */
    public function getTimeline(): Collection;

    /**
     * @return Collection
     */
    public function getPending(): Collection;

    /**
     * @return Collection
     */
    public function getRunning(): Collection;
}
