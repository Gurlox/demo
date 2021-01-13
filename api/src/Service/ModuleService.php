<?php

namespace App\Service;

use App\DTO\ModuleDTO;
use App\DTO\ModulesCollectionDTO;
use App\Entity\Module;
use App\Entity\Page;
use App\Exception\FormValidationException;
use App\Exception\ItemDataNotFoundException;
use App\Exception\ItemNotFoundException;
use App\Exception\MoveModuleInvalidArgumentException;
use App\Factory\ModuleFactory;
use App\Form\ModulesType;
use App\Form\ModuleType;
use App\Utils\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ModuleService
{
    const SORT_UP = 'up';
    const SORT_DOWN = 'down';

    private FormFactoryInterface $formFactory;

    private ModuleFactory $moduleFactory;

    private EntityManagerInterface $em;

    private ItemService $itemService;

    public function __construct(
        FormFactoryInterface $formFactory,
        ModuleFactory $moduleFactory,
        EntityManagerInterface $em,
        ItemService $itemService
    ) {
        $this->formFactory = $formFactory;
        $this->moduleFactory = $moduleFactory;
        $this->em = $em;
        $this->itemService = $itemService;
    }

    /**
     * @throws FormValidationException
     */
    public function createFromDefaultConfiguration(array $payload, Page $page): ModulesCollectionDTO
    {
        $modulesCollectionDTO = new ModulesCollectionDTO();
        $form = $this->formFactory->create(ModulesType::class, $modulesCollectionDTO);
        $form->submit($payload);

        if ($form->isValid()) {
            foreach ($modulesCollectionDTO->modules as $moduleDTO) {
                $module = $this->moduleFactory->createFromDTO($moduleDTO, $page);
                $page->addModule($module);
                $this->em->persist($module);
            }
            $this->em->flush();

            return $modulesCollectionDTO;
        } else {
            throw new FormValidationException(FormErrors::getAll($form));
        }
    }

    /**
     * @throws ItemNotFoundException
     * @throws FormValidationException
     * @throws ItemDataNotFoundException
     */
    public function modify(array $payload, Module $module): void
    {
        $moduleDTO = new ModuleDTO();
        $form = $this->formFactory->create(ModuleType::class, $moduleDTO);
        $form->submit($payload);

        if ($form->isValid()) {
            if (!is_null($moduleDTO->isShowInMenu())) {
                $module->setShowInMenu($moduleDTO->isShowInMenu());
            }
            if (!is_null($moduleDTO->getLabelInMenu())) {
                $module->setLabelInMenu($moduleDTO->getLabelInMenu());
            }
            if (!is_null($moduleDTO->getSlug())) {
                $module->setSlug($moduleDTO->getSlug());
            }

            $this->itemService->updateItemsFromDTO($moduleDTO->getItems(), $module->getItems());

            $this->em->flush();
        } else {
            throw new FormValidationException(FormErrors::getAll($form));
        }
    }

    public function normalizeModulesForPage(Page $page): array
    {
        $modules = [];

        foreach ($page->getModules() as $module) {
            $modules[] = $this->normalizeModule($module);
        }

        return $modules;
    }

    public function normalizeModule(Module $module): array
    {
        return [
            'id' => $module->getId(),
            'name' => $module->getName(),
            'type' => $module->getType(),
            'showInMenu' => $module->getShowInMenu(),
            'slug' => $module->getSlug(),
            'labelInMenu' => $module->getLabelInMenu(),
            'items' => $this->itemService->getItemsList($module->getItems(), true),
        ];
    }

    public function deleteModule(Module $module): void
    {
        $this->em->remove($module);
        $this->em->flush();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws MoveModuleInvalidArgumentException
     */
    public function changeOrder(Module $module, string $action): void
    {
        if ($action === self::SORT_UP) {
            $module->moveUp();
        } elseif ($action === self::SORT_DOWN) {
            $module->moveDown();
        } else {
            throw new \InvalidArgumentException();
        }

        $this->em->flush();
    }
}
