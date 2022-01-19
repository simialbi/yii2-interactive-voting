<?php

namespace simialbi\yii2\voting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchVoting represents the model behind the search form of `simialbi\yii2\voting\models\Voting`.
 */
class SearchVoting extends Voting
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['is_active', 'is_finished', 'is_moderated', 'is_with_mobile_registration', 'show_results'], 'boolean'],
            [['subject', 'description', 'created_by', 'updated_by', 'finished_message'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Voting::find();

        // add conditions that should always apply here

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
            'is_active' => $this->is_active,
            'is_finished' => $this->is_finished,
            'is_moderated' => $this->is_moderated,
            'is_with_mobile_registration' => $this->is_with_mobile_registration,
            'show_results' => $this->show_results,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'finished_message', $this->finished_message]);

        return $dataProvider;
    }
}
