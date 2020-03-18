<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Services\Webhooks\Beanstalkapp;
use REBELinBLUE\Deployer\Services\Webhooks\Bitbucket;
use REBELinBLUE\Deployer\Services\Webhooks\Custom as CustomWebhook;
use REBELinBLUE\Deployer\Services\Webhooks\Github;
use REBELinBLUE\Deployer\Services\Webhooks\Gitlab;
use REBELinBLUE\Deployer\Services\Webhooks\Gogs;
use REBELinBLUE\Deployer\Services\Webhooks\Webhook;
use Symfony\Component\HttpFoundation\Response;

/**
 * The deployment webhook controller.
 */
class WebhookAutoController extends Controller
{
    /**
     * List of supported service classes.
     *
     * @var array
     */
    private $services = [
        Gogs::class,
        Github::class,
        Beanstalkapp::class,
        Bitbucket::class,
        Gitlab::class,
    ];

    /**
     * The project repository.
     *
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * The deployment repository.
     *
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * WebhookController constructor.
     *
     * @param ProjectRepositoryInterface    $projectRepository
     * @param DeploymentRepositoryInterface $deploymentRepository
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        DeploymentRepositoryInterface $deploymentRepository
    ) {
        $this->projectRepository    = $projectRepository;
        $this->deploymentRepository = $deploymentRepository;
    }

    /**
     * Handles incoming requests to trigger deploy.
     *
     * @param Request         $request
     * @param ResponseFactory $response
     * @param string          $hash     The webhook hash
     *
     * @return \Illuminate\View\View
     */
    public function autodeploy(Request $request, ResponseFactory $response)
    {
	    $payload = $this->parseAutoWebhookRequest($request);
	    
	    $projects = $this->projectRepository->getByPayload($payload);
        if(!$projects->count()){
           return response()->json([]); 
        }  
	    foreach($projects as $project){
		    if ($project->servers->where('deploy_code', true)->count() > 0) {
			    $payload = $this->parseWebhookRequest($request, $project);
			    if (is_array($payload)) {
	                $this->deploymentRepository->abortQueued($project->id);
	                $deployment = $this->deploymentRepository->create($payload);
	            }
			}
	    }
	    if($project->count() > 0){
		     return $response->json([
	                    'success'       => true,
	                    'project_ids' => $projects->pluck("id"),
	                ], Response::HTTP_CREATED);
	    }


        // FIXME: This should not be HTTP_OK really
        return $response->json(['success' => false], Response::HTTP_OK);
    }

    

    /**
     * Goes through the various webhook integrations as checks if the request is for them and parses it.
     * Then adds the various additional details required to trigger a deployment.
     *
     * @param Request $request
     * @param Project $project
     *
     * @return array Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function parseAutoWebhookRequest(Request $request)
    {
        $integration = new CustomWebhook($request);

        foreach ($this->services as $service) {
            /** @var Webhook $integration */
            $service = new $service($request);

            if ($service->isRequestOrigin()) {
                $integration = $service;
                break;
            }
        }

        return $this->appendAutoProjectSettings($integration->handlePush(), $request);
    }

    /**
     * Takes the data returned from the webhook request and then adds deployers own data, such as project ID
     * and runs any checks such as checks the branch is allowed to be deployed.
     *
     * @param array   $payload
     * @param Request $request
     * @param Project $project
     *
     * @return array|false Either an array of the complete deployment config, or false if it is invalid.
     */
    private function appendAutoProjectSettings($payload, Request $request)
    {
	   
        // If the payload is empty return false
        if (!is_array($payload) || !count($payload)) {
            return false;
        }

       
        


        $payload['optional'] = [
	        'project' => $request->get('project')
        ];

        // Check if the commands input is set, if so explode on comma and filter out any invalid commands
        if ($request->has('commands')) {
            $valid     = $project->commands->pluck('id');
            $requested = explode(',', $request->get('commands'));

            $collection = new Collection($requested);

            $payload['optional'] = $collection->unique()->intersect($valid)->toArray();
        }

        

        return $payload;
    }
    
    
    /**
     * Goes through the various webhook integrations as checks if the request is for them and parses it.
     * Then adds the various additional details required to trigger a deployment.
     *
     * @param Request $request
     * @param Project $project
     *
     * @return array Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function parseWebhookRequest(Request $request, Project $project)
    {
        $integration = new CustomWebhook($request);

        foreach ($this->services as $service) {
            /** @var Webhook $integration */
            $service = new $service($request);

            if ($service->isRequestOrigin()) {
                $integration = $service;
                break;
            }
        }

        return $this->appendProjectSettings($integration->handlePush(), $request, $project);
    }

    /**
     * Takes the data returned from the webhook request and then adds deployers own data, such as project ID
     * and runs any checks such as checks the branch is allowed to be deployed.
     *
     * @param array   $payload
     * @param Request $request
     * @param Project $project
     *
     * @return array|false Either an array of the complete deployment config, or false if it is invalid.
     */
    private function appendProjectSettings($payload, Request $request, Project $project)
    {
        // If the payload is empty return false
        if (!is_array($payload) || !count($payload)) {
            return false;
        }

        $payload['project_id'] = $project->id;

        // If there is no branch set get it from the project
        if (is_null($payload['branch']) || empty($payload['branch'])) {
            $payload['branch'] = $project->branch;
        }

        // If the project doesn't allow other branches check the requested branch is the correct one
        if (!$project->allow_other_branch && $payload['branch'] !== $project->branch) {
            return false;
        }

        $payload['optional'] = [];

        // Check if the commands input is set, if so explode on comma and filter out any invalid commands
        if ($request->has('commands')) {
            $valid     = $project->commands->pluck('id');
            $requested = explode(',', $request->get('commands'));

            $collection = new Collection($requested);

            $payload['optional'] = $collection->unique()->intersect($valid)->toArray();
        }

        // If the webhook is allowed to deploy other branches check if the request has an update_only
        // query string and if so check the branch matches that which is currently deployed
        if ($project->allow_other_branch && $request->has('update_only') && $request->get('update_only') !== false) {
            $deployment = $this->deploymentRepository->getLatestSuccessful($project->id);

            if (!$deployment || $deployment->branch !== $payload['branch']) {
                return false;
            }
        }

        return $payload;
    }
}
