<?php

namespace common\models;

use common\models\Departament;
use Yii;
use yii\data\ActiveDataProvider;

class DepartamentSearch extends Departament
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'short_name', 'role'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Departament::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'title',
                    'role',
                    'short_name' => [
                        'asc' => ['short_name' => new \yii\db\Expression(
                            'CASE WHEN short_name IS NULL THEN 1 ELSE 0 END, short_name ASC'
                        )],
                        'desc' => ['short_name' => new \yii\db\Expression(
                            'CASE WHEN short_name IS NULL THEN 0 ELSE 1 END, short_name DESC'
                        )]
                    ]
                ],
                'defaultOrder' => [
                    'role' => SORT_ASC,
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

        $query->andFilterWhere(['like', 'departament.title', $this->title])
            ->andFilterWhere(['like', 'departament.role', $this->role])
            ->andFilterWhere(['like', 'departament.short_name', $this->short_name]);

        return $dataProvider;
    }
}
