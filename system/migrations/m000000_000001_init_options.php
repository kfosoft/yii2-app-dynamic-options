<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Migration for create options table.
 *
 * @version 20.06
 * @author (c) KFOSOFT <kfosoftware@gmail.com>
 */
class m000000_000001_init_options extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): void
    {
        $this->createTable('option', [
            'id' => Schema::TYPE_PK,
            'key' => Schema::TYPE_STRING . ' NOT NULL',
            'value' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): void
    {
        $this->dropTable('option');
    }
}
