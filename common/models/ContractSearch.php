<?php

namespace common\models;

use common\models\Contract;
use Yii;
use yii\data\ActiveDataProvider;

class ContractSearch extends Contract
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'start_date', 'end_date'], 'safe'],
        ];
    }

    /**
     * @param int $organization_id индекс отдела
     */
    public function search($params, $organization_id = 0)
    {
        $query = Contract::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'number',
                    'start_date',
                    'end_date'
                ],
                'defaultOrder' => [
                    'number' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($organization_id != 0) {
            $query->andFilterWhere(['organization_id' => $organization_id]);
        }

        $query->andFilterWhere(['like', 'contract.number', $this->number])
            ->andFilterWhere(['like', 'contract.start_date', $this->start_date])
            ->andFilterWhere(['like', 'contract.end_date', $this->end_date]);

        return $dataProvider;
    }
}
