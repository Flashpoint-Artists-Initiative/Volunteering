<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Requirement;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
	const STATUS_ACTIVE = 10;
	const STATUS_DELETED = 0;

	public $new_password;
	public $new_password_repeat;
	public $settings;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
			[['username', 'email', 'real_name', 'new_password', 'new_password_repeat'], 'required', 'on'=>'insert'],
			[['username', 'email', 'real_name'], 'required', 'on'=>'update'],
			['username', 'unique'],
			['burn_name', 'safe'],
			['email', 'email'],
			['new_password', 'compare', 'compareAttribute' => 'new_password_repeat', 'on' => 'update'],
			['new_password_repeat', 'compare', 'compareAttribute' => 'new_password', 'on' => 'update'],
			['status', 'default', 'value' => self::STATUS_ACTIVE],
			['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

	public function attributeLabels()
	{
		return  [
			'username' => 'Username',
			'email' => 'Email',
			'burn_name' => 'Burn Name',
			'real_name' => 'Legal Name',
			'new_password_repeat' => 'Confirm new password',
		];
	}

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
		return static::findOne($id);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
		$model = static::findOne(['username' => $username]);

		if($model)
		{
			return $model;
		}

		return static::findOne(['email' => $username]);
    }

	public static function findByPasswordResetToken($token)
	{
		if (!static::isPasswordResetTokenValid($token)) {
			return null;
		}

		return static::findOne([
			'password_reset_token' => $token,
			'status' => self::STATUS_ACTIVE,
		]);
	}

	public static function isPasswordResetTokenValid($token)
	{
		if (empty($token)) {
			return false;
		}

		$timestamp = (int) substr($token, strrpos($token, '_') + 1);
		$expire = Yii::$app->params['user.passwordResetTokenExpire'];
		return $timestamp + $expire >= time();
	}
    
	/**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
		//return crypt($password, $this->password) === $this->password;
		return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

	public function setPassword($password)
	{
		//$this->password = $this->hashPassword($password);
		$this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
	}

	/**
	 * Get the shift participation this user is a part of 
	 */

	public function getParticipation()
	{
		Participant::findAll(['user_id' => $this->uid]);
	}

	public function getRequirements()
	{
		return $this->hasMany(Requirement::className(), ['id' => 'requirement_id'])
			->viaTable('user_requirement', ['user_id' => 'id']);
	}

	public function beforeSave()
	{
		if(!$this->isNewRecord && isset($this->new_password))
		{
			$this->setPassword($this->new_password);
		}
		$this->data = serialize($this->settings);

		return true;
	}

	public function afterFind()
	{
		$this->settings = unserialize($this->data);
	}


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
		return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
		return $this->getAuthKey() === $authKey;
    }

	public function generateAuthKey()
	{
		$this->auth_key = \Yii::$app->security->generateRandomString();
	}

	public static function findIdentityByAccessToken($token, $type = NULL)
	{
	}

	public function generatePasswordResetToken()
	{
		$this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
	}

	public function removePasswordResetToken()
	{
		$this->password_reset_token = null;
	}
}
