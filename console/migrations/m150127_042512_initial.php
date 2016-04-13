<?php

use yii\db\Schema;
use yii\db\Migration;

class m150127_042512_initial extends Migration
{
    public function up()
    {
		$this->createTable('team', [
			'id' => 'pk', 
			'description' => Schema::TYPE_TEXT,
			'contact' => Schema::TYPE_STRING,
			'event_id' => Schema::TYPE_INTEGER . ' NOT NULL',
		]);

		$this->createTable('shift', [
			'id' => 'pk',
			'team_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'title' => Schema::TYPE_STRING . ' NOT NULL',
			'length' => Schema::TYPE_INTEGER . ' NOT NULL',
			'start_time' => Schema::TYPE_INTEGER . ' NOT NULL',
			'participant_num' => Schema::TYPE_INTEGER . ' NOT NULL',
			'active' => Schema::TYPE_BOOLEAN,
			'requirement_id' => Schema::TYPE_INTEGER,
		]);

		$this->createTable('participant', [
			'shift_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'user_id' => Schema::TYPE_INTEGER,
			'name' => Schema::TYPE_STRING,
			'size' => Schema::TYPE_INTEGER,
		]);

		$this->createTable('event', [
			'id' => 'pk',
			'name' => Schema::TYPE_STRING . ' NOT NULL',
			'start' => Schema::TYPE_INTEGER . ' NOT NULL',
			'end' => Schema::TYPE_INTEGER . ' NOT NULL',
			'active' => Schema::TYPE_BOOLEAN,
		]);

		$this->createTable('teamlead', [
			'team_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
		]);

		$this->createTable('settings', [
			'id' => 'pk',
			'value' => Schema::TYPE_TEXT,
		]);

		$this->createTable('requirement', [
			'id' => 'pk',
			'name' => Schema::TYPE_STRING . ' NOT NULL',
		]);

		$this->createTable('user_requirement', [
			'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'requirement_id' => Schema::TYPE_INTEGER . ' NOT NULL',
		]);

		$this->addForeignKey('fk_team_event_id', 'team', 'event_id', 'event', 'id', 'NO ACTION', 'NO ACTION');
		$this->addForeignKey('fk_shift_team_id', 'shift', 'team_id', 'team', 'id', 'NO ACTION', 'NO ACTION');
		$this->addForeignKey('fk_participant_shift_id', 'participant', 'shift_id', 'shift', 'id', 'NO ACTION', 'NO ACTION');
		$this->addForeignKey('fk_teamlead_team_id', 'teamlead', 'team_id', 'team', 'id', 'NO ACTION', 'NO ACTION');
		$this->addForeignKey('fk_user_requirement_requirement_id', 'user_requirement', 'requirement_id', 'requirement', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
 	{
		$this->dropTable('team');
		$this->dropTable('shift');
		$this->dropTable('participant');
		$this->dropTable('event');
		$this->dropTable('teamlead');
		$this->dropTable('settings');
		$this->dropTable('requirement');
		$this->dropTable('user_requirement');
    }

}
