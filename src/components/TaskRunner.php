<?php
namespace rossmann\cron\components;

use Cron\CronExpression;
use rossmann\cron\models\TaskRun;

/**
 * Class TaskRunner
 * Runs tasks and handles time expression
 * @author  mult1mate
 * @author  rossmann-it
 * @since 07.02.2016
 */
class TaskRunner
{
    /**
     * Runs active tasks if current time matches with time expression
     *
     * @param array $tasks
     */
    public static function checkAndRunTasks($tasks)
    {
        $invocationTimestamp = time();
        $invocationDatetime = date('Y-m-d H:i:00');

        foreach ($tasks as $task) {
            /**
             * @var TaskInterface $task
             */
            if (TaskInterface::TASK_STATUS_ACTIVE != $task->getStatus()) {
                continue;
            }

            $cron = CronExpression::factory($task->getTime());
            $lastRun = TaskRun::getLast($task->getId());

            if ($cron->isDue($invocationDatetime)) {
                $runTask = false;
                // task never ran before
                if (empty($lastRun)) {
                    $runTask = true;
                } else {
                    // task ran before, but not since this script was started
                    if ($invocationTimestamp > strtotime($lastRun->getTs())) {
                        $runTask = true;
                    } else {
                        static::log('info', 'Task with ID ' . $task->getId() . ' ran since this script was started');
                        static::log('info', 'Task with ID ' . $task->getId() . ', invocation timestamp: ' . $invocationTimestamp
                            . '(' . date('Y-m-d H:i:s', $invocationTimestamp) . '), last run timestamp:  ' . strtotime($lastRun->getTs()). '(' . $lastRun->getTs() . ')');
                    }
                }
                if (true === $runTask) {
                    static::log('info', 'Task with ID ' . $task->getId() . ' is due');
                    static::runTask($task);
                }
            } else {
                // The task is not due exactly now, but maybe another long running task from a previous invocation
                // of the TaskRunner is blocking the execution queue. Check if the task was run on or since its last due date:
                // if the task has never run before, we fake a last execution timestamp in the future,
                // because we do not want all jobs to run when the system is deployed for the first time
                $lastRunTs = !empty($lastRun) ? strtotime($lastRun->getTs()) : $invocationTimestamp + 1;
                $lastDue = $cron->getPreviousRunDate($invocationDatetime);
                if ($lastRunTs < $lastDue->format('U')) {
                    static::log('info', 'Task with ID ' . $task->getId() . ' was not executed on its last due date ('
                        . $lastDue->format('Y-m-d H:i:s') . ") or since then. Executing it now.");
                    static::runTask($task);
                }
            }
        }
    }

    /**
     * Runs task and returns output
     *
     * @param TaskInterface $task
     *
     * @return string
     */
    public static function runTask($task)
    {
        $result = $task->acquireLock();
        if ($result) {
            $run = $task->createTaskRun();
            $run->setTs(date('Y-m-d H:i:s'));
            $run->setStatus(TaskRunInterface::RUN_STATUS_STARTED);
            $run->saveTaskRun();
        } else {
            $lastRun = TaskRun::getLast($task->getId());
            if (!$lastRun) {
                $errorMessage = 'Task with ID ' . $task->getId() . ' cannot be run because a lock could not be acquired, '
                    . 'but no previous run could be found';
                static::log('error', $errorMessage);
            } elseif ($lastRun->status == TaskRun::RUN_STATUS_STARTED) {
                if ($lastRun->getTs() < date('Y-m-d H:i:s', time() - 3600)) {
                    $errorMessage = 'Task with ID ' . $task->getId() . ' cannot be run because a lock could not be acquired, '
                        . 'and the last run is in "running" state since more than one hour. Check if manual action is required.';
                    static::log('error', $errorMessage);
                } else {
                    $errorMessage = 'Task with ID ' . $task->getId() . ' cannot be run because it is already running.';
                    static::log('info', $errorMessage);
                }
            } elseif ($lastRun->status == TaskRun::RUN_STATUS_ERROR) {
                $errorMessage = 'Task with ID ' . $task->getId() . ' cannot be run because a lock could not be acquired, '
                    . 'and the last run ended in an error state. Check if the lock has to be released manually.';
                static::log('error', $errorMessage);
            } else {
                $errorMessage = 'Task with ID ' . $task->getId() . ' cannot be run because a lock could not be acquired '
                    . 'although the last run is marked as completed. If you see this message only once, the reason might be '
                    . 'a race condition, in that case no action is required.';
                static::log('error', $errorMessage);
            }

            return $errorMessage;
        }

        ob_start();
        $timeBegin = microtime(true);

        try {
            $result = static::parseAndRunCommand($task->getCommand());
            if (!$result) {
                $runFinalStatus = TaskRunInterface::RUN_STATUS_ERROR;
            } else {
                $runFinalStatus = TaskRunInterface::RUN_STATUS_COMPLETED;
            }
        } catch (\Exception $e) {
            $runFinalStatus = TaskRunInterface::RUN_STATUS_ERROR;
            static::log('error',
                'Exception while running task with ID ' . $task->getId() . ': ' . get_class($e) . PHP_EOL . $e->getMessage());
        }

        $output = ob_get_clean();
        $run->setOutput($output);

        $run->setStatus($runFinalStatus);

        $timeEnd = microtime(true);
        $time = round(($timeEnd - $timeBegin), 2);
        $run->setExecutionTime($time);

        try {
            $run->saveTaskRun();
        } catch (\Exception $e) {
            // If this process had to wait a long time for the task to complete, the database server may have
            // closed the connection. For example: Oracle ORA-03113: end-of-file on communication channel
            // If this happens, we try to open a new connection
            \Yii::$app->db->close();
            if (property_exists(\Yii::$app->db, 'forceReconnect')) {
                // if PHP just reuses the defunct connection (happens with Oracle), you have to extend yii\db\Connection
                // and implement a mechanism in createPdoInstance() which forces a new connection
                \Yii::$app->db->forceReconnect = true;
            }
            \Yii::$app->db->open();
            $run->saveTaskRun();
        }

        $task->releaseLock();

        return $output;
    }

    /**
     * @param string $level
     * @param string $message
     */
    protected static function log($level, $message)
    {
        \Yii::$level($message);
    }

    /**
     * Parses given command, creates new class object and calls its method via call_user_func_array
     *
     * @param string $command
     *
     * @return mixed
     */
    public static function parseAndRunCommand($command)
    {
        try {
            list($class, $method, $args) = TaskManager::parseCommand($command);
            if (!class_exists($class)) {
                TaskLoader::loadController($class);
            }

            $reflection = new \ReflectionClass($class);
            if ($reflection->getParentClass()->name === 'yii\console\Controller') {
                $obj = \Yii::createObject($class, ['crontask', \Yii::$app->controller->module->id]);
            } else {
                $obj = new $class;
            }

            if (!method_exists($obj, $method)) {
                throw new TaskManagerException('method ' . $method . ' not found in class ' . $class);
            }
            $result = call_user_func_array([$obj, $method], $args);

            return $result;

        } catch (\Exception $e) {
            static::log('error', 'Exception while executing the task command: ' . get_class($e) . ': '
                . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Returns next run dates for time expression
     *
     * @param string $time
     * @param int $count
     *
     * @return array
     */
    public static function getRunDates($time, $count = 10)
    {
        try {
            $cron = CronExpression::factory($time);
            $dates = $cron->getMultipleRunDates($count);
        } catch (\Exception $e) {
            return [];
        }

        return $dates;
    }

}
