<?php
namespace kfosoft\yii2\system\commands;
use \Yii;
use \yii\console\Controller;

/**
 * Options Command Controller.
 * @package kfosoft\yii2\system\commands
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class OptionsController extends Controller
{
    /**
     * Push original params.
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPush()
    {
        Yii::$app->get('yii2options')->push();
    }

    /**
     * Clear db params.
     */
    public function actionClear()
    {
        Yii::$app->get('yii2options')->resetDb();
    }
}