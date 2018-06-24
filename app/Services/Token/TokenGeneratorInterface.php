<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Services\Token;

interface TokenGeneratorInterface
{
    /**
     * Generate a random string.
     *
     * @param  int    $length
     * @return string
     */
    public function generateRandom(int $length = 32): string;
}
