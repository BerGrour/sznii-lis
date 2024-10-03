<?php

namespace common\models;

use common\models\ArchivePriceList;
use Yii;
use yii\data\ActiveDataProvider;

class ArchivePriceListSearch extends ArchivePriceList
{
    public $research_name;      // атрибут для поиска по названию исследования
    public $departament_name;   // атрибут для поиска по названию лаборатории

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['research_name', 'departament_name', 'price', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @param int $research_id индекс исследования
     */
    public function search($params, $research_id = 0, $departament_id = null)
    {
        $query = ArchivePriceList::find();

        if ($departament_id) {
            $query->joinWith('research')
                ->where(['price_list.departament_id' => $departament_id]);
        }
        if ($research_id) {
            $query->where(['research_id' => $research_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'updated_at',
                    'price'
                ],
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
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

        if ($this->research_name) {
            $query->joinWith('research')
                ->andFilterWhere(['LIKE', 'price_list.research', $this->research_name]);
        }

        if ($this->departament_name) {
            $query->joinWith('research')->joinWith('research.departament')
                ->andFilterWhere(['LIKE', 'departament.title', $this->departament_name]);
        }

        if (!is_null($this->updated_at) && strpos($this->updated_at, ' | ') !== false) {
            list($start_date, $end_date) = explode(' | ', $this->updated_at);
            $query->andFilterWhere(['between', 'updated_at', $start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        }

        $query->andFilterWhere(['like', 'price', $this->price]);

        return $dataProvider;
    }
}
