<?php
namespace kfosoft\yii2\system;

use \Yii;
use \yii\base\BootstrapInterface;
use \yii\base\Component;
use \yii\base\Application;
use \yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Application dynamic options.
 * @package kfosoft\yii2\system
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class Option extends Component implements BootstrapInterface
{
    /** @var array system original params. */
    protected $originalParams;

    /** @var array params after parse original params. */
    protected $options;

    /** @var array cache system params. */
    protected $cache;

    /** @var \yii\db\ActiveRecord[] params records. */
    protected $models;

    /** @var array result params. */
    protected $params;

    /** @var string table name. */
    public $tableName = 'option';

    /** @var string system params model class. */
    public $modelClass = '\kfosoft\yii2\system\models\Option';

    /** @var string system params model search class. */
    public $modelSearchClass = '\kfosoft\yii2\system\models\OptionSearch';

    /** @var string system params table key field name. */
    public $tableKeyField = 'key';

    /** @var string system params table value field name. */
    public $tableValueField = 'value';

    /** @var string change key. */
    public $cacheKey = 'yii2options';

    /** @var string manage action, for example /admin/options/manage */
    public $manageAction = '';

    /** @var string update action, for example /admin/options/update */
    public $updateAction = '';

    /** @var string manage view path. */
    public $manageView = '@yii2options/views/manageOptions';

    /** @var string update view path. */
    public $updateView = '@yii2options/views/updateOptions';

    public $translations = [
        'class' => 'yii\i18n\PhpMessageSource',
        'sourceLanguage' => 'en-US',
        'basePath' => '@yii2options/messages',
        'fileMap' => [],
    ];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@yii2options', __DIR__);
        if($app instanceof \yii\console\Application) {
            $app->controllerMap = ArrayHelper::merge($app->controllerMap, ['options' => '\kfosoft\yii2\system\commands\OptionsController']);
        }

        $this->originalParams = $app->params;
        $this->setOptions();
        if (!Yii::$app->cache->exists($this->cacheKey)) {
            $this->pull($app);
        } else {
            $this->options = $app->cache->get($this->cacheKey);
        }
        $this->parseParams();

        $app->params = $this->params;
    }

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['yii2options'] = $this->translations;
    }

    /**
     * Set original params.
     */
    protected function setOptions()
    {
        foreach ($this->originalParams as $key => $param) {
            if (!is_array($param)) {
                $param = [
                    'value'     => $param,
                    'comments'  => 'N/A',
                    'edit'      => false,
                    'validator' => 'string',
                ];
            }
            $this->setOption($key, $param);
        }
    }

    /**
     * Set option.
     * @param string $key param key.
     * @param array $param param options.
     */
    protected function setOption($key, array $param)
    {
        $this->options[$key] = $param;
    }

    /**
     * Get option.
     * @param string $key param key.
     * @return mixed
     */
    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * Push all options in sql table.
     */
    public function push()
    {
        $options = [];
        $models = [];

        /** @var \yii\db\BaseActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $this->models = $modelClass::findAll('');

        foreach ($this->models as $model) {
            $models[$model->key] = $model;
        }

        foreach ($this->options as $key => $option) {
            $options[$key] = $option;
            if (isset($models[$key])) {
                $options[$key]['value'] = $models[$key]->value;
            }
        }

        $this->options = $options;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->resetDb();

            foreach ($this->options as $key => $option) {
                /** @var \kfosoft\yii2\system\models\Option $model */
                $model = new $this->modelClass();
                $model->key = $key;
                $model->value = $option['value'];
                $model->save();
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        Yii::$app->cache->set($this->cacheKey, $this->options);
        return true;
    }

    /**
     * Pull all options from sql table.
     * @param Application $app current application.
     */
    protected function pull(Application $app)
    {
        /** @var \yii\db\BaseActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $this->models = $modelClass::findAll('');

        foreach ($this->models as $model) {
            $this->options[$model->{$this->tableKeyField}] = [
                'value' => $model->{$this->tableValueField},
            ];
        }
        $app->cache->set($this->cacheKey, $this->options);
    }

    /**
     * Result event.
     */
    protected function parseParams()
    {
        foreach ($this->options as $key => $option) {
            $this->params[$key] = $option['value'];
        }
    }

    /**
     * Reset option table.
     */
    public function resetDb()
    {
        Yii::$app->db->queryBuilder->truncateTable($this->tableName);
    }
}
