<?php
namespace kfosoft\yii2\system\widgets;

use \Yii;
use \yii\bootstrap\Widget;

/**
 * Options Widget.
 * @package kfosoft\yii2\system\widgets
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class OptionsWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $modelClass = Yii::$app->get('yii2options')->modelSearchClass;
        $model = new $modelClass();

        return $this->render(Yii::$app->get('yii2options')->manageView, [
            'dataProvider' => $model->search(Yii::$app->request->get()),
            'searchModel'  => $model,
        ]);
    }
}