<?php

namespace app\components\actions;

use app\components\interfaces\BatchCreateActiveRecordInteface;
use Yii;
use yii\rest\CreateAction;
use yii\web\ServerErrorHttpException;

class BatchCreateAction extends CreateAction
{
    /**
     * Creates a new models.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        $data = Yii::$app->getRequest()->getBodyParams();

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $modelClass = $this->modelClass;

        if (!in_array(BatchCreateActiveRecordInteface::class, class_implements($modelClass))) {
            throw new ServerErrorHttpException(
                sprintf('Class %s must implement %s', $this->modelClass, BatchCreateActiveRecordInteface::class)
            );
        }

        $errors = $modelClass::batchCreate($data);

        if (empty($errors)) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (empty($errors)) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $errors;
    }
}
