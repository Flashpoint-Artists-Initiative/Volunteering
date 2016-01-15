<?php

use yii\db\Schema;
use yii\db\Migration;

class m160114_161121_add_user_table extends Migration
{
    public function up()
    {
		$this->createTable('user', [
			'id' => $this->primaryKey(),
			'real_name' => $this->string(),
			'display_name' => $this->string(),
		]);
    }

    public function down()
    {
		$this->dropTable('user');
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
