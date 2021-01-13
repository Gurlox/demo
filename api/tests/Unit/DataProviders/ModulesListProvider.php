<?php

namespace App\Tests\Unit\DataProviders;

class ModulesListProvider
{
    public static function getModulesListSample(): array
    {
        return [
            'modules' => [
                [
                    'type' => 'boxes',
                    'name' => 'boxes_template_1',
                    'showInMenu' => true,
                    'labelInMenu' => 'label',
                    'slug' => 'slug',
                    'items' => [
                        [
                            'name' => 'title',
                            'data' => [
                                'text' => 'text',
                            ],
                            'items' => [
                                [
                                    'name' => 'some_name',
                                    'data' => [
                                        'text' => 'text',
                                    ],
                                    'items' => []
                                ]
                            ]
                        ],
                        [
                            'name' => 'description',
                            'data' => [
                                'text' => 'descriptionText',
                            ],
                            'items' => []
                        ]
                    ]
                ],
            ]
        ];
    }

    public static function getFirstModuleSample(): array
    {
        return self::getModulesListSample()['modules'][0];
    }

    public static function getFirstChildItemSample(): array
    {
        return self::getFirstItemSample()['items'][0];
    }

    public static function getFirstItemSample(): array
    {
        return self::getModulesListSample()['modules'][0]['items'][0];
    }
}
