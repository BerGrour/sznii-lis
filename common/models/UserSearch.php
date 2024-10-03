<?php

namespace common\models;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public $organization_name;  // атрибут для поиска по названию организации
    public $staff_name;         // атрибут для поиска по фио сотрудника
    public $role;               // атрибут для поиска по роли

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'status', 'organization_name', 'staff_name', 'role'], 'safe'],
        ];
    }

    /**
     * @param int $type тип учетки: 1 - сотрудник, 2 - организация, 0 - все
     */
    public function search($params, $type)
    {
        $query = User::find()->where(['not', ['user.id' => 1]]);

        if ($type === 1) {
            $query->joinWith('staff')
                ->andWhere(['not', ['staff_id' => null]]);
        } elseif ($type === 2) {
            $query->joinWith('organization')
                ->andWhere(['not', ['organization_id' => null]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'username',
                    'status',
                    'organization_name' => [
                        'asc' => ['organization.name' => SORT_ASC],
                        'desc' => ['organization.name' => SORT_DESC],
                    ],
                    'staff_name' => [
                        'asc' => ['staff.fio' => SORT_ASC],
                        'desc' => ['staff.fio' => SORT_DESC],
                    ]
                ],
                'defaultOrder' => [
                    'status' => SORT_DESC,
                    'username' => SORT_ASC,
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

        $query->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['=', 'user.status', $this->status]);

        if ($type === 1) {
            $query->andFilterWhere(['like', 'staff.fio', $this->staff_name]);
        } elseif ($type === 2) {
            $query->andFilterWhere(['like', 'organization.name', $this->organization_name]);
        }

        if ($this->role) {
            $query->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = user.id')
                ->join('LEFT JOIN', 'auth_item', 'auth_item.name = item_name')
                ->andFilterWhere(['auth_item.description' => $this->role]);
        }

        return $dataProvider;
    }
}
