<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "rss_feed".
 *
 * @property integer $id
 * @property string $feed_id
 * @property string $title
 * @property string $description
 * @property string $link
 * @property string $content
 * @property string $author
 * @property string $pubDate
 * 
 */
class RssFeed extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rss_feed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feed_id', 'title', 'description', 'link', 'content', 'author', 'pubDate'], 'required'],
            [['description', 'content'], 'string'],
            [['feed_id', 'title', 'link', 'author', 'pubDate'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'feed_id' => Yii::t('app', 'Feed ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'link' => Yii::t('app', 'Link'),
            'content' => Yii::t('app', 'Content'),
            'author' => Yii::t('app', 'Author'),
            'pubDate' => Yii::t('app', 'Pub Date'),
        ];
    }

    public function beforeSave($insert)
    {
        
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $rss = Yii::$app->getModule('Rss');
                
                $rssItem = $rss->createNewItem();

                $rssItem->title = $this->title;
                $rssItem->description = $this->description;
                $rssItem->content = $this->content;
                $rssItem->author = $this->author;
                $rssItem->link = Url::to($this->link, true);
                $rssItem->pubDate = time();

                return $rss->addItemToFeed('rss', $rssItem);
            }
            return true;
        }
        return false;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $rss = Yii::$app->getModule('rss');

        $rss->deleteItems('rss', ['link' => Url::to($this->url, true)]);
    }
}
