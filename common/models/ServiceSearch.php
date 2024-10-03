<?php

namespace common\models;

use common\components\Utility;
use common\models\Service;
use Yii;
use yii\data\ActiveDataProvider;

class ServiceSearch extends Service
{
    public $staff_name;     // атрибут для поиска по сотруднику
    public $status;         // атрибут для поиска по статусу исследования (для лабораторий)
    public $statusClient;   // атрибут для поиска по статусу исследования (для клиентов)
    public $sample_type;     // атрибут для поиска по типу проб
    public $laboratory;     // атрибут для поиска по названию лаборатории
    public $sum;            // атрибут для поиска по стоимости (объединяет service.pre_sum и payment.fact_sum)
    public $batch_date;     // атрибут для поиска по дате прихода партии проб

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['research', 'started_at', 'sample_type', 'laboratory', 'staff_name', 'status', 'statusClient', 'completed_at', 'amount', 'sum', 'batch_date'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     * 
     * @param int|null $organization_id индекс организации
     * @param int|null $batch_id индекс партии проб
     * @param bool $late Показывать просроченные исследования, default = false
     */
    public function search($params, $organization_id = null, $batch_id = null, $late = false)
    {
        $query = Service::find()
            ->joinWith('staff')
            ->joinWith('staff.job.departament')
            ->joinWith('batch.payment', false, 'LEFT JOIN');

        if (Yii::$app->user->identity->staff and Yii::$app->user->identity->staff->job->departament->role == 'laboratory') {
            $query->where(['departament.id' => Yii::$app->user->identity->staff->job->departament_id]);
        }
        $pagination = ['pageSize' => Utility::getPagination($params, Yii::$app->params['pageSize'])];

        if ($organization_id) {
            $query->joinWith('batch.contract')
                ->where(['contract.organization_id' => $organization_id]);
            $pagination = ['pageSize' => Utility::getPagination($params, Yii::$app->params['pageSizeClient'])];
        }
        if ($batch_id) {
            $query->where(['service.batch_id' => $batch_id]);
        }

        if ($late) {
            $query->andWhere(new \yii\db\Expression('`predict_date` < DATE(`completed_at`)'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort' => [
                'attributes' => [
                    'id',
                    'research',
                    'started_at',
                    'batch_date' => [
                        'asc' => ['batch_id' => SORT_ASC],
                        'desc' => ['batch_id' => SORT_DESC]
                    ],
                    'completed_at' => [
                        'asc' => ['completed_at' => new \yii\db\Expression(
                            'CASE WHEN completed_at IS NULL THEN 1 ELSE 0 END, completed_at ASC'
                        )],
                        'desc' => ['completed_at' => new \yii\db\Expression(
                            'CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END, completed_at DESC'
                        )]
                    ],
                    'laboratory' => [
                        'asc' => [
                            'departament.title' => SORT_ASC,
                            'id' => SORT_DESC,
                        ],
                        'desc' => [
                            'departament.title' => SORT_DESC,
                            'id' => SORT_DESC,
                        ]
                    ],
                    'sample_type' => [
                        'asc' => [
                            'departament.short_name' => SORT_ASC,
                            'id' => SORT_DESC,
                        ],
                        'desc' => [
                            'departament.short_name' => SORT_DESC,
                            'id' => SORT_DESC,
                        ]
                    ],
                    'staff_name' => [
                        'asc' => [
                            'staff.fio' => SORT_ASC,
                            'id' => SORT_DESC
                        ],
                        'desc' => [
                            'staff.fio' => SORT_DESC,
                            'id' => SORT_DESC
                        ]
                    ],
                ],
                'defaultOrder' => [
                    'batch_date' => SORT_DESC,
                    'laboratory' => SORT_ASC,
                    'research' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if (isset($this->status)) {
            switch ($this->status) {
                case 0:
                    $query->andWhere(['NOT IN', 'service.id', SampleService::find()
                        ->select('service_id')]);
                    break;
                case 1:
                    $query->andWhere(['completed_at' => null])
                        ->andWhere(['<', 'predict_date', date('Y-m-d')]);
                    break;
                case 2:
                    $query->andWhere(['IN', 'service.id', SampleService::find()
                        ->select('service_id')])
                        ->andWhere(['service.locked' => 0]);
                    break;
                case 3:
                    $query->andWhere(['service.locked' => 1])
                        ->andWhere(['or',
                            ['payment.id' => null],
                            ['payment.pay_date' => null]
                        ]);
                    break;
                case 4:
                    $query->andWhere(['service.locked' => 1])
                        ->andWhere(['not', ['payment.pay_date' => null, 'payment.id' => null]]);
                    break;
            }
        }

        if (isset($this->statusClient)) {
            switch ($this->statusClient) {
                case 1:
                    $query->andwhere(['IN', 'service.id', SampleService::find()->select('service_id')])
                        ->andWhere(['service.locked' => 0]);
                    break;
                case 2:
                    $query->andWhere(['or',
                            ['payment.act_date' => null],
                            ['payment.file_act' => null],
                            ['payment.file_invoice' => null],
                            ['payment.file_pay' => null]
                        ]);
                    break;
                case 3:
                    $query->andWhere(['and',
                        ['not', ['payment.act_date' => null]],
                        ['not', ['payment.file_act' => null]],
                        ['not', ['payment.file_invoice' => null]]
                    ])->andWhere(['payment.pay_date' => null]);
                    break;
                case 4:
                    $query->andWhere(['and',
                        ['not', ['payment.act_date' => null]],
                        ['not', ['payment.pay_date' => null]],
                        ['not', ['payment.file_act' => null]],
                        ['not', ['payment.file_invoice' => null]],
                        ['not', ['payment.file_pay' => null]]
                    ])->andWhere(['or',
                        ['payment.file_act_client' => null],
                        ['payment.return_date' => null]
                    ]);
                    break;
                case 5:
                    $query->andWhere(['and',
                        ['not', ['payment.act_date' => null]],
                        ['not', ['payment.return_date' => null]],
                        ['not', ['payment.pay_date' => null]],
                        ['not', ['payment.file_act' => null]],
                        ['not', ['payment.file_act_client' => null]],
                        ['not', ['payment.file_invoice' => null]],
                        ['not', ['payment.file_pay' => null]],
                        ['payment.locked' => 0]
                    ]);
                    break;
                case 6:
                    $query->andWhere(['payment.locked' => 1]);
                    break;
            }
        }

        if (!is_null($this->batch_date) && strpos($this->batch_date, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->batch_date);
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $query->andFilterWhere([
                'between',
                'batch.employed_at',
                Yii::$app->formatter->asDate($start_date, 'php:Y-m-d'),
                Yii::$app->formatter->asDate($end_date, 'php:Y-m-d')
            ]);
        }

        $query->andFilterWhere(['like', 'research', $this->research])
            ->andFilterWhere(['like', 'staff.fio', $this->staff_name])
            ->andFilterWhere(['=', 'departament.id', $this->sample_type])
            ->andFilterWhere(['=', 'departament.id', $this->laboratory])
            ->andFilterWhere(['like', 'started_at', $this->started_at])
            ->andFilterWhere(['like', 'completed_at', $this->completed_at]);

        return $dataProvider;
    }
}
