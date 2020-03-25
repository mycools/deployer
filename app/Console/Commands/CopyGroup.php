<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory as Validation;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use RuntimeException;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Command as Commander;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\SharedFile;
class CopyGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:copy
    						{source : Project Source}
    						{name : Project Name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $name = $this->argument('name');
	    $source = $this->argument('source');
        $this->line("Copy project from {$source} to {$name}");
        $src = Group::where("name",$source);
        if($src->count() != 1){
	        return $this->warn("Project {$source} is not exists");
        }
        
        $src = $src->first();
        
        $group = Group::firstOrCreate([
	        'name' => $name
        ]);
        
        $project = Project::where('group_id',$group->id);
        $projectSrc = Project::where('group_id',$src->id);
        if($project->count() != 0){
	        return $this->warn("Group {$name} is not empty");
        }
        if($projectSrc->count() == 0){
	        return $this->warn("Group {$source} is empty");
        }
        
        foreach($projectSrc->get() as $proj){
	        //dd($proj);
	        $projname = $proj->name;
	        $projnameTo = str_replace($source, $name, $projname);
	        $command = Commander::where([
		        'target_type' => 'project',
		        'target_id' => $proj->id,
	        ])->orderBy('step','ASC')->get();
	        
	        $configfile = ConfigFile::where([
		        'target_type' => 'project',
		        'target_id' => $proj->id,
	        ])->get();
	         $SharedFile = SharedFile::where([
		        'target_type' => 'project',
		        'target_id' => $proj->id,
	        ])->get();
	        $this->line("Copy project {$projname} to {$projnameTo}");
	        $projNew = Project::create([
			    "name" => $projnameTo,
			    "repository" => $proj->repository,
			    "hash" => token(60),
			    "branch" => $proj->branch,
			    "private_key" => $proj->private_key,
			    "public_key" => $proj->public_key,
			    "group_id" => $group->id,
			    "builds_to_keep" => $proj->builds_to_keep,
			    "url" => $proj->url,
			    "build_url" => $proj->build_url,
			    "allow_other_branch" => $proj->allow_other_branch,
			    "include_dev" => $proj->include_dev,
			    "status" => $proj->status,
			    "is_mirroring" => $proj->is_mirroring,
			    "namespace" => strtolower($projnameTo),
			  ]);
			foreach($command as $comm){
				$c = $comm->only('name','user','script','order','optional','default_on','target_type','step');
				$c['target_id'] = $projNew->id;
				Commander::create($c);
			}

			foreach($configfile as $cnf){
				$c = $cnf->only('name','path','content','target_type');
				$c['target_id'] = $projNew->id;
				ConfigFile::create($c);
			}
			foreach($SharedFile as $cnf){
				$c = $cnf->only('name','file','target_type');
				$c['target_id'] = $projNew->id;
				SharedFile::create($c);
			}
        }
        
        
        
    }
}
