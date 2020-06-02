<?php
namespace kfosoft\yii2\system\commands;

use kfosoft\yii2\system\Option as OptionComponent;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;

/**
 * List commands to manage dynamic options.
 *
 * @package kfosoft\yii2\system\commands
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class OptionsController extends Controller
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
     * Push params from application config.
     */
    public function actionPush()
    {
        $this->optionsComponent->push();
    }

    /**
     * Clear db params in database.
     */
    public function actionClear()
    {
        $this->optionsComponent->resetDb();
    }
}