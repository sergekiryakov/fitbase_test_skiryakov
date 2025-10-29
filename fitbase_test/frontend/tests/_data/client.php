<?php

use frontend\models\Client;

return [
    [
        'id' => '00000000-0000-0000-0000-000000001000',
        'first_name' => 'Test',
        'last_name' => 'Client',
        'gender' => Client::GENDER_MALE,
        'birth_date' => '1995-02-02',
        'status' => Yii::$app->statusManager->getIdByCode('active'),
        'created_at' => '2025-10-28 12:00:40',
        'updated_at' => '2025-10-28 12:00:40',
        'created_by' => '00000000-0000-0000-0000-000000000001',
    ]
];
