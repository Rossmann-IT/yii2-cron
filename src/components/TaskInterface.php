<?php
namespace rossmann\cron\components;

/**
 * Interface TaskInterface
 * Common interface to handle tasks
 * @author  mult1mate
 * @author  rossmann-it
 * @since 20.12.2015
 */
interface TaskInterface
{
    const TASK_STATUS_ACTIVE = 'active';
    const TASK_STATUS_INACTIVE = 'inactive';
    const TASK_STATUS_DELETED = 'deleted';

    /**
     * Returns tasks with given id
     *
     * @param int $taskId
     *
     * @return TaskInterface
     */
    public static function taskGet($taskId);

    /**
     * Returns array of all tasks
     * @return array
     */
    public static function getAll();

    /**
     * Creates new task object and returns it
     * @return TaskInterface
     */
    public static function createNew();

    /**
     * Deletes the task
     * @return mixed
     */
    public function taskDelete();

    /**
     * Saves the task
     * @return mixed
     */
    public function taskSave();

    /**
     * Creates new task run object for current task and returns it
     * @return TaskRunInterface
     */
    public function createTaskRun();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTime();

    /**
     * @param string $time
     */
    public function setTime($time);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * @return string
     */
    public function getCommand();

    /**
     * @param string $command
     */
    public function setCommand($command);

    /**
     * @return string
     */
    public function getTs();

    /**
     * @param string $timestamp
     */
    public function setTs($timestamp);

    /**
     * @return string
     */
    public function getTsUpdated();

    /**
     * @param string $timestamp
     */
    public function setTsUpdated($timestamp);

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * sets the locked flag to 0 in the database
     */
    public function releaseLock();

    /**
     * @param int|bool $locked
     */
    public function setLocked($locked);

    /**
     * @return bool
     * @throws \Exception
     */
    public function acquireLock();

}
