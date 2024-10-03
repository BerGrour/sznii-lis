<?php

namespace common\models;

use common\models\PriceList;
use Yii;
use yii\data\ActiveDataProvider;

class PriceListSearch extends PriceList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['research', 'status', 'price'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function search($params, $departament_id = null)
    {
        $query = PriceList::find();

        if ($departament_id) {
            $query->where(['departament_id' => $departament_id]);
        }
        if (!Yii::$app->user->can('price_list/update')) {
            $query->andWhere(['status' => 1]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'research',
                    'status',
                    'price',
                ],
                'defaultOrder' => [
                    'status' => SORT_DESC,
                    'research' => SORT_ASC,
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

        $query->andFilterWhere(['like', 'research', $this->research])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'price', $this->price]);

        return $dataProvider;
    }
}
