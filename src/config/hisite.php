<?php
/**
 * Yii2 Pages Module
 *
 * @link      https://github.com/hiqdev/yii2-module-pages
 * @package   yii2-module-pages
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2016-2017, HiQDev (http://hiqdev.com/)
 */

return [
    'modules' => [
        'pages' => [
            'class' => \hiqdev\yii2\modules\pages\Module::class,
        ],
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                'pages/list' => 'pages/render/list',
                'pages/<page:.*>' => 'pages/render/index',
            ],
        ],
        'i18n' => [
            'translations' => [
                'hiqdev:pages' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hiqdev/com/messages',
                ],
            ],
        ],
    ],
];
