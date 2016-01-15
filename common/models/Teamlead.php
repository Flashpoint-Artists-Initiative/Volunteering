<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teamlead".
 *
 * @property integer $team_id
 * @property integer $user_id
 */
class Teamlead extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teamlead';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'user_id'], 'required'],
            [['team_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'team_id' => 'Team ID',
            'user_id' => 'User ID',
        ];
    }

	public function getTeam()
	{
		return $this->hasOne(Team::className(), ['id' => 'team_id']);
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
