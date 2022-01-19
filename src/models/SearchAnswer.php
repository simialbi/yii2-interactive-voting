<?php

namespace simialbi\yii2\voting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use simialbi\yii2\voting\models\Answer;

/**
 * SearchAnswer represents the model behind the search form of `simialbi\yii2\voting\models\Answer`.
 */
class SearchAnswer extends Answer
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'question_id', 'created_at', 'updated_at'], 'integer'],
            [['text', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param integer|null $questionId
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, ?int $questionId = null): ActiveDataProvider
    {
        $query = Answer::find();

        if ($questionId) {
            $query->andWhere(['question_id' => $questionId]);
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
            'question_id' => $this->question_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->created_by
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
