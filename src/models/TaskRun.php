<?php
namespace rossmann\cron\models;

use rossmann\cron\components\TaskRunInterface;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @author mult1mate
 * @author rossmann-it
 * @since 20.12.2015
 *
 * @property int $id
 * @property int $task_id
 * @property string $status
 * @property string $output
 * @property int $execution_time
 * @property string $ts
 */
class TaskRun extends ActiveRecord implements TaskRunInterface
{
    /**
     * @param int $taskId
     * @param int $count
     * @return array
     */
    public static function getLastRuns($taskId = null, $count = 100)
    {
        $db = (new Query())
            ->select('task_runs.*, tasks.command')
            ->from(self::tableName())
            ->join('LEFT JOIN', 'tasks', 'tasks.id = task_runs.task_id')
            ->orderBy('task_runs.id desc')
            ->limit($count);
        if ($taskId) {
            $db->where('task_runs.task_id=:task_id', [':id' => $taskId]);
        }

        return $db->all();
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%task_runs}}';
    }

    /**
     * @param int $taskId
     * @return null|TaskRun
     */
    public static function getLast($taskId)
    {
        $last = self::find()
            ->where('task_id = :task_id', [':task_id' => $taskId])
            ->orderBy('id DESC')->one();

        return $last;
    }

    /**
     * @inheritdoc
     */
    public function saveTaskRun()
    {
        return $this->save();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->task_id = $taskId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getExecutionTime()
    {
        return $this->execution_time;
    }

    /**
     * @param int $executionTime
     */
    public function setExecutionTime($executionTime)
    {
        $this->execution_time = $executionTime;
    }

    /**
     * @return string
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @param string $timestamp
     */
    public function setTs($timestamp)
    {
        $this->ts = $timestamp;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }
}
