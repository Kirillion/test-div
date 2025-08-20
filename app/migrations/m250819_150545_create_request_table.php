<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m250819_150545_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TYPE statusRequestEnum AS ENUM ('Active', 'Resolved')");

        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'email' => $this->string(254)->notNull(),
            'status' => $this->getDb()->getSchema()->createColumnSchemaBuilder("statusRequestEnum default 'Active'")->notNull(),
            'message' => $this->text()->notNull(),
            'comment' => $this->text(),
            'createdAt' => $this->integer()->notNull(),
            'updatedAt' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request}}');

        $this->execute('DROP TYPE statusRequestEnum');
    }
}
