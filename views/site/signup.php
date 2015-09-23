<?php

/**
* View page for Signup
* @author Sujata Yadav
*/


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$this->title = 'Sign Up';
?>
<div class="site-signup">
   

    <div class = "row">

      
        <div>
            <div class = "signup-header">
                    <h1><?= Html::encode($this->title) ?></h1>

            </div>
            <br>
               
           
            <?php $form = ActiveForm::begin([
                'id' => 'signup-form',
               
            ]); 
            ?>

           
                <div class = "col-lg-4">
                    <label>Username</label>
                    <?= $form->field($model, 'username')->textInput(['style'=>'width:300px'])->label(false) ?>
                    <br>

                    <label>Email</label>
                    <?= $form->field($model, 'email_id')->textInput(['style'=>'width:300px'])->label(false) ?>
                    <br>

                    <label>Confirm Email</label>
                    <?= $form->field($model, 'confirm_email')->textInput(['style'=>'width:300px'])->label(false) ?>
                    <br>
                </div>
                <div class = "col-lg-4">

                    <label>Phone</label>
                    <?= $form->field($model, 'phone')->textInput(['style'=>'width:300px'])->label(false) ?>
                    <br>

                    <label>Password</label>
                    <?= $form->field($model, 'password')->passwordInput(['style'=>'width:300px'])->label(false) ?>
                    <br>

                    <label>Confirm Password</label>
                    <?= $form->field($model, 'password_repeat')->passwordInput(['style'=>'width:300px'])->label(false) ?>
                    <br>

                     <div class="form-group">
                      
                            <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary', 'name' => 'signup-button'], ['style'=>'width:250px']) ?>
                       
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

      
        </div>
    </div>

</div>

