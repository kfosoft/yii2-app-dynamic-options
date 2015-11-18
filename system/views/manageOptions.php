<?php
use \yii\grid\GridView;
use \yii\helpers\Html;

/**
 * Manage yii2 options view.
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \kfosoft\yii2\system\models\OptionSearch $searchModel
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
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
                    /** @var \kfosoft\yii2\system\models\Option $data */
                    return $data->getValue();
                },
                'filter' => false,
            ],
            'comments:ntext',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        if(!$model->edit){
                            return '';
                        }

                        $options = array_merge([
                            'title' => Yii::t('yii2options', 'Update'),
                            'aria-label' => Yii::t('yii2options', 'Update'),
                            'data-pjax' => '0',
                        ]);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [Yii::$app->get('yii2options')->updateAction, 'id' => $model->primaryKey], $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
