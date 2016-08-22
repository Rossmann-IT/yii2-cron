<?php
/**
 * @author mult1mate
 * @since 06.02.2016
 */
namespace rossmann\cron\commands;

use rossmann\cron\components\TaskRunner;
use rossmann\cron\models\Task;
use yii\console\Controller;

class CronController extends Controller
{
    public function actionCheckTasks()
    {
        TaskRunner::checkAndRunTasks(Task::getAll());
    }
}
