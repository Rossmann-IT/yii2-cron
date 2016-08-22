<?php
/**
 * @author mult1mate
 * @since 21.12.2015
 * @var array $runs
 */
$this->title = Yii::t('cron', 'Run list');
$this->params['breadcrumbs'][] = ['label' => 'Task Manager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('tasks_template');
?>
<table class="table table-bordered">
    <tr>
        <th><?= Yii::t('cron', 'ID') ?></th>
        <th><?= Yii::t('cron', 'Task ID') ?></th>
        <th><?= Yii::t('cron', 'Command') ?></th>
        <th><?= Yii::t('cron', 'Status') ?></th>
        <th><?= Yii::t('cron', 'Time') ?></th>
        <th><?= Yii::t('cron', 'Started') ?></th>
        <th></th>
    </tr>
    <?php foreach ($runs as $r):
        /**
         * @var \rossmann\cron\models\TaskRun $r
         */
        ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['task_id'] ?> </td>
            <td><?= $r['command'] ?></td>
            <td><?= $r['status'] ?></td>
            <td><?= sprintf('%.02f', $r['execution_time']) ?>s</td>
            <td><?= $r['ts'] ?></td>
            <td>
                <?php if (!empty($r['output'])): ?>
                    <a href="#output_modal" data-task-run-id="<?= $r['id'] ?>"
                       data-toggle="modal" data-target="#output_modal"
                       class="show_output"><?= Yii::t('cron', 'Show output') ?></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="modal fade" tabindex="-1" role="dialog" id="output_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= Yii::t('cron', 'Task run output') ?></h4>
            </div>
            <div class="modal-body">
                <pre id="output_container"><?= Yii::t('cron', 'Loading...') ?></pre>
            </div>
        </div>
    </div>
</div>
