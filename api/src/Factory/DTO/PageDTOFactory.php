<?php

namespace App\Factory\DTO;

use App\DTO\PageDTO;
use App\Entity\Page;

class PageDTOFactory
{
    public function create(Page $page): PageDTO
    {
        return new PageDTO($page);
    }
}
