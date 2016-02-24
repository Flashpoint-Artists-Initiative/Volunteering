<?php

use yii\db\Schema;
use yii\db\Migration;

class m160224_034811_add_timestamp_to_participant extends Migration
{
    public function up()
    {
		$this->addColumn('participant', 'timestamp', $this->integer());

    }

    public function down()
    {
		$this->dropColumn('participant', 'timestamp');
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
