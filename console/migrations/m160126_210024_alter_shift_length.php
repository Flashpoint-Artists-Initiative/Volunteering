<?php

use yii\db\Schema;
use yii\db\Migration;

class m160126_210024_alter_shift_length extends Migration
{
    public function up()
    {
		$this->alterColumn('shift', 'length', $this->float()->notNull());

    }

    public function down()
    {
		$this->alterColumn('shift', 'length', $this->integer()->notNull());
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
