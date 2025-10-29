<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * ClientSearch represents the model behind the search form of `frontend\models\Client`.
 */
class ClientSearch extends Client
{
    public $full_name;
    public $birth_date_range;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'gender', 'birth_date_range'], 'safe'],
            [['status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Client::find()->joinWith('clubs');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->status) {
            $query->andFilterWhere(['client.status' => $this->status]);
        }

        if ($this->gender) {
            $query->andFilterWhere(['gender' => $this->gender]);
        }

        if ($this->full_name) {
            $query->andFilterWhere([
                'or',
                ['like', 'first_name', $this->full_name],
                ['like', 'last_name', $this->full_name],
                ['like', 'middle_name', $this->full_name],
            ]);
        }

        if ($this->birth_date_range) {
            $dates = explode(' - ', $this->birth_date_range);
            if (count($dates) === 2) {
                $start = $dates[0];
                $end = $dates[1];
                $query->andFilterWhere(['between', 'birth_date', $start, $end]);
            }
        }

        return $dataProvider;
    }
}
