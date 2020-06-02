<?php

namespace kfosoft\yii2\system\actions;

use kfosoft\yii2\system\models\Option as OptionModel;
use kfosoft\yii2\system\Option as OptionComponent;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Update option action
 *
 * @package kfosoft\yii2\system\actions
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class UpdateOptionAction extends Action
{
    /**
     * @var OptionComponent
     */
    private $optionsComponent;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->optionsComponent = Yii::$app->get(OptionComponent::COMPONENT_NAME);
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @return string|Response
     */
    public function run($id)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->optionsComponent->modelClass;

        /** @var OptionModel $model */
        $model = $modelClass::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $postData = Yii::$app->request->post();
        if(isset($postData[$model->formName()]['value'])){
            $model->setValue($postData[$model->formName()]['value']);
            if($model->save()) {
                $this->optionsComponent->updateCacheParam($model->key, $model->value);
                return $this->controller->redirect([$this->optionsComponent->manageAction]);
            }

        }

        $view = $this->optionsComponent->updateView;

        $params = [
            'model' => $model
        ];

        return Yii::$app->request->isAjax ? $this->controller->renderAjax($view, $params) : $this->controller->render($view, $params);
    }
}