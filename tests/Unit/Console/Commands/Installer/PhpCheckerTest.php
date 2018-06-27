<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands\Installer;

use REBELinBLUE\Deployer\Console\Commands\Installer\PhpChecker;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\Installer\PhpChecker
 */
class PhpCheckerTest extends TestCase
{
    private $phpchecker;

    public function setUp()
    {
        parent::setUp();

        $this->phpchecker = new PhpChecker();
    }

    /**
     * @covers ::minimumVersion
     */
    public function testMinimumVersion()
    {
        $this->assertTrue($this->phpchecker->minimumVersion('6.0.0'));
        $this->assertFalse($this->phpchecker->minimumVersion('10.0.0'));
    }

    /**
     * @covers ::hasExtension
     */
    public function testHasExtension()
    {
        $this->assertTrue($this->phpchecker->hasExtension('Core'));
        $this->assertFalse($this->phpchecker->hasExtension('some-fake-unknown-extension'));
    }

    /**
     * @covers ::functionExists
     */
    public function testFunctionExists()
    {
        $this->assertTrue($this->phpchecker->functionExists('phpinfo'));
        $this->assertFalse($this->phpchecker->functionExists('someFakeUnknownFunction'));
    }
}
