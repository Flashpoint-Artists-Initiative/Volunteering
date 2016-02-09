<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
	public $roleSearch;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['username', 'real_name', 'burn_name', 'email', 'roleSearch'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

		if(isset($this->roleSearch))
		{
			$query->joinWith('authAssignments');
			$query->orFilterWhere(['auth_assignment.item_name' => $this->roleSearch]);
		}

        $query->orFilterWhere([
            'id' => $this->id,
        ]);

        $query->orFilterWhere(['like', 'username', $this->username]) 
            ->orFilterWhere(['like', 'real_name', $this->real_name])
            ->orFilterWhere(['like', 'burn_name', $this->burn_name])
            ->orFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}

