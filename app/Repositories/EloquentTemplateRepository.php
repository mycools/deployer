<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Template;
use Illuminate\Database\Eloquent\Collection;

/**
 * The template repository.
 */
class EloquentTemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    /**
     * EloquentTemplateRepository constructor.
     *
     * @param Template $model
     */
    public function __construct(Template $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model
                    ->orderBy('name')
                    ->get();
    }
}
