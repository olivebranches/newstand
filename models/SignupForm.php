<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SignupForm is the model behind the signup form.
 */
class SignupForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;
    public $email_id;
    public $confirm_email;
    public $phone;
    public $photo;
    

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            
            [['username', 'password', 'password_repeat', 
            'email_id', 'confirm_email','phone'], 'required'],
            [['username'], 'string', 'min'=>4,'max' => 255],
            [['email_id', 'confirm_email'], 'email'],
            [['phone'], 'string', 'min'=> 8, 'max' => 20],
            [['phone'], 'match', 'pattern' => '/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/'],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
            [['confirm_email'], 'compare', 'compareAttribute' => 'email_id'],

           
        ];
    }

    public function addUser()
    {
        if($this->validate())
        {
            $today = date("Y/m/d");

            $users = new Users();
            $users->username = $this->username;
            $users->password_field = $this->password;
            $users->email_id = $this->email_id;
            $users->phone = $this->phone;

            $users->subscribed_to_rss = 0;

            $users->date_of_subscribing = $today;

            if($users->save())
                return true;
            
            else
                return false;
            
        }
        else
            return false;

    }

}
