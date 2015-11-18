<?php

namespace kfosoft\yii2\system\models;

use \Yii;
use \yii\base\Model;
use \yii\data\ActiveDataProvider;

/**
 * Option represents the model behind the search form about `kfosoft\yii2\system\models\Option`.
 * @package kfosoft\yii2\system\models
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSOFT Team <kfosoftware@gmail.com>
 * @author Cyril Turkevich
 */
class OptionSearch extends Option
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
              ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
