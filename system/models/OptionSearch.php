<?php

namespace kfosoft\yii2\system\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Option represents the model behind the search form about `kfosoft\yii2\system\models\Option`.
 *
 * @package kfosoft\yii2\system\models
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class OptionSearch extends Option
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['key', 'value'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
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
