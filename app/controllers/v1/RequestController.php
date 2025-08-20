<?php

namespace app\controllers\v1;

use app\components\actions\BatchCreateAction;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class RequestController extends ActiveController
{
    public $modelClass = 'app\models\Request';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [$_ENV['FRONTEND_DOMAIN']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT'],
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['create', 'options']
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['index', 'view', 'create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['?']
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'update'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => false,
                    'actions' => ['delete'],
                ],
            ],
            'except' => ['options']
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['get', 'options'],
                'view' => ['get', 'options'],
                'create' => ['post', 'options'],
                'update' => ['put', 'options'],
                'delete' => ['delete', 'options'],
            ],
        ];

        Yii::$app->user->enableSession = false;

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['create'] = [
            'class' => BatchCreateAction::class,
            'modelClass' => $this->modelClass,
        ];

        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        $searchModel = new \app\models\RequestSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $dataProvider;
    }
}
