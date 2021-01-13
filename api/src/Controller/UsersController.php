<?php

namespace App\Controller;

use App\DTO\UserDTO;
use Swagger\Annotations as SWG;
use App\Exception\FormValidationException;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractFOSRestController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get authenticated user
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns currently logged user"
     * )
     * @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token")
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")

     * @Rest\Get("/auth/user")
     */
    public function getAuthenticatedUser()
    {
        return View::create(['user' => new UserDTO($this->getUser())]);
    }

    /**
     * Register new user
     *
     * @SWG\Post(
     *     path="/public/users",
     *     summary="Confirm",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"User"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="data",
     *          type="json",
     *          @SWG\Schema(
     *               type="object",
     *               @SWG\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="object",
     *                  @SWG\Property(property="first", type="string"),
     *                  @SWG\Property(property="second", type="string")
     *              ),
     *          )
     *     ),
     *     @SWG\Response(response=200, description="Success"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Rest\Post("/public/users")
     */
    public function postUsersAction(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->register($request->request->all());

            return new JsonResponse(['user' => $user], Response::HTTP_OK);
        } catch (FormValidationException $exception) {
            return new JsonResponse(['messages' => $exception->getErrorMessages()], Response::HTTP_BAD_REQUEST);
        }
    }
}
