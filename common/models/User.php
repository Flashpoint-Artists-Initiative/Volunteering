<?php
namespace common\models;

require_once DRUPAL_ROOT . '/includes/password.inc';

use Yii;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public $uid;
    public $name;
    public $pass;
	public $roles;
    public $role_concat;
    public $authKey;
    public $accessToken;

    public static function tableName()
    {
        return 'user';
    }

	public function init()
	{
		$this->roles = [];

		if(!empty($this->role_concat))
		{
			$this->roles = explode(",", $this->role_concat);
		}
	}

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
		$result = (new \yii\db\Query())
			->select('u.uid, u.name, u.pass, group_concat(r.name) as role_concat')
			->from('users u')
			->leftJoin('users_roles ur', 'ur.uid = u.uid')
			->leftJoin('role r', 'r.rid = ur.rid')
			->where('u.uid = :id', [':id' => $id])
			->one(\Yii::$app->shared_db);

		if($result)
		{
			return new static($result);
		}	

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
		$result = (new \yii\db\Query())
			->select('u.uid, u.name, u.pass, group_concat(r.name) as role_concat')
			->from('users u')
			->leftJoin('users_roles ur', 'ur.uid = u.uid')
			->leftJoin('role r', 'r.rid = ur.rid')
			->where('u.name = :name', [':name' => $username])
			->one(\Yii::$app->shared_db);

		if($result)
		{
			return new static($result);
		}	

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
		return user_check_password($password, $this);
    }

	/**
	 * Get the shift participation this user is a part of 
	 */

	public function getParticipation()
	{
		Participant::findAll(['user_id' => $this->uid]);
	}
}
