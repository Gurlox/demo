<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Page;
use App\Exception\FormValidationException;
use App\Exception\ItemDataNotFoundException;
use App\Exception\ItemNotFoundException;
use App\Exception\MoveModuleInvalidArgumentException;
use App\Service\ModuleService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModulesController extends AbstractFOSRestController
{
    private ModuleService $moduleService;

    private TranslatorInterface $translator;

    public function __construct(ModuleService $moduleService, TranslatorInterface $translator)
    {
        $this->moduleService = $moduleService;
        $this->translator = $translator;
    }

    /**
     * Add modules to page
     *
     * @SWG\Post(
     *     path="/pages/{page}/modules",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="list of modules",
     *          type="json",
     *          @SWG\Schema(
     *              type="object"
     *          )
     *     ),
     *     @SWG\Response(response=200, description="List of modules for page"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Security(name="Bearer")
     *
     * @IsGranted("manage", subject="page")
     * @Rest\Post("/pages/{page}/modules")
     */
    public function postModulesAction(Request $request, Page $page): JsonResponse
    {
        try {
            $this->moduleService->createFromDefaultConfiguration($request->request->all(), $page);

            return new JsonResponse(
                ['modules' => $this->moduleService->normalizeModulesForPage($page)],
                Response::HTTP_OK
            );
        } catch (FormValidationException $exception) {
            return new JsonResponse(['messages' => $exception->getErrorMessages()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Modify single module
     *
     * @SWG\Patch(
     *     path="/modules/{module}",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="single module body",
     *          type="json",
     *          @SWG\Schema(
     *              type="object"
     *          )
     *     ),
     *     @SWG\Response(response=200, description="Single module data"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Security(name="Bearer")
     *
     * @IsGranted("manage", subject="module")
     * @Rest\Patch("/modules/{module}")
     */
    public function patchModulesAction(Request $request, Module $module): JsonResponse
    {
        try {
            $this->moduleService->modify(json_decode($request->getContent(), true), $module);

            return new JsonResponse(
                ['module' => $this->moduleService->normalizeModule($module)],
                Response::HTTP_OK
            );
        } catch (FormValidationException $exception) {
            return new JsonResponse(['messages' => $exception->getErrorMessages()], Response::HTTP_BAD_REQUEST);
        } catch (ItemNotFoundException $exception) {
            return new JsonResponse(
                ['message' => $this->translator->trans('exception.item_not_found')],
                Response::HTTP_BAD_REQUEST
            );
        } catch (ItemDataNotFoundException $exception) {
            return new JsonResponse(
                ['message' => $this->translator->trans('exception.item_data_not_found')],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Get all modules for page
     *
     * @SWG\Get(
     *     path="/pages/{page}/modules",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Response(response=200, description="List"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Security(name="Bearer")
     *
     * @IsGranted("manage", subject="page")
     * @Rest\Get("/pages/{page}/modules")
     */
    public function getModulesAction(Page $page): JsonResponse
    {
        return new JsonResponse(
            ['modules' => $this->moduleService->normalizeModulesForPage($page)],
            Response::HTTP_OK
        );
    }

    /**
     * Delete single module
     *
     * @SWG\Delete(
     *     path="/modules/{module}",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Response(response=200, description="Success"),
     * )
     * @Security(name="Bearer")
     * @IsGranted("manage", subject="module")
     */
    public function deleteModulesAction(Module $module): JsonResponse
    {
        $this->moduleService->deleteModule($module);

        return new JsonResponse(['status' => 'success']);
    }


    /**
     * Move module up or down
     *
     * @SWG\Post(
     *     path="/modules/{module}/sort/{direction}",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Response(response=200, description="Ok"),
     *     @SWG\Response(response=400, description="Invalid argument for position")
     * )
     * @Security(name="Bearer")
     *
     * @Rest\Post("/modules/{module}/sort/{direction}")
     * @IsGranted("manage", subject="module")
     */
    public function postOrderChangeAction(Module $module, string $direction): JsonResponse
    {
        try {
            $this->moduleService->changeOrder($module, $direction);

            return new JsonResponse([], Response::HTTP_OK);
        } catch (MoveModuleInvalidArgumentException $exception) {
            return new JsonResponse(
                ['message' => $this->translator->trans('exception.move_module.out_of_range')],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(
                ['message' => $this->translator->trans('exception.move_module.invalid_argument')],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
