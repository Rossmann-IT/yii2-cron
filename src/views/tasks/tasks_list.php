<?php
/**
 * @author mult1mate
 * @since 21.12.2015
 * @var array $tasks
 * @var array $methods
 */
use rossmann\cron\components\TaskInterface;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('cron', 'Task list');
$this->params['breadcrumbs'][] = ['label' => 'Task Manager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('tasks_template');
?>
<table class="table table-bordered">
    <tr>
        <th>
            <input type="checkbox" id="select_all" title="select all">
        </th>
        <th><?= Yii::t('cron', 'ID') ?></th>
        <th><?= Yii::t('cron', 'Time') ?></th>
        <th><?= Yii::t('cron', 'Command') ?></th>
        <th><?= Yii::t('cron', 'Status') ?></th>
        <th><?= Yii::t('cron', 'Locked') ?></th>
        <th><?= Yii::t('cron', 'Comment') ?></th>
        <th><?= Yii::t('cron', 'Created') ?></th>
        <th><?= Yii::t('cron', 'Updated') ?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <?php
    foreach ($tasks as $t):
        /**
         * @var \rossmann\cron\models\Task $t
         */
        $statusClass = (TaskInterface::TASK_STATUS_ACTIVE == $t->status) ? '' : 'text-danger';
        ?>
        <tr>
            <td>
                <input type="checkbox" value="<?= $t->id ?>" class="task_checkbox" title="select task">
            </td>
            <td><?= $t->id ?></td>
            <td><?= $t->time ?></td>
            <td><?= $t->command ?></td>
            <td class="<?= $statusClass ?>"><?= $t->status ?></td>
            <td><?= $t->locked ?></td>
            <td><?= $t->comment ?></td>
            <td><?= $t->ts ?></td>
            <td><?= $t->ts_updated ?></td>
            <td>
                <?= Html::a(Yii::t('cron', 'Edit'), ['task-edit', 'id' => $t->id]); ?>
            </td>
            <td>
                <?= Html::a(Yii::t('cron', 'Log'), ['task-log']); ?>
            </td>
            <td>
                <?= Html::a(Yii::t('cron', 'Run'), '', ['id' => $t->id, 'class' => 'run_task']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<form class="form-inline" action="<?= Url::toRoute(['tasks-update']) ?>">
    <div class="form-group">
        <label for="mode"><?= Yii::t('cron', 'With selected') ?></label>
        <select class="form-control" id="mode" name="mode">
            <option value="Enable"><?= Yii::t('cron', 'Enable') ?></option>
            <option value="Disable"><?= Yii::t('cron', 'Disable') ?></option>
            <option value="Delete"><?= Yii::t('cron', 'Delete') ?></option>
            <option value="Run"><?= Yii::t('cron', 'Run') ?></option>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" value="<?= Yii::t('cron', 'Apply') ?>" class="btn btn-primary" id="execute_action">
    </div>
</form>
<form class="form-inline">
    <h3><?= Yii::t('cron', 'Run custom task') ?></h3>
    <div class="form-group">
        <label for="method"><?= Yii::t('cron', 'Methods') ?></label>
        <select class="form-control" id="method">
            <option></option>
            <?php foreach ($methods as $class => $classMethods): ?>
                <optgroup label="<?= $class ?>">
                    <?php foreach ($classMethods as $method): ?>
                        <option value="<?= $class . '::' . $method . '()' ?>"><?= $method ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="command"><?= Yii::t('cron', 'Command') ?></label>
        <input type="text" class="form-control" id="command" name="command" placeholder="Controller::method"
               style="width: 300px;">
    </div>
    <input type="submit" value="<?= Yii::t('cron', 'Run') ?>" class="btn btn-primary" id="run_custom_task">
</form>
<div id="output_section" style="display: none;">
    <h3><?= Yii::t('cron', 'Task output') ?></h3>
    <pre id="task_output_container"></pre>
</div>
