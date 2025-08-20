<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname={$_ENV['TEST_DB_NAME']}",
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8'
];
