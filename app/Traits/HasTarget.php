<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A trait for target polymorphic relationship.
 */
trait HasTarget
{
    /**
     * One-to-one to polymorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
