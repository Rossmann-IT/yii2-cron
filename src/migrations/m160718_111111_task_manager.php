<?php

use yii\db\Migration;

/**
 * Updates tasks table
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
        $this->addColumn('tasks', 'locked', $this->boolean()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('tasks', 'locked');
    }
}
