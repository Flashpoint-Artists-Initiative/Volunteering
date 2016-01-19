<?php

use yii\db\Schema;
use yii\db\Migration;

class m160115_193813_add_email_to_user extends Migration
{
    public function up()
    {
		$this->addColumn('user', 'email', $this->string());

    }

    public function down()
    {
		$this->dropColumn('user', 'email');
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
