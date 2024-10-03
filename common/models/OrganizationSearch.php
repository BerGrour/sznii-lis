<?php

namespace common\models;

use common\models\Organization;
use Yii;
use yii\data\ActiveDataProvider;

class OrganizationSearch extends Organization
{
    public $active_contract;    // атрибут для поиска по активному договору

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'inn', 'director', 'active_contract'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function search($params)
    {
        $query = Organization::find()
            ->joinWith('user')->joinWith('contracts')->groupBy('organization.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'inn',
                    'director',
                    'user_id' => [
                        'asc' => [
                            'user.organization_id' => new \yii\db\Expression(
                                'CASE WHEN user.organization_id IS NULL THEN 0 ELSE 1 END, organization.name ASC'
                            ),
                        ],
                    ]
                ],
                'defaultOrder' => [
                    'user_id' => SORT_ASC,
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

        $query->andFilterWhere(['like', 'organization.name', $this->name])
            ->andFilterWhere(['like', 'organization.inn', $this->inn])
            ->andFilterWhere(['like', 'organization.director', $this->director])
            ->andFilterWhere(['contract.number' => $this->active_contract]);

        return $dataProvider;
    }
}
