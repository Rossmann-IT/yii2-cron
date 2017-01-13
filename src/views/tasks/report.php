<?php
/**
 * @author mult1mate
 * @since 05.01.2016
 * @var string $dateBegin
 * @var string $dateEnd
 * @var array $report
 */
$this->title = Yii::t('cron', 'Report');
$this->params['breadcrumbs'][] = ['label' => 'Task Manager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('tasks_template');
?>
<form class="form-inline" action="">
    <div class="form-group">
        <label for="date_begin" class="control-label"><?= Yii::t('cron', 'Date begin') ?></label>
        <input type="date" value="<?= $dateBegin ?>" name="date_begin" id="date_begin" class="form-control">
    </div>
    <div class="form-group">
        <label for="date_end" class="control-label"><?= Yii::t('cron', 'Date end') ?></label>
        <input type="date" value="<?= $dateEnd ?>" name="date_end" id="date_end" class="form-control">
    </div>
    <div class="form-group">
        <input type="hidden" value="tasksReport" name="r">
        <input type="submit" value="Update" class="btn btn-primary">
    </div>
</form>

<table class="table">
    <tr>
        <th><?= Yii::t('cron', 'ID') ?></th>
        <th><?= Yii::t('cron', 'Task') ?></th>
        <th><?= Yii::t('cron', 'Avg. time') ?></th>
        <th><?= Yii::t('cron', 'Min. time') ?></th>
        <th><?= Yii::t('cron', 'Max. time') ?></th>
        <th><?= Yii::t('cron', 'Started') ?></th>
        <th><?= Yii::t('cron', 'Success') ?></th>
        <th><?= Yii::t('cron', 'Error') ?></th>
        <th><?= Yii::t('cron', 'All') ?></th>
    </tr>
    <?php foreach ($report as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['command'] ?></td>
            <td><?= round($row['time_avg'], 2) ?>s</td>
            <td><?= round($row['time_min'], 2) ?>s</td>
            <td><?= round($row['time_max'], 2) ?>s</td>
            <td><?= $row['started'] ?></td>
            <td><?= $row['completed'] ?></td>
            <td><?= $row['error'] ?></td>
            <th><?= $row['runs'] ?></th>
        </tr>
    <?php endforeach; ?>
</table>
