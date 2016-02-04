<?php

use yii\db\Schema;
use yii\db\Migration;

class m160127_222407_add_fields_to_requirement extends Migration
{
    public function up()
    {
		$this->addColumn('requirement', 'error_message', $this->string());
		$this->addColumn('requirement', 'team', $this->string());
    }

    public function down()
    {
		$this->dropColumn('requirement', 'error_message');
		$this->dropColumn('requirement', 'team');
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
