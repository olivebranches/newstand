<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $email_id
 * @property string $phone
 * @property string $date_of_subscibing
 * @property integer $subscribed_to_rss
 * @property resource $picture
 *
 * @property News[] $news
 * @author Sujata Yadav
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_field', 'email_id', 'phone', 'date_of_subscribing', 'subscribed_to_rss'], 'required'],
            [['date_of_subscribing'], 'safe'],
            [['subscribed_to_rss'], 'integer'],
            [['picture'], 'string'],
            [['username', 'password_field', 'auth_key'], 'string', 'max' => 255],
            [['email_id'], 'string', 'max' => 155],
            [['phone'], 'string', 'max' => 45],
            [['access_token', 'auth_key'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'username' => Yii::t('app', 'User Name'),
            'password_field' => Yii::t('app', 'Password Field'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'email_id' => Yii::t('app', 'Email ID'),
            'phone' => Yii::t('app', 'Phone'),
            'date_of_subscribing' => Yii::t('app', 'Date Of Subscribing'),
            'subscribed_to_rss' => Yii::t('app', 'Subscribed To Rss'),
            'picture' => Yii::t('app', 'Picture'),
        ];
    }

    /**
    * Generated Hash of the password before saving
    * @param $insert - object to be inserted
    */
    public function beforeSave($insert) 
    {
        $this->password_field = Yii::$app->getSecurity()->generatePasswordHash($this->password_field);
        
        return parent::beforeSave($insert);
    }

    /**
    * Find user by id
    * @param $id - id
    */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['author_id' => 'id']);
    }

    /**
    * Find user by username
    * @param $username - username
    */
    public static function findByUsername($username)
    {
        $users = Users::find()
                ->select(['*'])
                ->all();

        foreach ($users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
    * Validate passowrd if matches with stored password or not
    * @param $password - password
    */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_field);

    }
   
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
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
        return $this->auth_key === $authKey;
    }

}
