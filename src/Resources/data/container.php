<?php

return [
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
