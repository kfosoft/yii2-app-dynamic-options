<?php
namespace kfosoft\yii2\system;

use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;

/**
 * Options Widget.
 *
 * @package kfosoft\yii2\system\widgets
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class OptionsWidget extends Widget
{
    /**
     * @var Option
     */
    private $optionComponent;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $this->optionComponent = Yii::$app->get(Option::COMPONENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        $modelClass = $this->optionComponent->modelSearchClass;

        /** @var models\Option $model */
        $model = new $modelClass();

        return $this->render($this->optionComponent->manageView, [
            'dataProvider' => $model->search(Yii::$app->request->get()),
            'searchModel'  => $model,
        ]);
    }
}