<?php
namespace kfosoft\yii2\system;

use kfosoft\yii2\system\commands\OptionsController;
use kfosoft\yii2\system\models\OptionSearch;
use Throwable;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\console\Application;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\helpers\ArrayHelper;

/**
 * Application dynamic options.
 *
 * @package kfosoft\yii2\system
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class Option extends Component implements BootstrapInterface
{
    public const COMPONENT_NAME = 'yii2options';

    /**
     * @var array system original params
     */
    protected $originalParams;

    /**
     * @var array params after parse original params
     */
    protected $options;

    /**
     * @var array cache system params
     */
    protected $cache;

    /**
     * @var ActiveRecord[] params records
     */
    protected $models;

    /**
     * @var array result params
     */
    protected $params;

    /**
     * @var array cache options
     */
    protected $cacheOptions = [];

    /**
     * @var string table name
     */
    public $tableName = 'option';

    /**
     * @var string system params model class
     */
    public $modelClass = models\Option::class;

    /**
     * @var string system params model search class
     */
    public $modelSearchClass = OptionSearch::class;

    /**
     * @var string system params table key field name
     */
    public $tableKeyField = 'key';

    /**
     * @var string system params table value field name
     */
    public $tableValueField = 'value';

    /**
     * @var string change key
     */
    public $cacheKey = 'yii2options';

    /**
     * @var string manage action, for example /admin/options/manage
     */
    public $manageAction = '';

    /**
     * @var string update action, for example /admin/options/update
     */
    public $updateAction = '';

    /**
     * @var string manage view path
     */
    public $manageView = '@yii2options/views/manageOptions';

    /**
     * @var string update view path
     */
    public $updateView = '@yii2options/views/updateOptions';

    /**
     * @var array translations i18n
     */
    public $translations = [
        'class'          => 'yii\i18n\PhpMessageSource',
        'sourceLanguage' => 'en-US',
        'basePath'       => '@yii2options/messages',
        'fileMap'        => [],
    ];

    /**
     * @var string component db connection
     */
    public $connectionName = 'db';

    /**
     * {@inheritdoc}
     * @throws Throwable
     */
    public function bootstrap($app): void
    {
        Yii::setAlias('@yii2options', __DIR__);
        if ($app instanceof Application) {

            $request = $app->request->resolve();

            if (preg_match('/migrate\//', $request[0])) {
                return;
            } elseif (preg_match('/migrate/', $request[0])) {
                return;
            }

            $app->controllerMap = ArrayHelper::merge($app->controllerMap, ['options' => OptionsController::class]);
        }

        $this->originalParams = $app->params;
        $this->setOptions();

        if (!Yii::$app->cache->exists($this->cacheKey)) {
            $this->pull();
        } else {
            foreach ($app->cache->get($this->cacheKey) as $key => $cacheOption) {
                $this->options[$key]['value'] = $cacheOption;
            }
        }

        $this->parseParams();

        $app->params = $this->params;
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->registerTranslations();
    }

    /**
     * Register i18n.
     */
    public function registerTranslations(): void
    {
        Yii::$app->i18n->translations['yii2options'] = $this->translations;
    }

    /**
     * Set original params.
     */
    protected function setOptions(): void
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
     *
     * @param string $key   param key.
     * @param array  $param param options.
     */
    protected function setOption($key, array $param): void
    {
        $this->options[$key] = $param;
    }

    /**
     * Get option.
     *
     * @param string $key param key.
     * @return mixed
     */
    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * Push all options in sql table.
     * @throws DbException
     */
    public function push(): bool
    {
        $options = [];
        $models = [];

        /** @var BaseActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $this->models = $modelClass::findAll('');

        /** @var models\Option $model */
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
                /** @var models\Option $model */
                $model = new $this->modelClass();
                $model->load($option, '');
                $model->key = $key;
                if(!$model->save()){
                    $transaction->rollBack();
                }
            }
        } catch (Throwable $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();

            return false;
        }

        $transaction->commit();

        $this->setCache();
        return true;
    }

    /**
     * Pull all options from sql table.
     */
    protected function pull(): void
    {
        /** @var BaseActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $this->models = $modelClass::findAll('');

        if (!empty($this->models)) {
            foreach ($this->models as $model) {
                $this->options[$model->{$this->tableKeyField}] = [
                    'value' => $model->{$this->tableValueField},
                ];
            }
            $this->setCache();
        }
    }

    /**
     * Result event.
     */
    protected function parseParams(): void
    {
        foreach ($this->options as $key => $option) {
            $this->params[$key] = $option['value'];
        }
    }

    /**
     * Reset option table.
     * @throws InvalidConfigException
     * @throws DbException
     */
    public function resetDb(): void
    {
        /** @var Connection $db */
        $db = Yii::$app->get($this->connectionName);
        $db->createCommand($db->queryBuilder->truncateTable($this->tableName))->execute();
        Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * Set updated options in cache.
     */
    public function setCache(): void
    {
        foreach($this->options as $key => $option) {
            $this->cacheOptions[$key] = $option['value'];
        }

        Yii::$app->cache->set($this->cacheKey, $this->cacheOptions);
    }

    /**
     * Update option in cache.
     * @param string $key key of param.
     * @param string $value value of param.
     */
    public function updateCacheParam($key, $value): void
    {
        $this->options[$key]['value'] = $value;
        $this->setCache();
    }
}
