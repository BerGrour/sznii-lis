<?php

namespace common\models;

use common\models\Batch;
use Yii;
use yii\data\ActiveDataProvider;

class BatchSearch extends Batch
{
    public $organization_name;  // атрибут для поиска по названию организации

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employed_at', 'organization_name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Batch::find()->joinWith('contract.organization');

        if (Yii::$app->user->identity->staff and Yii::$app->user->identity->staff->job->departament->role == 'laboratory') {
            $query->joinWith('samples')
            ->where(['sample.departament_id' => Yii::$app->user->identity->staff->job->departament_id])
            ->groupBy('batch.id');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'employed_at',
                    'organization_name' => [
                        'asc' => [
                            'organization.name' => SORT_ASC,
                            'batch.employed_at' => SORT_DESC
                        ],
                        'desc' => [
                            'organization.name' => SORT_DESC,
                            'batch.employed_at' => SORT_DESC
                        ]
                    ],
                ],
                'defaultOrder' => [
                    'employed_at' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'contract.id' => $this->organization_name
        ]);

        if (!is_null($this->employed_at) && strpos($this->employed_at, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->employed_at);
            $query->andFilterWhere(['between', 'employed_at', $start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        }

        return $dataProvider;
    }
}
