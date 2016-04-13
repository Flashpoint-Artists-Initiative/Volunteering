<?php

use yii\db\Schema;
use yii\db\Migration;

class m150320_222611_add_name_to_team extends Migration
{
    public function up()
    {
		$this->addColumn('team', 'name', Schema::TYPE_STRING . ' NOT NULL');
    }

    public function down()
    {
		$this->dropColumn('team', 'name');
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
