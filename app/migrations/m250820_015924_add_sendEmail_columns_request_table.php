<?php

use yii\db\Migration;

class m250820_015924_add_sendEmail_columns_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%request}}', 'isEmailSent', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%request}}', 'isEmailSent');
    }
}
