<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * ViewnewsForm is the model behind the showing news snippets and
 * full news articles on webpage.
 * @author Sujata Yadav
 */
class ViewnewsForm extends Model
{
    public $article_id;

    /**
    * Render news on webpage depending on mode
    * @param - $news = news details array
    * @param - $mode => if true renders snippets
    *                => if false renders full news article
    */
    public function renderNews($news, $mode)
    {
       $user_details = (new Users())->findIdentity($news['author_id']);
       $date = new \DateTime($news['date']);

        $date = date_format($date, "d F Y");

        if($mode == true)
        {
            echo "<div class = \"news-box\">";
        
            echo "<h3 align=\"justify\" ><b><a id = \"title\" href = ".Url::toRoute(['/site/viewarticle','id'=>$news['news_id'] ])." >".$news['news_title']."</a></b></h3>";
            echo "<p  style = \"font: italic 0.8em sans-serif;\">".$date." ".$news['time']."</p>
                <p  style = \"font: italic 0.8em sans-serif;\"><b>Author </b>".$user_details['username']."</p>
                <p  style = \"font: italic 0.8em sans-serif;\"><b>Email: </b>".$user_details['email_id']."</p>
                
                <p align=\"justify\">".$this->shortContent($news['news_text'], 800)."......
                ";
            echo "<a href =".Url::toRoute(['/site/viewarticle','id'=>$news['news_id']  ])." style = \"padding-left: 0px;\">View More</a></p>";
          

            echo "</div>";
            
        }
        else
        {
            $path = Yii::getAlias('@webroot')."/";

            

            $file_path = '../../images/'.$news['image'];
            
            echo "<div class = \"full-article\">";
            echo "<h1 style = \"font-family:sans-serif\"><b>".$news['news_title']."</b></h1>
                <p style = \"font: italic 1em sans-serif;\">".$date." ".$news['time']."</p>
                <p style = \"font: italic 1em sans-serif;\"><b>Author </b>".$user_details['username']."</p>
                <p style = \"font: italic 1em sans-serif;\"><b>Email: </b>".$user_details['email_id']."</p>
                <br>
                <center>";
                if($news['image']!=NULL)
                    echo "<img src = '".$file_path."'' ></img><br><br>";
                
                echo "<p align=\"justify\" style = \"font-family: Times, 'Times New Roman', Georgia, serif;\">".$news['news_text']."</p>
                </center>";
            echo "</div>";
        }

    }

    /**
    * Shows full article by news id
    * @param $id = news_id
    */
    public function showFullArticle($id)
    {
        $news = (new News())->getNewsById($id);

        if(!Yii::$app->user->isGuest)
        {    echo "<a href= ".Url::toRoute('/site/addnews')." role=\"button\" title = \"Add your own news\">
                <img src = \"../../img/add1.png\" width = \"40px\" height = \"40px\"></img>
            </a>";

            
        }
        echo "<a href= ".Url::toRoute(['/site/converttopdf','id'=>$news['news_id']  ])." role=\"button\" title = \"Download PDF\">
                <img src = \"../../img/pdf2.png\" width = \"40px\" height = \"40px\"></img>
              </a>";

        if(!Yii::$app->user->isGuest)
        { 
            if($news['author_id'] == Yii::$app->user->id)
            {
                echo "<a href= ".Url::toRoute(['/site/delete-this-news', 'id'=>$news['news_id']  ])." role=\"button\" title = \"Delete this Article\" style = \"float:right;\">
                        <img src = \"../../img/delete.png\" width = \"40px\" height = \"40px\"></img>
                    </a>";
            }
        }
        echo "<br/><br/>";

        $this->renderNews($news, false);
   
    }

    /**
    * Makes PDF from a news article
    * Uses TCPDF - pdf library
    * @param $id = news_id 
    */
    public function makePDF($id)
    {
        $path = Yii::getAlias('@webroot')."/";

        require_once($path.'third_party_software/tcpdf/tcpdf.php');

        $directoryTempPath = $path.$id.'TempAssigmentFiles/';

        if(!file_exists($directoryTempPath))
        { 
            echo "file not exists";
            $old = umask(0);
            mkdir($directoryTempPath, 0777, true);
            umask($old);    
             
        }


        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

        

        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        

        if(@file_exists(dirname(__FILE__).'/lang/eng.php'))
        {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage();

        ob_clean();
        
        $news = (new News())->getNewsById($id);

        $user_details = (new Users())->findIdentity($news['author_id']);
       
        $date = new \DateTime($news['date']);

        $date = date_format($date, "d F Y");
       
         $file_path = 'images/'.$news['image'];
            $html = '<html>
            <head></head>
            <body>
            
            <h1><b>'.$news['news_title'].'</b></h1>
            <p>'.$date.' '.$news['time'].'</p>
            <p><b>Author </b>'.$user_details['username'].'</p>
            <p><b>Email: </b>'.$user_details['email_id'].'</p>
            <br>
            <center>';

            if($news['image']!=NULL)
            {
               
                $html .= '<img src = "'.$file_path.'"  width=\'200px\' height=\'300px\'>';
            }
            $html .=
           
            '<br>
            <p align="justify">'.$news['news_text'].'</p>
            </center>
            </body>
            </html>';

        $file_name = "News";

        $full_file_path = $directoryTempPath.$file_name.$id.$date.'.pdf';
       
           
                
           
        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->lastPage();
        $pdf->Output($full_file_path, 'F'); 
        

        
        require_once($path.'ConcatPdf.php');

        $pdf = new \ConcatPdf();
        $pdf->setFiles(array($full_file_path));
        $pdf->concat();
        $pdf->SetProtection(array('modify','extract', 'annot-forms','assemble','copy'), '', '', 0, 1);

        $pdf->Output($directoryTempPath.$file_name.$id.$date.'_temp.pdf', 'F');

        
            header("Content-disposition: attachment; filename=".$file_name.$news['date'].".pdf");
            header("Content-type: application/pdf");
            readfile($directoryTempPath.$file_name.$id.$date.'_temp.pdf');
       
        /**
        *Deleting the files that were created
        */
        unlink($full_file_path);
        unlink($directoryTempPath.$file_name.$id.$date.'_temp.pdf');
        rmdir($directoryTempPath);

    }

    public function shortContent($text, $len)
    {
        $string = "";
        $i=0;
        while($i!=$len)
        {
            $string .= $text[$i];
            $i++;
        }

        return $string;
    }

  

}
