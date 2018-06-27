<?php

namespace REBELinBLUE\Deployer\Console\Commands\Installer;

/**
 * Wrapper class for PHP functions needed during install so that they can be mocked.
 */
class PhpChecker
{
    /**
     * @param string $version
     *
     * @return bool
     */
    public function minimumVersion(string $version): bool
    {
        return \version_compare(PHP_VERSION, $version, '>=');
    }

    /**
     * @param string $extension
     *
     * @return bool
     */
    public function hasExtension(string $extension): bool
    {
        return \extension_loaded($extension);
    }

    /**
     * @param string $function
     *
     * @return bool
     */
    public function functionExists(string $function): bool
    {
        return \function_exists($function);
    }
}
