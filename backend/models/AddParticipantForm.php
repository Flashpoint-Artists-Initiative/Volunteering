<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Shift;
use common\models\User;
use common\models\Participant;
use yii\web\NotFoundHttpException;

/**
 * LoginForm is the model behind the login form.
 */
class AddParticipantForm extends Model
{
	public $shift_id;
	public $user_id;
	public $user_search;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['shift_id', 'user_id', 'user_search'], 'required'],
            [['shift_id', 'user_id'], 'integer'],
        ];
    }

	public function attributeLabels()
	{
		return [
			'shift_id' => 'Shift',
			'user_id' => 'User',
			'user_search' => 'User',
		];
	}

	public function addUser()
	{
		if(!$this->validate())
		{
			return false;
		}

        if (Shift::findOne($this->shift_id === null) ||
			User::findOne($this->user_id == null))
		{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

		$participant = new Participant();
		$participant->shift_id = $this->shift_id;
		$participant->user_id = $this->user_id;

		return $participant->save();

	}
}
