<?php
/**
 * @author mult1mate
 * @since 31.12.2015
 * @var string $content
 */
use yii\helpers\Html;
use yii\helpers\Url;

$menu = [
    'index' => Yii::t('cron', 'Tasks list'),
    'task-edit' => Yii::t('cron', 'Add new/edit task'),
    'task-log' => Yii::t('cron', 'Logs'),
    'export' => Yii::t('cron', 'Import/Export'),
    'tasks-report' => Yii::t('cron', 'Report'),
];

?>
<div class="col-lg-10">

    <h2><?= Yii::t('cron', 'Cron tasks manager') ?></h2>

    <?php foreach (Yii::$app->session->getAllFlashes(true) as $key => $message) : ?>
        <div class="alert alert-<?= $key ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <ul class="nav nav-tabs">
        <?php foreach ($menu as $route => $text):
            $class = Yii::$app->controller->action->id == $route ? 'active' : '';
            ?>
            <li class="<?= $class ?>"><?= Html::a($text, Url::toRoute($route)) ?></li>
        <?php endforeach; ?>
    </ul>
    <br>
    <?= isset($content) ? $content : '' ?>
</div>
