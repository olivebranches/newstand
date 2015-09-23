<?php
/**
 * Controller file of application
 *
 * @author Sujata Yadav
 * 
 */
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\web\Session;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\AddnewsForm;
use app\models\ViewnewsForm;
use app\models\ContactForm;
use app\models\News;
use yii\data\Pagination;

use yii\web\NotFoundHttpException;
use app\modules\Rss\models\Feed;


class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
           
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    /**
    *Controller function for index page rendering with pagination
    */
    public function actionIndex()
    {
        $model = new ViewnewsForm();
        
        if(!Yii::$app->user->isGuest)
        {
            $news = News::find()->orderBy('time DESC');
            $pages = new Pagination(['totalCount' => $news->count()]);
            $models = $news
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
        else
        {
            $news = News::find()->orderBy('time DESC');
            
            $pages = new Pagination(['totalCount' =>1]);
            $models = $news
                    ->limit(10)
                    ->all();
        }

        return $this->render('index', [

             'models' => $models,

             'model'=>$model,

             'pages' => $pages,

        ]);

    }

    /**
    *Controller function for me page rendering with pagination
    */
    public function actionMe()
    {

        $model = new ViewnewsForm();

        $news = News::find()->where(['author_id'=>Yii::$app->user->id])->orderBy('time DESC');
            $pages = new Pagination(['totalCount' => $news->count()]);
            $models = $news
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();

         return $this->render('me', [

             'models' => $models,

             'model'=>$model,

             'pages' => $pages,

        ]);
    }

    /**
    *Controller function for rss subscription
    */
    public function actionRssSubscription()
    {
       
       $id = 'rss';
        $module = Yii::$app->getModule('Rss');
        
        if (empty($module->feeds[$id]))
        {
             
            throw new NotFoundHttpException("RSS feed not found.");
        }

        header('Content-type: application/xml');
        if($view = Yii::$app->cache->get("{$module->cacheKeyPrefix}-{$id}")) {
            echo $view;
            return;
        }

        $feedItems = Feed::find()
            ->select('title, description, link, content, author, pubDate')
            ->where(['feed_id' => 'rss'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        echo $view = $this->renderPartial('rssfeed', [
            'channel' => $module->feeds[$id],
            'items' => $feedItems
        ]);
        Yii::$app->cache->set("{$module->cacheKeyPrefix}-{$id}", $view, $module->cacheExpire);
    }

    /**
    *Controller function for view full news article
    */
    public function actionViewarticle($id)
    {
        $model = new ViewnewsForm();
        $model->article_id = $id;

        return $this->render('viewArticle', ['model'=>$model]);
    }

    /**
    *Controller function to convert news article to pdf
    */
    public function actionConverttopdf($id)
    {
        $model = new ViewnewsForm();

        $model->makePDF($id);

    }


    /**
    *Controller function for Login page and login
    */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) 
        {
            return $this-redirect('index');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) 
        {
            Yii::$app->session->setFlash('login_success', 'Logged in Successfully');
            return $this->redirect('index');
        } 
        else 
        {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
    *Controller function for signup page and signing up
    */
     public function actionSignup()
    {
        if (!\Yii::$app->user->isGuest) 
        {

            return $this->redirect('index');
        }

         $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()))
        {

            if($model->addUser())
            {
               Yii::$app->session->setFlash('signup_success', 'Registration Successful');
                return $this->redirect('login');

            }
            else
            {
                Yii::$app->session->setFlash('signup_failure', 'Registration Failed');
                return $this->redirect('index');
            }
        } 
        else 
        {

           
            return $this->render('signup', ['model'=>$model]);
        }
       
    }

    /**
    *Controller function for logout
    */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('index');
    }

    /**
    *Controller function for Add news page and adding news article
    */
    public function actionAddnews()
    {
        $model = new AddnewsForm();

        if($model->load(Yii::$app->request->post()))
        {
            if($model->addNews())
            {
               $model->image = UploadedFile::getInstance($model, 'image');

                if($model->image) 
                { 
                    $path = Yii::getAlias('@webroot')."/";
                    $images = $path.'/images';

                    if(!file_exists($images))
                    { 
                       
                        $old = umask(0);
                        mkdir($images, 0777, true);
                        umask($old);    
                         
                    }

                    $filePath = $images.'/'.$model->image->baseName.'.'.$model->image->extension;
              
                    //save file in filepath and check if successfull
                    if(is_writable($images) && $model->image->saveAs($filePath))
                     chmod($filePath, 0777);
                   
                    //if file not uploaded successfully
                    else
                    {  
                        if(!is_writable($images))
                        {
                            Yii::$app->session->setFlash('permission_error', 'Check Directory Permissions! Unable to Upload File');
                            //return $this->goHome();
                        }

                        Yii::$app->session->setFlash('file_error', 'Unable to Upload image');
                       // return $this->goHome();
                  
                    }
                }
               
                Yii::$app->session->setFlash('newsadd_success', 'News post Successful');
               // return $this->goHome();
               
            }
            else
            {
                Yii::$app->session->setFlash('newsadd_failure', 'News post Failed');
               // return $this->goHome();
            }   
        }
        else
        {
             return $this->render('addnews', [
                'model' => $model,
            ]);
        }
    }

    /**
    *Controller function for deleting a news article identified by
    * news_id
    * @param $id = news_id
    */
    public function actionDeleteThisNews($id)
    {
        $model = new AddnewsForm();
        if($model->deleteThisArticle($id))
        {
            Yii::$app->session->setFlash('delete_success', 'Deleted Successfully');
                return $this->redirect('me');
        }
        else
        {
            Yii::$app->session->setFlash('delete_failure', 'Cannot Delete');
                return $this->redirect('me');
        }
    }
   
}
