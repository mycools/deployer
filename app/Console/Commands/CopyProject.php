<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Command as AppCommand;
use Illuminate\Support\Arr;
class CopyProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:copy {project} {target} {--namespace=} {--url=}';

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
        $srcProject = $this->argument('project');
        $destProject = $this->argument('target');
        $namespace = $this->option('namespace');
        $url = $this->option('url');
        $srcProjectRes = Project::where("name",$srcProject)->firstOrFail();
        $destProject = $this->dstName($destProject);
        $this->line("Copy project {$srcProject} to {$destProject}");
        $arrOri = $srcProjectRes->toArray();
        $private_key = $srcProjectRes->private_key;
        $arr = $arrOri;
        $users = $srcProjectRes->users->toArray();
        $servers  = $srcProjectRes->servers->toArray();
        unset($arr['last_run']);
        unset($arr['last_mirrored']);
        unset($arr['webhook_url']);
        foreach($servers as &$u){
            if($arr['url'] && $url){
                $domain = parse_url($arr['url'],PHP_URL_HOST);
                $newdomain = parse_url($url,PHP_URL_HOST);
                $dx = strpos($u['path'],$domain);
                $dn = $dx+strlen($domain);
                $newpath=mb_substr($u['path'],0,$dx).$newdomain.mb_substr($u['path'],$dn,strlen($u['path'])-1);
                $u['path'] = $newpath;
            }
            unset($u['id']);
            unset($u['project_id']);
            unset($u['order']);
            unset($u['status']);
            unset($u['connect_log']);
            $u['name'] = mb_substr($u['name'],0,strpos($u['name'],'['));
            $u['name'] .= '['.$destProject.']';
        }
        if($url){
            $arr['url'] = $url;
        }
        $arr['name'] = $destProject;
        $arr['namespace'] = $namespace?$namespace:strtolower($destProject);
        $arr['private_key'] = $private_key;
        $SharedFile = SharedFile::where([
            'target_type' => 'project',
            'target_id' => $arrOri['id'],
        ])->get()->toArray();
       
        $ConfigFile = ConfigFile::where([
            'target_type' => 'project',
            'target_id' => $arrOri['id'],
        ])->get()->toArray();
        $Command = AppCommand::where([
            'target_type' => 'project',
            'target_id' => $arrOri['id'],
        ])->get()->toArray();
        //dd($Command);
        $newproject = Project::create($arr);
        
        foreach($servers as $s){
            $this->line("Copy server ".$s['name']);
            $newproject->servers()->save(new Server($s));
        }

        foreach($users as $u){
            $this->line("Copy user ".$u['name']);
            $newproject->users()->attach($u['id'],['role' => $u['pivot']['role']]);
        }

        foreach($SharedFile as $sf){
            $sf =Arr::except($sf,['id','target_id']);
            $sf['target_id'] =  $newproject->id;
            SharedFile::create($sf);
        }
        foreach($ConfigFile as $sf){
            $sf =Arr::except($sf,['id','target_id']);
            $sf['target_id'] =  $newproject->id;
            ConfigFile::create($sf);
        }
        foreach($Command as $sf){
            $this->line("Copy Command ".$sf['name']);
            $sf =Arr::except($sf,['id','target_id']);
            $sf['target_id'] =  $newproject->id;
            $cmd = AppCommand::create($sf);
            foreach($newproject->servers()->get() as $s){
                $this->line("Attach Command ".$sf['name'].' to server '.$s['name']);
                $cmd->servers()->attach($s->id);
            }
        }
    }
    public function dstName($name)
    {
        $destProjectRes = Project::where("name",$name);
        if($destProjectRes->count() > 0){
           $name .= "_copy";
           return $this->dstName($name);
        }
        return $name;
    }

}
