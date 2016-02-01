<?php

namespace common\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "requirement".
 *
 * @property integer $id
 * @property string $name
 * @property string $error_message
 * @property string $team
 */
class Requirement extends \yii\db\ActiveRecord
{
	const defaultMessage = "You do not meet the requirements for this shift.";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requirement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'team'], 'string', 'max' => 255],
			['error_message', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
			'error_message' => 'Error Message',
			'errorMessageString' => 'Error Message',
			'team' => 'Associated Team',
        ];
    }

	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('user_requirement', ['requirement_id' => 'id']);
	}

	public function check($user_id)
	{
		return $this->getUsers()
			->where(['id' => $user_id])
			->count() > 0;
	}

	public function getErrorMessageString()
	{
		return isset($this->error_message) ? $this->error_message : self::defaultMessage;
	}

	public function getTeamString()
	{
		return isset($this->team) ? $this->team : self::defaultTeam;
	}
}
