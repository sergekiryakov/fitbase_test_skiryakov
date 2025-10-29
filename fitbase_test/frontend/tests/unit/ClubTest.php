<?php

namespace tests\unit\models;

use frontend\models\Club;
use common\models\User;
use Yii;
use Ramsey\Uuid\Uuid;

class ClubTest extends \Codeception\Test\Unit
{
    protected User $testUser;

    protected function _before(): void
    {
        $this->transaction = Yii::$app->db->beginTransaction();
        $this->testUser = new User([
            'id' =>  Uuid::uuid4()->toString(),
            'username' => 'testuser',
            'status' => Yii::$app->statusManager->getIdByCode('active'),
            'email' => 'test@example.com',
            'auth_key' => 'iwTNae9t34OmnK6l4vT4IeaTk-YWI2Rv',
            'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.EylfmQ39O5JlHJVFpNn618OUS1HwaIi',
            'password_reset_token' => 't5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv_' . time(),
            'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
        ]);
        $this->testUser->save(false);

        Yii::$app->set('user', new \yii\web\User([
            'identityClass' => User::class,
            'enableSession' => false,
            'identity' => $this->testUser,
        ]));
    }

    protected function _after(): void
    {
        $this->transaction->rollBack();
    }

    public function testClubValidation()
    {
        $club = new Club();

        $this->assertFalse($club->validate());

        $club->name = 'Fitness Club';
        $club->address = 'Test Address';
        $club->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->assertTrue($club->validate());
    }

    public function testClubSave()
    {
        $club = new Club([
            'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Yoga Club',
        ]);
        $this->assertFalse($club->save());
        $club->address = 'Test Address';

        $this->assertTrue($club->save());
    }
}
