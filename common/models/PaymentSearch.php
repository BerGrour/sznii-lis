<?php

namespace common\models;

use common\models\Payment;
use Yii;
use yii\data\ActiveDataProvider;

class PaymentSearch extends Payment
{
    public $organization_info;
    public $sum;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_num', 'organization_info', 'contract_info', 'fact_sum', 'sum', 'act_date', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function search($params)
    {
        $query = Payment::find()->joinWith('batch.contract.organization')->groupBy('payment.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'payment.id',
                    'act_num',
                    'act_date',
                    'fact_sum',
                    'sum' => [
                        'asc' => [],
                        'desc' => []
                    ],
                    'organization_info' => [
                        'asc' => [
                            'organization.name' => SORT_ASC,
                            'contract.number' => SORT_ASC
                        ],
                        'desc' => [
                            'organization.name' => SORT_DESC,
                            'contract.number' => SORT_DESC
                        ]
                    ],
                ],
                'defaultOrder' => [
                    'payment.id' => SORT_DESC
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'payment.id' => $this->id,
            'act_num' => $this->act_num,
            'contract.id' => $this->organization_info
        ]);

        if (!is_null($this->act_date) && strpos($this->act_date, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->act_date);
            $query->andFilterWhere(['between', 'act_date', $start_date, $end_date]);
        }

        $query->andFilterWhere(['like', 'fact_sum', $this->fact_sum]);

        if ($this->status === '1') {
            $query->andWhere(['payment.locked' => 1]);
        } elseif ($this->status === '0') {
            $query->andWhere(['payment.locked' => 0]);
        }

        return $dataProvider;
    }
}
