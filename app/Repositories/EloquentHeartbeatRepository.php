<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;

/**
 * The heartbeat repository.
 */
class EloquentHeartbeatRepository extends EloquentRepository implements HeartbeatRepositoryInterface
{
    /**
     * EloquentHeartbeatRepository constructor.
     *
     * @param Heartbeat $model
     */
    public function __construct(Heartbeat $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $hash
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Model
     */
    public function getByHash(string $hash): Model
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }
}
