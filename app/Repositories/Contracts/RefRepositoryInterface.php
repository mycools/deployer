<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface RefRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);
}
