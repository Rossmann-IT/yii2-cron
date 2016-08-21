<?php
/**
 * @author mult1mate
 * @since 31.12.2015
 * @var \rossmann\cron\models\Task $task
 * @var array $methods
 */
use yii\bootstrap\ActiveForm;
use rossmann\cron\components\TaskInterface;

$this->title = Yii::t('cron', 'Edit task');
$this->params['breadcrumbs'][] = ['label' => 'Task Manager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('tasks_template');
$form        = ActiveForm::begin([]);
?>
<div class="col-lg-6">
    <div class="form-group">
        <label for="method"><?=Yii::t('cron', 'Methods')?></label>
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
    <?= $form->field($task, 'command')->textInput(['placeholder' => 'Controller::method']) ?>
    <?php $statuses = [
        TaskInterface::TASK_STATUS_ACTIVE   => Yii::t('cron', 'Active'),
        TaskInterface::TASK_STATUS_INACTIVE => Yii::t('cron', 'Inactive'),
    ];
    if ($task->getId()) {
        $statuses[TaskInterface::TASK_STATUS_DELETED] = Yii::t('cron', 'Deleted');
    }
    ?>
    <?= $form->field($task, 'status')->dropDownList($statuses) ?>
    <?= $form->field($task, 'comment') ?>

    <button type="submit" class="btn btn-primary"><?=Yii::t('cron', 'Save')?></button>

</div>
<div class="col-lg-6">
    <div class="form-group">
        <label for="times"><?=Yii::t('cron', 'Predefined intervals')?></label>
        <select class="form-control" id="times">
            <option></option>
            <option value="* * * * *"><?=Yii::t('cron', 'Minutely')?></option>
            <option value="0 * * * *"><?=Yii::t('cron', 'Hourly')?></option>
            <option value="0 0 * * *"><?=Yii::t('cron', 'Daily')?></option>
            <option value="0 0 * * 0"><?=Yii::t('cron', 'Weekly')?></option>
            <option value="0 0 1 * *"><?=Yii::t('cron', 'Monthly')?></option>
            <option value="0 0 1 1 *"><?=Yii::t('cron', 'Yearly')?></option>
        </select>
    </div>
    <?= $form->field($task, 'time')->textInput(['placeholder' => '* * * * *']) ?>
    <pre>
*    *    *    *    *
-    -    -    -    -
|    |    |    |    |
|    |    |    |    |
|    |    |    |    +----- <?=Yii::t('cron', 'day of week (0 - 7) (Sunday=0 or 7)')."\n"?>
|    |    |    +---------- <?=Yii::t('cron', 'month (1 - 12)')."\n"?>
|    |    +--------------- <?=Yii::t('cron', 'day of month (1 - 31)')."\n"?>
|    +-------------------- <?=Yii::t('cron', 'hour (0 - 23)')."\n"?>
+------------------------- <?=Yii::t('cron', 'min (0 - 59)')."\n"?>
    </pre>
    <h4><?=Yii::t('cron', 'Next runs')?></h4>
    <div id="dates_list"></div>
</div>

<?php ActiveForm::end(); ?>
<div class="clearfix"></div>
