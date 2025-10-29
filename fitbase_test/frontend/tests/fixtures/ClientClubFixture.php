<?php

namespace frontend\tests\fixtures;

use yii\test\ActiveFixture;

class ClientClubFixture extends ActiveFixture
{
    public $modelClass = 'frontend\models\ClientClub';

    public $depends = [
        'frontend\tests\fixtures\ClientFixture',
        'frontend\tests\fixtures\ClubFixture',
    ];
}
