<?php

namespace App\Factory;

use App\DTO\ModuleDTO;
use App\Entity\Module;
use App\Entity\Page;

class ModuleFactory
{
    private ItemFactory $itemFactory;

    public function __construct(ItemFactory $itemFactory)
    {
        $this->itemFactory = $itemFactory;
    }

    public function createFromDTO(ModuleDTO $moduleDTO, Page $page): Module
    {
        $module = new Module($moduleDTO->getName(), $moduleDTO->getType(), $page);

        if ($moduleDTO->isShowInMenu()) {
            $module->setShowInMenu($moduleDTO->isShowInMenu());
        }
        if ($moduleDTO->getLabelInMenu()) {
            $module->setLabelInMenu($moduleDTO->getLabelInMenu());
        }
        if ($moduleDTO->getSlug()) {
            $module->setSlug($moduleDTO->getSlug());
        }

        foreach ($moduleDTO->getItems() as $item) {
            $module->addItem($this->itemFactory->createFromDTO($item, $module));
        }

        return $module;
    }
}
