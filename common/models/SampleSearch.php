<?php

namespace common\models;

use common\models\Sample;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SampleSearch extends Sample
{
    public $batch_date;     // атрибут для поиска по диапазону дат поступления партии проб
    public $laboratory;     // атрибут для поиска по названию лаборатории

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identificator', 'laboratory', 'losted_at', 'batch_date'], 'safe'],
        ];
    }

    /**
     * @param int $departament_id индекс лаборатории
     * @param int $batch_id индекс партии
     * @param int $service_id индекс исследования (для поиска проб из партии входящих в исследование)
     * @param int $un_service_id индекс исследования (для поиска проб из партии НЕ входящих в исследование) 
     */
    public function search($params, $departament_id = null, $batch_id = null, $service_id = null, $un_service_id = null)
    {
        $query = Sample::find()->joinWith('batch')->joinWith('departament');

        if (Yii::$app->user->identity->staff->job->departament->role == 'laboratory') {
            $departament_id = Yii::$app->user->identity->staff->job->departament_id;
        }

        $query->filterWhere([
            'departament_id' => $departament_id
        ]);

        $pager = ['pageSize' => Yii::$app->params['pageSize']];

        if ($service_id) {
            $pager = [
                'pageSize' => Yii::$app->params['pageSize'],
                'forcePageParam' => false,
                'pageParam' => 'left_page'
            ];
        } elseif ($un_service_id) {
            $pager = [
                'pageSize' => Yii::$app->params['pageSize'],
                'forcePageParam' => false,
                'pageParam' => 'right_page'
            ];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pager,
            'sort' => [
                'attributes' => [
                    'id',
                    'batch_id',
                    'identificator' => [
                        'asc' => [
                            'batch_id' => SORT_ASC,
                            'SUBSTR(`identificator`, 1, 2)' => SORT_ASC,
                            'num' => SORT_ASC
                        ],
                        'desc' => [
                            'batch_id' => SORT_DESC,
                            'SUBSTR(`identificator`, 1, 2)' => SORT_DESC,
                            'num' => SORT_DESC
                        ]
                    ],
                    'losted_at' => [
                        'asc' => [
                            'losted_at' => SORT_ASC,
                            'identificator' => SORT_ASC
                        ],
                        'desc' => [
                            'losted_at' => SORT_DESC,
                            'identificator' => SORT_ASC
                        ],
                    ],
                    'laboratory' => [
                        'asc' => [
                            'departament.title' => SORT_ASC
                        ],
                        'desc' => [
                            'departament.title' => SORT_DESC
                        ]
                    ]
                ],
                'defaultOrder' => [
                    'identificator' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($service_id != 0) {
            $query->joinWith('sampleServices', true, 'INNER JOIN')
                ->andFilterWhere(['sample_service.service_id' => $service_id]);
        }

        if ($un_service_id != 0) {
            $subsubQuery = (new Query())
                ->select(['sample_id'])
                ->from('sample_service')
                ->andWhere(['service_id' => $un_service_id]);

            $subQuery = (new Query())
                ->select(['sample.id'])
                ->from('sample')
                ->leftJoin('sample_service', 'sample.id = sample_service.sample_id')
                ->andWhere(['NOT IN', 'sample.id', $subsubQuery])
                ->orWhere(['sample_service.sample_id' => null]);

            $query->andWhere(['IN', 'sample.id', $subQuery]);
        }

        if ($this->laboratory) {
            $query->joinWith('departament')
                ->andFilterWhere(['like', 'departament.title', $this->laboratory]);
        }

        if (!is_null($this->batch_date) && strpos($this->batch_date, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->batch_date);
            $query->andFilterWhere(['between', 'batch.employed_at', $start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        }

        if (!is_null($this->losted_at) && strpos($this->losted_at, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->losted_at);
            $query->andFilterWhere(['between', 'losted_at', $start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        }

        $query->andFilterWhere(['like', 'sample.identificator', $this->identificator])
            ->andFilterWhere(['batch_id' => $batch_id]);

        return $dataProvider;
    }
}
