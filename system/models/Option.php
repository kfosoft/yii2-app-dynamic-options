<?php

namespace kfosoft\yii2\system\models;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\behaviors\TimestampBehavior;

use \kfosoft\enums\IntegerX64;

/**
 * This is the model class for table "Options".
 * @property integer $id primary key.
 * @property string $key option key.
 * @property string $value option value.
 * @property string $created_at option created at.
 * @property string $updated_at option updated at.
 * @package kfosoft\yii2\system\models
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class Option extends ActiveRecord
{
    /** @var bool edit param option. */
    public $edit;

    /** @var string comments param option. */
    public $comments;

    /** @var string param validator. */
    public $validator;

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $option = Yii::$app->get('yii2options')->getOption($this->key);
        $this->edit = $option['edit'];
        $this->comments = $option['comments'];
        $this->validator = $option['validator'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            [['key', 'value'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'integer', 'min' => IntegerX64::INT_MIN, 'max' => IntegerX64::INT_MAX]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('yii2options', '#'),
            'key'        => Yii::t('yii2options', 'Key'),
            'value'      => Yii::t('yii2options', 'Value'),
            'created_at' => Yii::t('yii2options', 'Created At'),
            'updated_at' => Yii::t('yii2options', 'Updated At'),
            'comments'   => Yii::t('yii2options', 'Comments'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }
}
