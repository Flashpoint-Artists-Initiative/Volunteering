<?php

namespace common\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "requirement".
 *
 * @property integer $id
 * @property string $name
 */
class Requirement extends \yii\db\ActiveRecord
{
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
            [['name'], 'string', 'max' => 255]
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
        ];
    }

	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('user_requirement', ['requirement_id' => 'id']);
	}
}
