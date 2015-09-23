<?php

/**
* View page for Login
* @author Sujata Yadav
*/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;


$this->title = 'Login';
?>
<?php
    if(Yii::$app->session->hasFlash('signup_success'))
         {
             $flash = Yii::$app->session->getFlash('signup_success');
             echo "<div class=\"alert alert-success\">";
              echo "<a class=\"close\" data-dismiss=\"alert\">×</a>";
            echo $flash;
            echo "</div>";
        }
        else if(Yii::$app->session->hasFlash('signup_failure'))
         {
             $flash = Yii::$app->session->getFlash('signup_failure');
             echo "<div class=\"alert alert-warning\">";
              echo "<a class=\"close\" data-dismiss=\"alert\">×</a>";
            echo $flash;
            echo "</div>";
        }
?>
<div class="site-login">
   

    <div class = "row">

        <div class = "col-lg-4">
        </div>

        <div class = "col-lg-8">
            <div class = "login-header">
                    <h1><?= Html::encode($this->title) ?></h1>

            </div>
            <div class = "login-box">
               
           
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                
            ]); 
            ?>
    
            <label>Username</label>
            <?= $form->field($model, 'username')->textInput(['style'=>'width:300px'])->label(false) ?>
            <br>

            <label>Password</label>
            <?= $form->field($model, 'password')->passwordInput(['style'=>'width:300px'])->label(false) ?>
            <br>

            
                <div class="form-group">
                    
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button'], ['style'=>'width:250px']) ?>
                    
                </div>
            <p>New User? Register <a href = <?=Url::toRoute('/site/signup') ?> >here</a></p>
            <?php ActiveForm::end(); ?>

        </div>
        </div>
    </div>

</div>

<style>


.wrap
{
   background: -webkit-linear-gradient(120deg, #A7BEDA, #FDFDFF); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(120deg, #A7BEDA, #FDFDFF); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(120deg, #A7BEDA, #FDFDFF); /* For Firefox 3.6 to 15 */
  background: linear-gradient(120deg, #A7BEDA, #FDFDFF); /* Standard syntax */
 
}
</style>