<?php

return [
    [
        'translation' => 'false',
        'name' => 'status',
        'element' => 'radio',
        'parent_class' => 'col-lg-6',
        'labels' => [
            '164' => 'Durum',
            '40' => 'Status',
        ],
        'rules' => [
            'required'
        ],
        'options' => [
            [
                'key' => 1,
                'value' => [
                    '164' => 'Aktif',
                    '40' => 'Active',
                ],
            ],
            [
                'key' => 2,
                'value' => [
                    '164' => 'Taslak',
                    '40' => 'Draft',
                ]
            ],
            [
                'key' => 0,
                'value' => [
                    '164' => 'Pasif',
                    '40' => 'Passive',
                ]
            ],
        ]
    ],
    [
        'translation' => 'false',
        'name' => 'order',
        'element' => 'input',
        'type' => 'number',
        'parent_class' => 'col-lg-6',
        'labels' => [
            '164' => 'Sıra',
            '40' => 'Order',
        ],
        'rules' => [
            'required'
        ],
    ],
    [
        'translation' => 'true',
        'name' => 'name',
        'element' => 'input',
        'type' => 'text',
        'parent_class' => 'col-lg-6',
        'labels' => [
            '164' => 'Sayfa Adı',
            '40' => 'Page Name',
        ],
        'rules' => [
            'required_if:languages.*,1'
        ]
    ],
    [
        'translation' => 'true',
        'name' => 'slug',
        'element' => 'slug',
        'parent_class' => 'col-lg-6',
        'labels' => [
            '164' => 'Slug TR',
            '40' => 'Slug EN',
        ],
        'rules' => [
            'required_if:languages.*,1'
        ]
    ],
    [
        'translation' => 'true',
        'name' => 'content',
        'element' => 'textarea',
        'type' => 'ckeditor',
        'parent_class' => 'col-lg-12',
        'class' => '',
        'labels' => [
            '164' => 'Detay',
            '40' => 'Detail',
        ]
    ],
];
