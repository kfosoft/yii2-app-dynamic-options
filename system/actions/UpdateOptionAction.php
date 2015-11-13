<?php

namespace kfosoft\yii2\system\actions;

use \Yii;
use \yii\base\Action;
use \yii\web\NotFoundHttpException;

/**
 * @inheritdoc
 * @package app\helpers
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class UpdateOptionAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $modelClass = Yii::$app->get('yii2options')->modelClass;
        $model = $modelClass::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException;
        }

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->controller->redirect([Yii::$app->get('yii2options')->manageAction]);
        }

        $view = Yii::$app->get('yii2options')->updateView;
        $params = [
            'model' => $model
        ];

        return Yii::$app->request->isAjax ? $this->controller->renderAjax($view, $params) : $this->controller->render($view, $params);
    }
}