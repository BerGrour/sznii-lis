<?php

namespace console\controllers;

use common\models\CalendarDate;
use common\models\CalendarYear;
use yii\console\Controller;
use yii\httpclient\Client;

class CalendarController extends Controller
{
    public function getUrl($year = null)
    {
        return "https://xmlcalendar.ru/data/ru/{$year}/calendar.json";
    }

    /**
     * {@inheritDoc}
     */
    public function actionIndex($year = null)
    {
        if ($year === null) $year = date("Y") + 1;

        $year_model = new CalendarYear();
        $year_model->number = $year;

        if (!CalendarYear::findOne(['number' => $year]) && $year_model->save()) {
            $transaction = \Yii::$app->db->beginTransaction();

            $url = $this->getUrl($year);
            $client = new Client();
            $request = $client->get($url);

            echo "\nПроизводственный календарь за {$year} год по адресу:\n{$url}\n";

            if ($response = $client->send($request)) {
                echo "\nОтвет успешно получен.\n";
                echo "\nБыли получены следущие выходные дни:\n";
                $data = $response->getData()["months"];

                foreach ($data as $elem) {
                    $cur_month = str_pad($elem['month'], 2, '0', STR_PAD_LEFT);
                    $days = explode(',', $elem['days']);

                    foreach ($days as $key => $day) {
                        if (strpos($day, '*')) continue;

                        $cur_day = str_pad((int)$day, 2, '0', STR_PAD_LEFT);

                        $date = new CalendarDate();
                        $date->year_id = $year_model->id;
                        $date->date = "{$cur_day}.{$cur_month}";

                        if (!$date->save()) {
                            echo "Произошла ошибка во время сохранения!";
                            $transaction->rollBack();
                            return false;
                        }

                        echo "{$cur_day}.{$cur_month}";
                        if ($key != count($days) - 1) echo ", ";
                    }
                    echo "\n";
                }
            } else {
                echo "\nОтвета не было получено!\n\nАдрес:\n{$url}\n";
                return false;
            }
        } else {
            echo "Данный год уже был загружен в систему!";
            return false;
        }

        if (isset($transaction)) {
            echo "Успешно сохранено.\n";
            $transaction->commit();
            return true;
        } else {
            echo "Данных не найдено. Был занесен пустой год.\n";
            return true;
        }
    }
}
