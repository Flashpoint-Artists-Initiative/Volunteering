<?php
namespace common\components;

use yii2mod\settings\components\Settings as BaseSettings;

class Settings extends BaseSettings
{
    public function invalidateCache()
    {
		parent::invalidateCache();

		\Yii::$app->frontendCache->delete($this->cacheKey);

        return true;
    }
}
