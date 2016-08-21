<?php
namespace rossmann\cron\components;

use Cron\CronExpression;

/**
 * Class TaskManager
 * Contains methods for manipulate TaskInterface objects
 * @author  mult1mate
 * @since 20.12.2015
 */
class TaskManager
{
    const CRON_LINE_REGEXP = '/(#?)(.*)cd.*php.*\.php\s+([\\\\\w\d-_]+)\s+([\w\d-_]+)\s*([\d\w-_\s]+)?(\d[\d>&\s]+)(.*)?/i';

    /**
     * Edit and save TaskInterface object
     *
     * @param TaskInterface $task
     * @param string $time
     * @param string $command
     * @param string $status
     * @param string $comment
     *
     * @return TaskInterface
     */
    public static function editTask(
        $task,
        $time,
        $command,
        $status = TaskInterface::TASK_STATUS_ACTIVE,
        $comment = null
    ) {
        if (!$validatedCommand = self::validateCommand($command)) {
            return $task;
        }
        $task->setStatus($status);
        $task->setCommand($validatedCommand);
        $task->setTime($time);
        if (isset($comment)) {
            $task->setComment($comment);
        }

        $task->setTsUpdated(date('Y-m-d H:i:s'));

        $task->taskSave();

        return $task;
    }

    /**
     * Checks if the command is correct and removes spaces
     *
     * @param string $command
     *
     * @return string|false
     */
    public static function validateCommand($command)
    {
        try {
            list($class, $method, $args) = self::parseCommand($command);
        } catch (TaskManagerException $e) {
            return false;
        }
        $args = array_map(function ($elem) {
            return trim($elem);
        }, $args);

        return $class . '::' . $method . '(' . trim(implode(',', $args), ',') . ')';
    }

    /**
     * Parses command and returns an array which contains class, method and arguments of the command
     *
     * @param string $command
     *
     * @return array
     * @throws TaskManagerException
     */
    public static function parseCommand($command)
    {
        if (preg_match('/([@\w\\\\]+)::(\w+)\((.*)\)/', $command, $match)) {
            $params = explode(',', $match[3]);
            // trim params and strip quotes
            foreach ($params as &$param) {
                $param = trim($param);
                if (empty($param)) {
                    continue;
                }
                if (($param[0] == "'" AND substr($param, -1) == "'") OR ($param[0] == '"' AND substr($param,
                            -1) == '"')
                ) {
                    $param = substr($param, 1, -1);
                }
            }
            if ((1 == count($params)) && ('' == $params[0])) {
                // prevents to pass an empty string
                $params[0] = null;
            }

            return [
                $match[1],
                $match[2],
                $params,
            ];
        }

        throw new TaskManagerException('Command not recognized');
    }

    /**
     * Parses each line of crontab content and creates new TaskInterface objects
     *
     * @param string $cron
     * @param TaskInterface $taskClass
     *
     * @return array
     */
    public static function parseCrontab($cron, $taskClass)
    {
        $cronArray = explode(PHP_EOL, $cron);
        $tasks = [];
        foreach ($cronArray as $cronElement) {
            $cronElement = trim($cronElement);
            if (empty($cronElement)) {
                continue;
            }
            $task = [$cronElement];
            if (preg_match(self::CRON_LINE_REGEXP, $cronElement, $matches)) {
                try {
                    CronExpression::factory($matches[2]);
                } catch (\Exception $e) {
                    $task[1] = \Yii::t('cron', 'Time expression is not valid');
                    $task[2] = $matches[2];
                    $tasks[] = $task;
                    continue;
                }
                $taskObject = self::createTaskWithCrontabLine($taskClass, $matches);

                $task[1] = \Yii::t('cron', 'Saved');
                $task[2] = $taskObject;

                $comment = null;
            } elseif (preg_match('/#([\w\d\s]+)/i', $cronElement, $matches)) {
                $comment = trim($matches[1]);
                $task[1] = \Yii::t('cron', 'Comment');
                $task[2] = $comment;
            } else {
                $task[1] = \Yii::t('cron', 'Not matched');
            }
            $tasks[] = $task;
        }

        return $tasks;
    }

    /**
     * Creates new TaskInterface object from parsed crontab line
     *
     * @param TaskInterface $taskClass
     * @param array $matches
     * @param string $comment
     *
     * @return TaskInterface
     */
    protected static function createTaskWithCrontabLine($taskClass, $matches, $comment = '')
    {
        $task = $taskClass::createNew();
        $task->setTime(trim($matches[2]));
        $arguments = str_replace(' ', ',', trim($matches[5]));
        $command = ucfirst($matches[3]) . '::' . $matches[4] . '(' . $arguments . ')';
        $task->setCommand($command);
        if (!empty($comment)) {
            $task->setComment($comment);
        }
        //$output = $matches[7];
        $status = empty($matches[1]) ? TaskInterface::TASK_STATUS_ACTIVE : TaskInterface::TASK_STATUS_INACTIVE;
        $task->setStatus($status);
        $task->setTs(date('Y-m-d H:i:s'));
        $task->taskSave();

        return $task;
    }

    /**
     * Formats task for export into crontab file
     *
     * @param TaskInterface $task
     * @param string $path
     * @param string $phpBin
     * @param string $inputFile
     *
     * @return string
     */
    public static function getTaskCrontabLine($task, $path, $phpBin, $inputFile)
    {
        $str = '';
        $comment = $task->getComment();
        if (!empty($comment)) {
            $str .= '#' . $comment;
        }
        if (TaskInterface::TASK_STATUS_ACTIVE != $task->getStatus()) {
            $str .= '#';
        }
        list($class, $method, $args) = self::parseCommand($task->getCommand());
        $execCmd = $phpBin . ' ' . $inputFile . ' ' . $class . ' ' . $method . ' ' . implode(' ', $args);
        $str .= $task->getTime() . ' cd ' . $path . '; ' . $execCmd . ' 2>&1 > /dev/null';

        return $str;
    }
}
