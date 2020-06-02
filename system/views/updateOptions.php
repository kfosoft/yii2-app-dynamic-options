<?php

use kfosoft\yii2\system\models\Option as OptionModel;
use kfosoft\yii2\system\Option as OptionComponent;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * Update yii2 options view.
 *
 * @var View $this
 * @var OptionModel $model
 * @var ActiveForm $form
 */

/** @var OptionComponent $optionComponent */
$optionComponent = Yii::$app->get(OptionComponent::COMPONENT_NAME);

$this->title = Yii::t('yii2options', 'Update Option #{id} - {key}',['id' => $model->id, 'key' => $model->key]);
?>
<div class="yii2options-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="yii2options-details">
        <div class="well well-sm">
            <p><strong><?= $model->getAttributeLabel('key')?>: </strong><?= $model->key; ?></p>
            <p><strong><?= $model->getAttributeLabel('comments')?>: </strong><?= $model->comments; ?></p>
            <p><strong><?= $model->getAttributeLabel('created_at')?>: </strong><?= Yii::$app->getFormatter()->asDatetime($model->created_at); ?></p>
            <p><strong><?= $model->getAttributeLabel('updated_at')?>: </strong><?= Yii::$app->getFormatter()->asDatetime($model->updated_at); ?></p>
        </div>
    </div>

    <div class="user-form">
        <div class="form-group">
            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'required-asterisk'],
            ]); ?>

            <?= $form->field($model, 'value')->textInput([
                'maxlength' => true,
                'class'  => 'form-control form-field-short',
                'value' => $model->getValue()
            ]) ?>

        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('yii2options', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('yii2options', 'Options'), [$optionComponent->manageAction], ['class' => 'btn btn-primary'])?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
