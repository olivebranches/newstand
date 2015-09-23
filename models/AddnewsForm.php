<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * AddnewsForm is the model behind the Add News Page and Delete News.
 * @author Sujata Yadav
 */
class AddnewsForm extends Model
{
    public $news_title;
    public $news_text;
    public $image;
    public $date;
    public $time;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            
            [['news_title', 'news_text'], 'required'],
           
            [['image'], 'file', 'extensions'=>'jpg, gif, png'],
            [['image'], 'file'],
           
        
        ];
    }

    /**
    * Add news entered in the form to News table in Database.
    */
    public function addNews()
    {
        if ($this->validate()) 
        {
            
            $news = new News();
            $news->news_title = $this->news_title;
            $news->news_text = $this->news_text;

           
            
            $news->date = date("Y/m/d");
            $news->time = date("H:m:s");
            $news->author_id = Yii::$app->user->id;

            $this->image = UploadedFile::getInstance($this, 'image');
             if($this->image!=null)
                $news->image = $this->image->baseName.'.'.$this->image->extension;

            if($news->save())
            {
                $id = Yii::$app->db->getLastInsertID();


                $rss = new RssFeed();
                $rss->feed_id = 'rss';
                $rss->title = $this->news_title;
                $rss->description = "hello";
                $rss->content = $this->news_text;
                $rss->link = Url::toRoute(['/site/viewarticle','id'=>$id]);

                $user = (new Users())->findIdentity(Yii::$app->user->id);

                $rss->author = $user['username'];
                $rss->pubDate = date('Y:m:d').' '.date('H:i:s');

                if($rss->beforeSave($rss))
                 {
                    echo "success";
                    return true;
                 }  
                else
                 {

                    echo $rss->feed_id;
                    echo  $rss->title;
                    echo $rss->description;
                    echo $rss->content;
                    echo $rss->link;

                

                    echo $rss->author;
                    echo $rss->pubDate;
                    echo "fail";
                 }  // return false;
            }
               
            else
            {
                echo "failed";
                //return false;
            }
        } 
        else 
        {
             echo "failed";
            //return false;
        }
    }

    /**
    * Delete the news identified by news id from the 
    * News table in database.
    * @param $id = news_id
    */
    public function deleteThisArticle($id)
    {
        $news = (new News())->getNewsById($id);

        if($news->delete()!==false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
