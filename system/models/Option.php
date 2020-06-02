<?php

namespace kfosoft\yii2\system\models;

use kfosoft\enums\IntegerX64;
use kfosoft\yii2\system\Option as OptionComponent;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int     $id         primary key.
 * @property string  $key        option key.
 * @property string  $value      option value.
 * @property string  $created_at option created at.
 * @property string  $updated_at option updated at.
 *
 * @package kfosoft\yii2\system\models
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class Option extends ActiveRecord
{
    /**
     * @var bool edit param option
     */
    public $edit;

    /**
     * @var string comments param option
     */
    public $comments;

    /**
     * @var string param validator
     */
    public $validator;

    /**
     * @var callable|null event beforeValidate
     */
    public $beforeValidate;

    /**
     * @var callable|null event afterValidate
     */
    public $afterValidate;

    /**
     * @var callable|null event afterFind
     */
    public $afterFind;

    /**
     * @var callable|null event beforeSave
     */
    public $beforeSave;

    /**
     * @var callable|null event afterSave
     */
    public $afterSave;

    /**
     * @var callable|null event getValue
     */
    public $getValue;

    /**
     * @var callable|null event setValue
     */
    public $setValue;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function afterFind(): void
    {
        parent::afterFind();

        $this->loadOption(Yii::$app->get(OptionComponent::COMPONENT_NAME)->getOption($this->key));

        $this->value = unserialize(base64_decode($this->value));

        if (is_callable($this->afterFind)) {
            call_user_func_array($this->afterFind, [$this, 'afterFind']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        $this->value = unserialize(base64_decode($this->value));

        if (is_callable($this->afterSave)) {
            call_user_func_array($this->afterSave, [$this, 'afterSave', $insert, $changedAttributes]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(): void
    {
        parent::afterValidate();

        if (is_callable($this->afterValidate)) {
            call_user_func_array($this->afterValidate, [$this, 'afterValidate']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate(): bool
    {
        if (is_callable($this->beforeValidate)) {
            return parent::beforeValidate() && call_user_func_array($this->beforeValidate, [$this, 'beforeValidate']);
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        $this->value = base64_encode(serialize($this->value));
        if (is_callable($this->beforeSave)) {
            return parent::beforeSave($insert) && call_user_func_array($this->beforeSave, [$this, 'beforeSave', $insert]);
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public static function tableName(): string
    {
        return Yii::$app->get(OptionComponent::COMPONENT_NAME)->tableName;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
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
                                $this->addError('value',
                                    Yii::t('yii2options', 'Param "{param}" cannot be empty', ['param' => $this->key]));
                            } elseif (strlen($this->value) > 255) {
                                $this->addError('value',
                                    Yii::t('yii2options', 'Param "{param}" length cannot be more than 255 symbols',
                                        ['param' => $this->key]));
                            }
                            break;
                        case $this->validator === 'integer' :
                            if (!preg_match('/^\s*[+-]?\d+\s*$/', "$value")) {
                                $this->addError('value',
                                    Yii::t('yii2options', 'Param "{param}" must have type "integer"!',
                                        ['param' => $this->key]));
                            } elseif (strlen((string)$this->value) > 255) {
                                $this->addError('value',
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
            [['validator', 'edit', 'comments', 'beforeValidate', 'afterValidate', 'afterFind', 'beforeSave', 'afterSave', 'getValue', 'setValue'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
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
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Returns value of key.
     * @return mixed value of key.
     */
    public function getValue()
    {
        return is_callable($this->getValue) ? call_user_func($this->getValue, $this->value) : $this->value;
    }


    /**
     * Set value of key.
     * @param mixed $value value of key.
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = is_callable($this->setValue) ? call_user_func($this->setValue, $value) : $value;
        return $this;
    }

    /**
     * Load class options.
     * @param array $attributes class attributes.
     * @return self
     */
    public function loadOption(array $attributes): self
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }

        return $this;
    }
}
