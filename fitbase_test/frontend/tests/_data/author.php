<?php

return [
    [
        'id' => '00000000-0000-0000-0000-000000000001',
        'username' => 'test_user',
        'auth_key' => 'testkey123',
        'password_hash' => Yii::$app->security->generatePasswordHash('Password123'),
        'email' => 'func@example.test',
        'status' => Yii::$app->statusManager->getIdByCode('active'),
        'created_at' => '2025-10-28 12:00:40',
    ],
];
