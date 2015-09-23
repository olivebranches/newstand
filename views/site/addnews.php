<?php

/**
* View page for Add news
* @author Sujata Yadav
*/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;


$this->title = 'Add News';

?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'addnews-form', 
                'options' => ['enctype'=>'multipart/form-data']
                 ]); ?>
                <?= $form->field($model, 'news_title')->textArea(['rows'=>4]) ?>
                <?= $form->field($model, 'news_text')->textArea(['rows'=>10]) ?>
                <?= $form->field($model, 'image')->fileInput() ?>
               
                <div class="form-group">
                    <?= Html::submitButton('Add', ['class' => 'btn btn-primary', 'name' => 'add-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


</div>
