<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\models\CalendarYear;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

$org_id = Yii::$app->user->identity->organization_id ?? null;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
    <script defer src="https://use.fontawesome.com/releases/v6.5.2/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandOptions' => ['title' => 'Главная'],
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);
        $menuItems = [
            ['label' => 'Разделы', 'items' => [
                ['label' => 'Партии проб', 'url' => ['/batch/index'], 'visible' => Yii::$app->user->can('batch/see')],
                ['label' => 'Исследования', 'url' => ['/service/index'], 'visible' => Yii::$app->user->can('service/see')],
                ['label' => 'Организации', 'url' => ['/organization/index'], 'visible' => Yii::$app->user->can('organization/see')],
                ['label' => 'Оплата', 'url' => ['/payment/index'], 'visible' => Yii::$app->user->can('payment/see')],
                ['label' => 'Отделы', 'url' => ['/departament/index'], 'visible' => Yii::$app->user->can('departament/see')],
                ['label' => 'Организация', 'url' => ['client/index', 'org_id' => $org_id], 'visible' => Yii::$app->user->can('client')],
                ['label' => 'Каталог', 'url' => ['/price-list/index'], 'visible' => Yii::$app->user->can('price_list/see')],
            ], 'visible' => !Yii::$app->user->isGuest],
        ];
        $menuItems[] = ['label' => 'Администрирование', 'items' => [
            ['label' => 'Пользователи', 'url' => ['/user/index']],
            ['label' => 'Сотрудники', 'url' => ['/staff/index']],
        ], 'visible' => Yii::$app->user->can('manageRoles')];

        $menuItems[] = [
            'label' => 'Календарь',
            'items' => CalendarYear::arrayYears(),
            'visible' => Yii::$app->user->can('calendar/see')
        ];

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menuItems,
        ]);
        if (Yii::$app->user->isGuest) {
            echo Html::tag('div', Html::a('Вход', ['/site/login'], ['class' => ['btn btn-link login text-decoration-none']]), ['class' => ['d-flex']]);
        } else {
            echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout text-decoration-none']
                )
                . Html::endForm();
        }
        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'encodeLabels' => false
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <p class="float-start">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
            <p class="float-end">
                <a href="http://www.vscc.ac.ru/" target="_blank">Вологодский научный центр Российской академии наук</a>
            </p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
