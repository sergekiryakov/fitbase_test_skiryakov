<?php

use Ramsey\Uuid\Uuid;

return [
    [
        'id' =>  Uuid::uuid4()->toString(),
        'username' => 'erau',
        'auth_key' => 'tUu1qHcde0diwUol3xeI-18MuHkkprQI',
        // password_0
        'password_hash' => '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
        'password_reset_token' => 'RkD_Jw0_8HEedzLk7MM-ZKEFfYR7VbMr_1392559490',
        'created_at' => '2025-10-28 12:00:40',
        'updated_at' => '2025-10-28 12:00:40',
        'email' => 'sfriesen@jenkins.info',
        'status' => Yii::$app->statusManager->getIdByCode('active'),

    ],
    [
        'id' =>  Uuid::uuid4()->toString(),
        'username' => 'test.test',
        'auth_key' => 'O87GkY3_UfmMHYkyezZ7QLfmkKNsllzT',
        // Test1234
        'password_hash' => 'O87GkY3_UfmMHYkyezZ7QLfmkKNsllzT',
        'email' => 'test@mail.com',
        'status' => Yii::$app->statusManager->getIdByCode('inactive'),
        'created_at' => '2025-10-28 12:00:40',
        'updated_at' => '2025-10-28 12:00:40',
        'verification_token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330',
    ],
];
