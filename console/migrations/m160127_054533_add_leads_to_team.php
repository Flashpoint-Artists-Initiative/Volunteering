<?php

use yii\db\Schema;
use yii\db\Migration;

class m160127_054533_add_leads_to_team extends Migration
{
    public function up()
    {
		$this->addColumn('team', 'leads', $this->string());
    }

    public function down()
    {
		$this->dropColumn('team', 'leads');
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
