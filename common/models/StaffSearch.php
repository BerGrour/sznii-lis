<?php

namespace common\models;

use common\models\Staff;
use Yii;
use yii\data\ActiveDataProvider;

class StaffSearch extends Staff
{
    public $job_name;           // атрибут для поиска по должности
    public $departament_name;   // атрибут для поиска по отделу
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'employ_date', 'leave_date', 'job_name', 'departament_name'], 'safe'],
        ];
    }

    /**
     * @param int $job_id индекс должности
     * @param bool $active фильтрация по активным сотрудникам, иначе все
     */
    public function search($params, $job_id = 0, $active = true)
    {
        $query = Staff::find()->joinWith('job')
            ->joinWith('user', true, 'LEFT JOIN')
            ->joinWith('job.departament')
            ->where(['or',
                ['user.id' => null],
                ['not', ['user.id' => 1]],
            ]);

        $defaultOrder = ['leave_date' => SORT_ASC];

        if ($active) {
            $query->andWhere(['leave_date' => null]);
            $defaultOrder = ['user_id' => SORT_ASC];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'fio',
                    'employ_date',
                    'job_name' => [
                        'asc' => ['job.title' => SORT_ASC],
                        'desc' => ['job.title' => SORT_DESC]
                    ],
                    'departament_name' => [
                        'asc' => ['departament.title' => SORT_ASC],
                        'desc' => ['departament.title' => SORT_DESC]
                    ],
                    'user_id' => [
                        'asc' => [
                            'user.staff_id' => new \yii\db\Expression(
                                'CASE WHEN user.staff_id IS NULL THEN 0 ELSE 1 END, staff.fio ASC'
                            ),
                        ],
                    ],
                    'leave_date' => [
                        'asc' => new \yii\db\Expression(
                            'CASE WHEN leave_date IS NULL THEN 0 ELSE 1 END, id DESC'
                        ),
                        'desc' => new \yii\db\Expression(
                            'CASE WHEN leave_date IS NULL THEN 1 ELSE 0 END, id ASC'
                        )
                    ]
                ],
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($job_id != 0) {
            $query->andFilterWhere(['job_id' => $job_id]);
        }

        if (!is_null($this->employ_date) && strpos($this->employ_date, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->employ_date);
            $query->andFilterWhere(['between', 'employ_date', $start_date, $end_date]);
        }

        if (!is_null($this->leave_date) && strpos($this->leave_date, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->leave_date);
            $query->andFilterWhere(['between', 'leave_date', $start_date, $end_date]);
        }

        if ($this->job_name) {
            $query->andFilterWhere(['=', 'job.title', $this->job_name]);
        }

        if ($this->departament_name) {
            $query->andFilterWhere(['=', 'departament.title', $this->departament_name]);
        }

        $query->andFilterWhere(['like', 'staff.fio', $this->fio]);

        return $dataProvider;
    }
}
