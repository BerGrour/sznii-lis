<?php

namespace common\models;

use common\models\Job;
use Yii;
use yii\data\ActiveDataProvider;

class JobSearch extends Job
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'safe'],
        ];
    }

    /**
     * @param int $departament_id индекс отдела
     */
    public function search($params, $departament_id = 0)
    {
        $query = Job::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'title',
                ],
                'defaultOrder' => [
                    'title' => SORT_ASC,
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

        if ($departament_id != 0) {
            $query->andFilterWhere(['departament_id' => $departament_id]);
        }

        $query->andFilterWhere(['like', 'job.title', $this->title]);

        return $dataProvider;
    }
}
