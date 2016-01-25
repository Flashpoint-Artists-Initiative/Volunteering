<?php

use yii\db\Schema;
use yii\db\Migration;

class m160121_194845_add_password_and_burn_name_to_user extends Migration
{
    public function up()
    {
		$this->createTable('user', [
			'id' => $this->primaryKey(),
			'username' => $this->string()->notNull(),
			'password' => $this->string()->notNull(),
			'auth_key' => $this->string(),
			'email' => $this->string()->notNull(),
			'real_name' => $this->string()->notNull(),
			'burn_name' => $this->string(),
			'data' => $this->text(),
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
