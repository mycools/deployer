<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Services\Update;

interface LatestReleaseInterface
{
    /**
     * @return false|string
     */
    public function latest();

    /**
     * @return bool
     */
    public function isUpToDate(): bool;
}
