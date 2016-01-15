<?php

namespace common\components;

use Yii;
use yii\web;

class DrupalUser extends \yii\web\User
{
	public function can($permissionName, $params = [], $allowCaching = true)
	{
		return $this->identity !== null && in_array($permissionName, $this->identity->roles);
	}

	public function hasRole($role)
	{
		if($this->isGuest)
		{
			return false;
		}

		return in_array($role, $this->identity->roles);
	}
}
