<?php

namespace simialbi\yii2\voting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use simialbi\yii2\voting\models\Invitee;

/**
 * SearchInvitee represents the model behind the search form of `simialbi\yii2\voting\models\Invitee`.
 */
class SearchInvitee extends Invitee
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['voting_id', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'code', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    /**
     * {@inheritDoc}
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
        $query = Invitee::find();

        if ($votingId) {
            $query->andWhere(['voting_id' => $votingId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'voting_id' => $this->voting_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->created_by
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
