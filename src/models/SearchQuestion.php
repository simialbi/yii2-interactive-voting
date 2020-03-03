<?php

namespace simialbi\yii2\voting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchQuestion represents the model behind the search form of `simialbi\yii2\voting\models\Question`.
 */
class SearchQuestion extends Question
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'voting_id', 'created_at', 'updated_at'], 'integer'],
            [['is_active', 'is_finished'], 'boolean'],
            [['subject', 'description', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param integer|null $votingId
     *
     * @return ActiveDataProvider
     */
    public function search($params, $votingId = null)
    {
        $query = Question::find();

        if ($votingId) {
            $query->andWhere(['voting_id' => $votingId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'voting_id' => $this->voting_id,
            'is_active' => $this->is_active,
            'is_finished' => $this->is_finished,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->created_by
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
