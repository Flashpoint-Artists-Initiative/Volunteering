<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Requirement;
use common\models\UserRequirement;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * LoginForm is the model behind the login form.
 */
class AddUserRequirementForm extends Model
{
	public $requirement_id;
	public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['requirement_id', 'user_id'], 'required'],
            [['requirement_id', 'user_id'], 'integer'],
        ];
    }

	public function attributeLabels()
	{
		return [
			'requirement_id' => 'Requirement',
			'user_id' => 'User',
		];
	}

	public function getRequirementList()
	{
		$requirements = Requirement::find()->addOrderBy('name asc')->all();
		$output = [];

		foreach($requirements as $req)
		{
			if(!isset($output[$req->teamString]))
			{
				$output[$req->teamString] = [];
			}

			$output[$req->teamString][$req->id] = $req->name;
		}
		
		return $output;
		return ArrayHelper::map($requirements, 'id', 'name');
	}

	public function addUserRequirement()
	{
		if(!$this->validate())
		{
			return false;
		}

		$user = User::findOne($this->user_id);

        if (Requirement::findOne($this->requirement_id === null) ||
			$user === null)
		{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

		foreach($user->requirements as $current_req)
		{
			if($current_req->id == $this->requirement_id)
			{
				return false;
			}
		}

		$req = new UserRequirement();
		$req->user_id = $this->user_id;
		$req->requirement_id = $this->requirement_id;

		return $req->save();
	}
}
