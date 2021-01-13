<?php

namespace App\Tests\Unit\DataProviders;

use App\DataFixtures\ItemFixtures;

class ModuleProvider
{
    public static function getModuleSample(): array
    {
        return [
            'showInMenu' => true,
            'labelInMenu' => 'label',
            'slug' => 'slug',
            'items' => [
                [
                    'id' => ItemFixtures::ITEM_1['id'],
                    'data' => [
                        'text' => 'text',
                    ],
                    'items' => [
                        [
                            'id' => ItemFixtures::ITEM_1_CHILD['id'],
                            'data' => [
                                'text' => 'text',
                            ],
                            'items' => []
                        ]
                    ]
                ],
            ]
        ];
    }

    public static function getFirstItemSample(): array
    {
        return self::getModuleSample()['items'][0];
    }

    public static function getFirstChildItemSample(): array
    {
        return self::getFirstItemSample()['items'][0];
    }
}
