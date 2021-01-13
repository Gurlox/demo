<?php

namespace App\Controller;

use App\Entity\Item;
use App\Service\ItemService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;

class ItemsController extends AbstractFOSRestController
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Edit single item data
     *
     * @SWG\Patch(
     *     path="/items/{item}",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Project"},
     *     @SWG\Parameter(name="Authorization", in="header", type="string", description="Authentication token"),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="item data",
     *          type="json",
     *          @SWG\Schema(
     *              type="object"
     *          )
     *     ),
     *     @SWG\Response(response=200, description="Item data"),
     *     @SWG\Response(response=400, description="Wrong value")
     * )
     * @Security(name="Bearer")
     * @IsGranted("manage", subject="item")
     */
    public function patchItemsAction(Request $request, Item $item): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $item = $this->itemService->edit($data, $item);

        return new JsonResponse(['item' => $this->itemService->getItemData($item)]);
    }
}
