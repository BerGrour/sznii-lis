<?php

/** @var yii\web\View $this */

use common\models\CalendarYear;
use yii\helpers\Html;

/** @var yii\db\ActiveQuery $dates */
/** @var common\models\CalendarYear $year */
/** @var array $events Массив с выходными днями */

$this->title = "Календарь на {$year->number}";
$this->params["breadcrumbs"][] = "Производственный календарь";
$this->params["breadcrumbs"][] = $year->number;
\yii\web\YiiAsset::register($this);
?>

<div class="calendar-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('calendar/update')) { ?>
        <p>
            <?= Html::a('Изменить', ['update', 'year' => $year->number], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php } ?>

    <?= CalendarYear::getInterval(
        $year,
        $events
    ); ?>
</div>