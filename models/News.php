<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "news".
 *
 * @property integer $news_id
 * @property string $news_title
 * @property integer $author_id
 * @property string $news_text
 * @property string $date
 * @property string $time
 * @property string $image
 *
 * @property Users $author
 * @author Sujata Yadav
 */
class News extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_title', 'author_id', 'news_text', 'date', 'time'], 'required'],
            [['author_id'], 'integer'],
            [['news_text', 'image'], 'string'],
            [['date', 'time'], 'safe'],
            [['news_title'], 'string', 'max' => 500],
            [['image'], 'string', 'max' => 225]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => Yii::t('app', 'News ID'),
            'news_title' => Yii::t('app', 'News Title'),
            'author_id' => Yii::t('app', 'Author ID'),
            'news_text' => Yii::t('app', 'News Text'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'image' => Yii::t('app', 'Image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['id' => 'author_id']);
    }

    
    /*public function get10Headlines()
    {
        $news = News::find()->orderBy('time DESC')->limit(10)->all();

        return $news;
    }

    public function getAllNews()
    {
        $news = News::find()->orderBy('time DESC')->all();

        return $news;
    }*/

    /**
    * Get news by news_id
    * @param $id = news_id
    */
    public function getNewsById($id)
    {
        $news = News::find()
                ->select(['*'])
                ->where(['news_id'=>$id])
                ->one();

        return $news;
    }
}
