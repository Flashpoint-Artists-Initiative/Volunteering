<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_requirement".
 *
 * @property integer $user_id
 * @property integer $requirement_id
 */
class UserRequirement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_requirement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'requirement_id'], 'required'],
            [['user_id', 'requirement_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'requirement_id' => 'Requirement ID',
        ];
    }
}
