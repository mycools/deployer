<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Variable;

/**
 * A trait for project relationships.
 */
trait ProjectRelations
{
    /**
     * Has many relationship to commands.
     *
     * @return Command
     */
    public function commands(): MorphMany
    {
        return $this->morphMany(Command::class, 'target')
                    ->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship to variables.
     *
     * @return Variable
     */
    public function variables(): MorphMany
    {
        return $this->morphMany(Variable::class, 'target');
    }

    /**
     * Has many relationship to shared files.
     *
     * @return SharedFile
     */
    public function sharedFiles(): MorphMany
    {
        return $this->morphMany(SharedFile::class, 'target');
    }

    /**
     * Has many relationship to config files.
     *
     * @return ConfigFile
     */
    public function configFiles(): MorphMany
    {
        return $this->morphMany(ConfigFile::class, 'target');
    }
}
