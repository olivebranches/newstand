<?php


/**
* View page for viewing full news article
* @author Sujata Yadav
*/

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'View Article|Newstand';
?>
<div class="site-article">

   
    <?php 
      $model->showFullArticle($model->article_id);
    ?>
    
   

    </div>
</div>


