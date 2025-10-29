<?php

namespace frontend\tests\fixtures;

use yii\test\ActiveFixture;

class ClientFixture extends ActiveFixture
{
    public $modelClass = 'frontend\models\Client';
    public $depends = [
        'common\fixtures\UserFixture',
        'frontend\tests\fixtures\ClubFixture',
        'frontend\tests\fixtures\ClientClubFixture',
    ];
}
