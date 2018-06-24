<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Database\Eloquent\Collection;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;

/**
 * The group repository.
 */
class EloquentGroupRepository extends EloquentRepository implements GroupRepositoryInterface
{
    /**
     * EloquentGroupRepository constructor.
     *
     * @param Group $model
     */
    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model
                    ->orderBy('order')
                    ->get();
    }
}
