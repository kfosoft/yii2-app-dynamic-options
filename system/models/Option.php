<?php

namespace kfosoft\yii2\system\models;

use \Yii;
use yii\base\Exception;
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

    /** @var callable|null event beforeValidate. */
    public $beforeValidate;

    /** @var callable|null event afterValidate. */
    public $afterValidate;

    /** @var callable|null event afterFind. */
    public $afterFind;

    /** @var callable|null event beforeSave. */
    public $beforeSave;

    /** @var callable|null event afterSave. */
    public $afterSave;

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if (is_callable($this->afterFind)) {
            call_user_func_array($this->afterFind, [$this, 'afterFind']);
        }

        $option = Yii::$app->get('yii2options')->getOption($this->key);
        $this->edit = $option['edit'];
        $this->comments = $option['comments'];
        $this->validator = $option['validator'];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (is_callable($this->afterSave)) {
            call_user_func_array($this->afterSave, [$this, 'afterSave', $insert, $changedAttributes]);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
        if (is_callable($this->afterValidate)) {
            call_user_func_array($this->afterValidate, [$this, 'afterFind']);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (is_callable($this->beforeValidate)) {
            return parent::beforeValidate() && call_user_func_array($this->beforeValidate, [$this, 'beforeValidate']);
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (is_callable($this->beforeSave)) {
            return parent::beforeSave($insert) && call_user_func_array($this->beforeSave, [$this, 'beforeSave', $insert]);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->get('yii2options')->tableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            ['key', 'string', 'max' => 255],
            [
                'value',
                'filter',
                'filter' => function ($value) {
                    switch (true) {
                        case is_callable($this->validator) :
                            call_user_func_array($this->validator, [$this, $this->value, $this->key]);
                            break;
                        case $this->validator === 'string' :
                            if (empty($this->value)) {
                                $this->addError($this->key,
                                    Yii::t('yii2options', 'Param "{param}" cannot be empty', ['param' => $this->key]));
                            } elseif (strlen($this->value) > 255) {
                                $this->addError($this->key,
                                    Yii::t('yii2options', 'Param "{param}" length cannot be more than 255 symbols',
                                        ['param' => $this->key]));
                            }
                            break;
                        case $this->validator === 'integer' :
                            if (!is_int($this->value)) {
                                $this->addError($this->key,
                                    Yii::t('yii2options', 'Param "{param}" must have type "integer"!',
                                        ['param' => $this->key]));
                            } elseif (strlen((string)$this->value) > 255) {
                                $this->addError($this->key,
                                    Yii::t('yii2options', 'Param "{param}" length cannot be more than 255 symbols',
                                        ['param' => $this->key]));
                            }
                            break;
                        default :
                            throw new Exception('Undefined validator "' . $this->validator . '"');
                    }
                    return $value;
                }
            ],
            [['created_at', 'updated_at'], 'integer', 'min' => IntegerX64::INT_MIN, 'max' => IntegerX64::INT_MAX],
            [['validator', 'edit', 'comments'], 'safe'],
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
