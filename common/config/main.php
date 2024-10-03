<?php
return [
    'timeZone' => 'Europe/Moscow',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'timeZone' => 'UTC',
            'nullDisplay' => '',
            'thousandSeparator' => ' ',
            'decimalSeparator' => '.'
        ],
        'grid' => [
            'class' => 'common\components\CustomActionColumn',
        ],
    ],
    'name' => 'СЗНИИ ЛИС',
    'charset' => 'UTF-8',
    'language' => 'ru-RU',
];
