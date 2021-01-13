<?php

namespace App\DTO;

use App\Entity\Page;

class PageDTO
{
    public ?int $id;
    public bool $isDefault;

    public function __construct(Page $page)
    {
        $this->id = $page->getId();
        $this->isDefault = $page->getIsDefault();
    }
}
