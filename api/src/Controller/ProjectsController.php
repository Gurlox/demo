<?php

namespace App\Controller;

use App\Exception\FormValidationException;
use App\Service\ProjectService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class ProjectsController extends AbstractFOSRestController
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * New project
     *
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="data",
     *          type="json",
     *          @SWG\Schema(
     *               type="object",
     *               @SWG\Property(
     *                  property="name",
     *                  type="string",
     *              )
     *          )
     *     ),
     *     @SWG\Response(response=200, description="Create empty project"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Security(name="Bearer")
     */
    public function postProjectsAction(Request $request): JsonResponse
    {
        try {
            $projectDTO = $this->projectService->create($request->request->all());

            return new JsonResponse(['project' => $projectDTO], Response::HTTP_OK);
        } catch (FormValidationException $exception) {
            return new JsonResponse(['messages' => $exception->getErrorMessages()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List of user project
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get all projects of currently logged user"
     * )
     * @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token")
     * @SWG\Tag(name="Project")
     * @Security(name="Bearer")
     */
    public function getProjectsAction(): JsonResponse
    {
        return new JsonResponse(['projects' => $this->projectService->getLoggedUserProjects()]);
    }
}
