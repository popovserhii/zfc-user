<?php
namespace Agere\Spare;

return [
    /*'default' => [
        'assets' => [
            //'@product_css',
            '@product_js',
        ],
        'options' => [
            'mixin' => true,
        ],
    ],*/

    /*'controllers' => [
        'spare' => [
            '@product_css',
            '@product_js',
        ],
        'shop-spare' => [
            //'@product_css',
            '@shop_product_js',
        ],
    ],*/


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