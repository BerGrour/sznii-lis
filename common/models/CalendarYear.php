<?php

namespace common\models;

use Yii;

/**
 * @property int $id
 * @property int $number
 * 
 * @property CalendarDate[] $dates
 */
class CalendarYear extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'calendar_year';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['number'], 'required'],
            [['number'], 'unique'],
            [['number'], 'integer', 'min' => date('Y') - 3, 'max' => date('Y') + 3]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Год'
        ];
    }

    /**
     * Gets query for [[CalendarDates]].
     * 
     * @return Yii\db\ActiveQuery
     */
    public function getDates()
    {
        return $this->hasMany(CalendarDate::class, ['year_id' => 'id']);
    }

    /**
     * Массив календаря по годам для навигацонной панели
     * @return array
     */
    static function arrayYears()
    {
        $out = [];
        $years = CalendarYear::find()->orderBy(['number' => SORT_DESC])->all();
        foreach ($years as $year) {
            $out[] = [
                'label' => $year->number,
                'url' => ['/calendar/view', 'year' => $year->number],
            ];
        }
        return $out;
    }

    /**
     * Получение массива с наименованиями месяцев по номерам
     * @return string[]
     */
    static function getNameMonths()
    {
        return [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];
    }

    /**
     * Получение html-конструкции для месяца
     * @param string $month месяц
     * @param string $year год
     * @param array $events массив с датами выходных
     * @return string
     */
    public static function getMonth($month, $year, $events = [])
    {
        $months = self::getNameMonths();
        $month = intval($month);
        $out = '
        <div class="calendar-item">
            <div class="calendar-head">' . $months[$month] . ' ' . $year . '</div>
            <table>
                <tr>
                    <th>Пн</th>
                    <th>Вт</th>
                    <th>Ср</th>
                    <th>Чт</th>
                    <th>Пт</th>
                    <th class="weekend">Сб</th>
                    <th class="weekend">Вс</th>
                <tr>';

        $day_week = date('N', mktime(0, 0, 0, $month, 1, $year));
        $day_week--;

        $out .= '<tr>';

        for ($x = 0; $x < $day_week; $x++) {
            $out .= '<td></td>';
        }

        $days_counter = 0;
        $days_month = date('t', mktime(0, 0, 0, $month, 1, $year));

        for ($day = 1; $day <= $days_month; $day++) {
            $full_date = "{$day}.{$month}.{$year}";
            if (date('j.n.Y') == $full_date) {
                $class = ' today';
            } elseif (time() > strtotime($full_date)) {
                $class = ' last';
            } else {
                $class = '';
            }

            $event_show = false;
            if (!empty($events)) {
                foreach ($events as $id => $date) {
                    $date = explode('.', $date);
                    if (count($date) == 3) {
                        $y = explode(' ', $date[2]);
                        if (count($y) == 2) {
                            $date[2] = $y[0];
                        }

                        if ($day == intval($date[0]) && $month == intval($date[1]) && $year == $date[2]) {
                            $event_show = true;
                        }
                    } elseif (count($date) == 2) {
                        if ($day == intval($date[0]) && $month == intval($date[1])) {
                            $event_show = true;
                        }
                    } elseif ($day == intval($date[0])) {
                        $event_show = true;
                    }
                }
            }

            $date_format = str_pad($day, 2, '0', STR_PAD_LEFT) . '.' . str_pad($month, 2, '0', STR_PAD_LEFT);
            if ($event_show) {
                $out .= "<td class=\"calendar-day{$class} event\" data-value=\"{$date_format}\">{$day}</td>";
            } else {
                $out .= "<td class=\"calendar-day{$class}\" data-value=\"{$date_format}\">{$day}</td>";
            }

            if ($day_week == 6) {
                $out .= '</tr>';
                if (($days_counter + 1) != $days_month) {
                    $out .= '<tr>';
                }
                $day_week = -1;
            }
            $day_week++;
            $days_counter++;
        }

        $out .= '</tr></table></div>';
        return $out;
    }

    /**
     * Получение html-конструкции для всего календаря по указанному году
     * @param string $start начальная дата
     * @param string $end конечная дата
     * @param array $events массив с датами выходных
     * @return string
     */
    public static function getInterval($year, $events = [], $class = 'wrp')
    {
        $start = date("01.{$year->number}");
        $end = date("12.{$year->number}");

        $curent = explode('.', $start);
        $curent[0] = intval($curent[0]);

        $end = explode('.', $end);
        $end[0] = intval($end[0]);

        $begin = true;
        $out = "<div class=\"calendar-{$class}\" data-id=\"{$year->id}\">";
        do {
            $out .= self::getMonth($curent[0], $curent[1], $events);

            if ($curent[0] == $end[0] && $curent[1] == $end[1]) {
                $begin = false;
            }

            $curent[0]++;
            if ($curent[0] == 13) {
                $curent[0] = 1;
                $curent[1]++;
            }
        } while ($begin == true);

        $out .= '</div>';
        return $out;
    }
}
