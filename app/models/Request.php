<?php

namespace app\models;

use app\components\interfaces\BatchCreateActiveRecordInteface;
use Yii;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $message
 * @property string|null $comment
 * @property int $createdAt
 * @property int|null $updatedAt
 */
class Request extends \yii\db\ActiveRecord implements BatchCreateActiveRecordInteface
{

    /**
     * ENUM field values
     */
    const STATUS_ACTIVE = 'Active';
    const STATUS_RESOLVED = 'Resolved';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['comment', 'updatedAt'], 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['createdAt', 'default', 'value' => time()],
            [['status', 'message', 'comment'], 'string'],
            [['createdAt', 'updatedAt'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 254],
            [['email'], 'email'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            ['status', 'validateResolved']
        ];
    }

    public function validateResolved($attribute, $params)
    {
        if ($this->$attribute == self::STATUS_RESOLVED && empty($this->comment)) {
            $this->addError($attribute, 'Нельзя устанавливать статус "Решено", пока комментарий не заполнен.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status',
            'message' => 'Message',
            'comment' => 'Comment',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RESOLVED => 'Resolved',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function setStatusToResolved()
    {
        $this->status = self::STATUS_RESOLVED;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            if (array_key_exists('status', $changedAttributes)) {

                if ($this->status == self::STATUS_RESOLVED && !$this->isEmailSent) {
                    $this->updateAttributes([
                        'updatedAt' => time()
                    ]);

                    $this->sendEmail();
                }
            }
        }
    }

    private function sendEmail()
    {
        $isEmailSent = Yii::$app->mailer->compose('request/answer', ['request' => $this])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($this->email)
            ->setSubject("Ответ на запрос: {$this->id}")
            ->send();

        if ($isEmailSent) {
            $this->updateAttributes([
                'isEmailSent' => true,
                'updatedAt' => time()
            ]);
        }
    }

    public static function batchCreate(array $data): array
    {
        $rows = [];
        $errorRows = [];        
        // Можно разбить массив на чанки, если ожидается большое количество данных
        foreach ($data as $datum) {
            $model = new self();
            $model->load($datum, '');

            if ($model->validate()) {
                $rows[] = [
                    'name' => $model->name,
                    'email' => $model->email,
                    'status' => $model->status,
                    'message' => $model->message,
                    'comment' => $model->comment,
                    'isEmailSent' => $model->isEmailSent,
                    'createdAt' => $model->createdAt,
                    'updatedAt' => $model->updatedAt,
                ];
            } else {
                $errorRows[] = [
                    'model' => $model->attributes,
                    'errors' => $model->getErrors()
                ];
            }
        }

        Yii::$app->db->createCommand()
            ->batchInsert(self::tableName(), ['name', 'email', 'status', 'message', 'comment', 'isEmailSent', 'createdAt', 'updatedAt'], $rows)
            ->execute();

        return $errorRows;
    }
}
