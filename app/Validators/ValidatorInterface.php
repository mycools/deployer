<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Validators;

interface ValidatorInterface
{
    /**
     * @param  array $args
     * @return bool
     */
    public function validate(...$args);
}
