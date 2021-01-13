<?php

namespace App\Controller;

use App\Service\Security\TokenService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Swagger\Annotations as SWG;

class SecurityController extends AbstractController
{
    private TranslatorInterface $translator;

    private TokenService $tokenService;

    public function __construct(TokenService $tokenService, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->tokenService = $tokenService;
    }

    /**
     * Login
     *
     * @SWG\Response(
     *     response=200,
     *     description="Login"
     * )
     * @SWG\Parameter(name="email", in="formData", type="string", description="Email")
     * @SWG\Parameter(name="password", in="formData", type="string", description="Password")
     * @SWG\Tag(name="Authentication")

     */
    public function loginAction()
    {
        throw new \Exception("This should not be reached");
    }

    /**
     * Refresh token
     *
     * @SWG\Response(
     *     response=200,
     *     description="Refresh expired token"
     * )
     * @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token")
     * @SWG\Tag(name="Authentication")
     * @Security(name="Bearer")
     */
    public function refreshTokenAction(Request $request): JsonResponse
    {
        try {
            $token = $this->tokenService->extractToken($request);
            if ($token) {
                $result = $this->tokenService->refreshToken($token);
                if ($result) {
                    return new JsonResponse(['token' => $result]);
                }
            }

            return new JsonResponse(
                ['message' => $this->translator->trans('exception.token.format')],
                Response::HTTP_UNAUTHORIZED
            );
        } catch (JWTEncodeFailureException $exception) {
            return new JsonResponse([
                'message' => $this->translator->trans('exception.default'),
                'exception' => get_class($exception)
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
