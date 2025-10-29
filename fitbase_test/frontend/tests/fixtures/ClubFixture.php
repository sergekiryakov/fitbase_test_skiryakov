<?php

namespace frontend\tests\fixtures;

use yii\test\ActiveFixture;

class ClubFixture extends ActiveFixture
{
    public $modelClass = 'frontend\models\Club';
    public $depends = [
        'common\fixtures\UserFixture',
    ];
}
