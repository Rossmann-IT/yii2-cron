<?php
/**
 * @author mult1mate
 * @since 31.12.2015
 */
$this->title = Yii::t('cron', 'Import/Export');
$this->params['breadcrumbs'][] = ['label' => 'Task Manager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('tasks_template');
?>
<div class="col-lg-6">
    <h2>Import</h2>
    <form method="post" id="parse_crontab_form">
        <div class="form-group">
            <?= Yii::t('cron', 'Example') ?>
            <pre>* * * * * cd /some/path; /usr/bin/php script.php \name\space\ClassName actionName 2>&1 > /dev/null</pre>
            <label for="crontab"><?= Yii::t('cron', 'Paste crontab content') ?></label><br>
            <textarea class="form-control" name="crontab" id="crontab"></textarea>
        </div>
        <div class="form-group">
            <input type="submit" value="Parse" class="btn btn-primary">
        </div>
    </form>
    <div id="parse_result">
    </div>
</div>

<div class="col-lg-6">
    <h2>Export</h2>
    <form id="export_form">
        <div class="form-group">
            <label class="control-label" for="php"><?= Yii::t('cron', 'Path to PHP') ?></label><br>
            <input type="text" class="form-control" name="php" id="php" value="php">
        </div>
        <div class="form-group">
            <label class="control-label" for="folder"><?= Yii::t('cron', 'Path to folder') ?></label><br>
            <input type="text" class="form-control" name="folder" id="folder" value="">
        </div>
        <div class="form-group">
            <label class="control-label" for="file"><?= Yii::t('cron', 'php file') ?></label><br>
            <input type="text" class="form-control" name="file" id="file" value="index.php">
        </div>
        <div class="form-group">
            <input type="submit" value="Export" class="btn btn-primary">
        </div>
    </form>
    <br>
    <pre id="export_result">

    </pre>
</div>
<div class="clearfix"></div>
