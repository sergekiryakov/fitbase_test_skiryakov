<?php

use Ramsey\Uuid\Uuid;

return [
    [
        'id' =>  Uuid::uuid4()->toString(),
        'username' => 'bayer.hudson',
        'auth_key' => 'HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR',
        //password_0
        'password_hash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'password_reset_token' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312317',
        'status' => Yii::$app->statusManager->getIdByCode('active'),
        'created_at' => '2025-10-28 12:00:40',
        'updated_at' => '2025-10-28 12:00:40',
        'email' => 'nicole.paucek@schultz.info',
    ],
];
