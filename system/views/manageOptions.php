<?php

use kfosoft\yii2\system\models\Option as OptionModel;
use kfosoft\yii2\system\models\OptionSearch as OptionSearchModel;
use kfosoft\yii2\system\Option as OptionComponent;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * Manage yii2 options view.
 *
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var OptionSearchModel $searchModel
 */

/** @var OptionComponent $optionComponent */
$optionComponent = Yii::$app->get('yii2options');
?>
<div class="yii2-options-manage-options">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'key',
            [
                'header' => $searchModel->getAttributeLabel('value'),
                'value' => function($data) {
                    /** @var OptionModel $data */
                    return $data->getValue();
                },
                'filter' => false,
            ],
            'comments:ntext',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) use ($optionComponent) {
                        /** @var OptionModel $model */
                        if (!$model->edit){
                            return '';
                        }

                        $options = array_merge([
                            'title' => Yii::t('yii2options', 'Update'),
                            'aria-label' => Yii::t('yii2options', 'Update'),
                            'data-pjax' => '0',
                        ]);

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [$optionComponent->updateAction, 'id' => $model->primaryKey], $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
