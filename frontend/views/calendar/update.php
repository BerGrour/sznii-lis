<?php

/** @var yii\web\View $this */

use common\models\CalendarYear;
use yii\helpers\Html;

/** @var common\models\CalendarYear $year */

$this->title = "Редактирование календаря {$year->number}";
$this->params["breadcrumbs"][] = ['label' => "Производственный календарь {$year->number}", 'url' => ['view', 'year' => $year->number]];
$this->params["breadcrumbs"][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="calendar-update">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('calendar/update')) { ?>
        <p>
            <?= Html::a('Закончить изменение', ['view', 'year' => $year->number], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php } ?>

    <?= CalendarYear::getInterval(
        $year,
        $events,
        'redact'
    ); ?>

</div>