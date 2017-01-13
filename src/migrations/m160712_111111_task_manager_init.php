<?php

use yii\db\Migration;

/**
 * Initializes tasks tables
 * @author rossmann-it
 */
class m160712_111111_task_manager_init extends Migration
{

    /**
     * for Oracle you need to overwrite the typeMap in \yii\db\oci\QueryBuilder
     * to get an equivalent for AUTO_INCREMENT, for example
     * 'NUMBER(10) GENERATED BY DEFAULT ON NULL AS IDENTITY PRIMARY KEY'
     */
    public function safeUp()
    {
        $this->createTable('tasks', [
            'id' => $this->primaryKey(),
            'time' => $this->string(64)->notNull(),
            'command' => $this->string(256)->notNull(),
            'status' => $this->string(20)->notNull(),
            'comments' => $this->string(256),
            'ts' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ts_updated' => $this->timestamp()
        ]);

        $this->createTable('task_runs', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer(),
            'status' => $this->string(20)->notNull(),
            'execution_time' => $this->float()->notNull()->defaultValue(0.00),
            'ts' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'output' => $this->string(20000)
        ]);
        $this->createIndex('ix_task_runs_task_id', 'task_runs', 'task_id', false);
        $this->addForeignKey('fk_task_runs_tasks_id', 'task_runs', 'task_id', 'tasks', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('task_runs');
        $this->dropTable('tasks');
    }
}
