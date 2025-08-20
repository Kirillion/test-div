<?php

namespace app\controllers\v1;

use Yii;
use yii\rest\ActiveController;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\filters\VerbFilter;

class UserController extends ActiveController
{

    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [$_ENV['FRONTEND_DOMAIN']],
                'Access-Control-Request-Method' => ['POST'],
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type'],
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['login'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login'],
                    'roles' => ['?']
                ]
            ],
            'except' => ['options']
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'login' => ['post', 'options']
            ],
        ];

        Yii::$app->user->enableSession = false;

        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function actionLogin()
    {
        Yii::$app->user->enableSession = false;

        $request = Yii::$app->request->post();
        $user = User::findByUsername($request['username']);
        if ($user && $user->validatePassword($request['password'])) {
            return ['access_token' => $user->accessToken];
        }
        throw new \yii\web\UnauthorizedHttpException('Неверные данные');
    }
}
