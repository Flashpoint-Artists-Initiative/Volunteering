<?php

use yii\db\Schema;
use yii\db\Migration;

class m160111_195021_shift_min_max extends Migration
{
    public function up()
    {
		$this->addColumn('shift', 'min_needed', $this->integer());
		$this->addColumn('shift', 'max_needed', $this->integer());
		$this->dropColumn('shift', 'participant_num');
    }

    public function down()
    {
        return false;
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
