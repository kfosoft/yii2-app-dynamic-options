# Yii2 Application Dynamic Options
## Installation

Installation with Composer

Either run
~~~
    php composer.phar require kfosoft/yii2-app-dynamic-options:"*"
~~~
or add in composer.json
~~~
    "require": {
        ...
        "kfosoft/yii2-app-dynamic-options":"*"
    }
~~~

Copy migration and up
~~~
    vendor\kfosoft\yii2-app-dynamic-options\system\migrations\m000000_000001_init_options.php
~~~
or create your migration and configure in config
~~~
    'yii2options'  => [ /** @todo Option component must have name 'yii2options'! */
            'class'        => '\kfosoft\yii2\system\Option', /* Component class */
            'tableName'    => 'Option', /* Optional. By default: 'option'. Table name. */
            'modelClass'   => '\kfosoft\yii2\system\models\Option', /* Optional. By default: '\kfosoft\yii2\system\models\Option'. Model class. */
            'modelSearchClass' => '\kfosoft\yii2\system\models\OptionSearch', /* Optional. By default: '\kfosoft\yii2\system\models\OptionSearch'. Search model class. */
            'tableKeyField' => 'key', /* Optional. By default: 'key'. Table key field. */
            'tableValueField' => 'value', /* Optional. By default: 'value'. Table value field. */
            'cacheKey', => 'yii2options', /* Optional. By default: 'yii2options'. Cache key. */
            'manageAction' => '/admin/options/manage',
            'updateAction' => '/admin/options/update',
            'manageView' => '@yii2options/views/manageOptions', /* Optional. By default: '@yii2options/views/manageOptions'. Manage view path. */
            'updateView' => '@yii2options/views/updateOptions', /* Optional. By default: '@yii2options/views/updateOptions'. Update view path. */
            'translations' => [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath'       => '@yii2options/messages',
                'fileMap'        => [],
            ], /* Optional. By default: this array. I18n params. */
            'connectionName' => 'db', /* Optional. By default: 'db'. Database component name. */
    ],
~~~
and add in bootstrap
~~~
    'bootstrap'      => ['log', 'urlManager', 'yii2options'],
~~~

This extension has two commands
~~~
    - options                         Options Command Controller.
        options/clear                 Clear db params.
        options/push                  Push original params.
~~~

Well done!

## Example 
config/params.php
~~~
<?php

return [
    'admin.email' => [
        'value' => 'example@gmail.com', /* Param value. */
        'comments' => Yii::t('app', 'Comment 1'), /* Comment for this param. */
        'edit' => true, /* Can you edit this param? */
        'validator' => 'string' /* Validator. 'string', 'integer', callable. */
    ],
    'param.array' => [
        'value' => [5, 10, 15, 20, 25],
        'comments' => Yii::t('app', 'Comment 2'),
        'edit' => true,
        'validator' => function ($model, $value, $key) { ... }, /* Callable validator. */
        'getValue' => function ($value) { ... }, /* Work for grid view value & active form value. */
        'setValue' => function ($value) { ... }, /* Work for active form value. */
    ],
    'param.2' => [
        'value' => 5,
        'comments' => Yii::t('app', ''),
        'edit' => true,
        'validator' => 'integer',
        'afterFind' => function ($model, $event) { ... }, /* Model event. @var string $event */
        'beforeValidate' => function ($model, $event) { ... }, /* Model event. @var string $event */ 
        'afterValidate' => function ($model, $event) { ... }, /* Model event. @var string $event */
        'beforeSave' => function ($model, $event, $insert) { ... }, /* Model event. @var string $event */
        'afterSave' => function ($model, $event, $insert, $changedAttributes) { ... }, /* Model event. @var string $event */
    ],
    'param.3' => 'dd/MM/yyyy', /* this line = ['value' => 'dd/MM/yyyy', 'comments' => 'N/A', 'edit' => false, 'validator' => 'string'] */
];
~~~

Enjoy, guys!
