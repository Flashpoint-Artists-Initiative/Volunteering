<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "participant".
 *
 * @property integer $shift_id
 * @property integer $user_id
 * @property string $name
 * @property integer $size
 * @property boolean $completed
 */
class Participant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'participant';
    }

	public static function primaryKey()
	{
		return ['shift_id', 'user_id'];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_id'], 'required'],
            [['shift_id', 'user_id', 'size'], 'integer'],
            [['name'], 'string', 'max' => 255],
			[['completed'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shift_id' => 'Shift ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'size' => 'Size',
			'completed' => 'Shift Completed',
        ];
    }

	/**
	 * Relations
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	public function getShift()
	{
		return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
	}
}
