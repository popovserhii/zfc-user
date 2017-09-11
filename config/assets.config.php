<?php
namespace Agere\Spare;

return [
    'modules' => [
        __NAMESPACE__ => [
            'root_path' => __DIR__ . '/../view/assets',
            'collections' => [
                'core_images' => [
                    'assets' => [
                        'images/*.png',
                        'images/*.jpg',
                        'images/*.gif',
                    ],
                    'options' => [
                        'move_raw' => true,
                    ]
                ],
            ],
        ],
    ],
];