<?php

/**
* View page for index
* @author Sujata Yadav
*/
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;


$this->title = 'Home|Newstand';
?>
<div class="site-index">

     <?php 
        if (Yii::$app->session->hasFlash('login_success'))
        {
            $flash = Yii::$app->session->getFlash('login_success');
            echo "<div class=\"alert alert-success\">";
            echo "<a class=\"close\" data-dismiss=\"alert\">×</a>";
            echo $flash;
            echo "</div>";
        }
       
        
         else if(Yii::$app->session->hasFlash('newsadd_success'))
         {
             $flash = Yii::$app->session->getFlash('newsadd_success');
             echo "<div class=\"alert alert-success\">";
              echo "<a class=\"close\" data-dismiss=\"alert\">×</a>";
            echo $flash;
            echo "</div>";
        }
         else if(Yii::$app->session->hasFlash('newsadd_failure'))
         {
             $flash = Yii::$app->session->getFlash('newsadd_failure');
             echo "<div class=\"alert alert-warning\">";
              echo "<a class=\"close\" data-dismiss=\"alert\">×</a>";
            echo $flash;
            echo "</div>";
        }

    ?>

    <?php
    if(!Yii::$app->user->isGuest)
    {
       echo "<a href= ".Url::toRoute('/site/addnews')." role=\"button\" title = \"Add your own news\">
                <img src = \"../img/add1.png\" width = \"40px\" height = \"40px\"></img>
            </a>";

      echo "<select style = \"float:right;\" onchange = \"filterArticles(this.value)\">
              <option value = 0>Show All</option>
              <option value = 1>Show Mine</option>
            </select>";
       
    }
    echo "<a href= ".Url::toRoute('/site/rss-subscription')." role=\"button\" title = \"Subscribe to RSS\">
            <img src = \"../img/rss.png\" width = \"30px\" height = \"30px\"></img>
          </a>";
    ?>
    <br><br>


     <?php 

        echo "<div class = \"row\">";
        echo "<div class = \"news-container\">";
       foreach ($models as $model1) 
       {
           
            echo "<div class = \"col-lg-12\">";
            $model->renderNews($model1, true);
             echo "</div>";
            
           
       }

       echo "</div>";
        echo "</div>";

          echo LinkPager::widget(['pagination' => $pages]);
     

    
    ?>

    </div>
</div>

<script type = "text/javascript">
  
  function filterArticles(id)
  {
    
    
    if(id == "1")
      location.href = "../site/me";
    else
      location.href = "../site/index";

  }
</script>
<style>
#title
{
  color:#5F2323;
  
}

a:hover
{
  color:black;
  text-decoration:none;
}
</style>