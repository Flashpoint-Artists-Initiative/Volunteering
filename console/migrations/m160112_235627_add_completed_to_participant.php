<?php

use yii\db\Schema;
use yii\db\Migration;

class m160112_235627_add_completed_to_participant extends Migration
{
    public function up()
    {
		$this->addColumn('participant', 'completed', $this->boolean());

    }

    public function down()
    {
		$this->dropColumn('participant', 'completed');
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
