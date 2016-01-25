<?php
namespace common\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\User;
use yii\di\Instance;

class UserinfoBehavior extends ActionFilter
{
	public $user = 'user';
	public $redirect_url = ['/site/userinfo'];

	public function init()
	{
		parent::init();

		$this->user = Instance::ensure($this->user, User::className());
	}

	public function beforeAction($action)
	{
		$user = $this->user->identity;
		if(empty($user->real_name) || 
			empty($user->username) || 
			empty($user->email))
		{
			//Redirect to fill out extra info
			$request = Yii::$app->getRequest();

			if ($this->redirect_url !== null)
			{
				$redirect_url = (array) $this->redirect_url;

				if ($redirect_url[0] !== Yii::$app->requestedRoute)
				{
					Yii::$app->session->addFlash("error", "Please fill out the following fields before continuing.");
					Yii::$app->getResponse()->redirect($this->redirect_url);
					return false;
				}
			}
		}

		return true;
	}
}
