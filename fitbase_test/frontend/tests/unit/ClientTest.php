<?php

namespace tests\unit\models;

use frontend\models\Client;
use frontend\models\Club;
use common\models\User;
use Yii;
use Ramsey\Uuid\Uuid;

class ClientTest extends \Codeception\Test\Unit
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

    public function testClientValidation()
    {
        $client = new Client();

        $this->assertFalse($client->validate());

        $client->first_name = 'John';
        $client->last_name = 'Doe';
        $client->gender = Client::GENDER_MALE;
        $client->birth_date = '2000-01-01';
        $client->status = 10;

        $this->assertTrue($client->validate());
    }

    public function testDefaultStatus()
    {
        $client = new Client();
        $client->first_name = 'Jane';
        $client->last_name = 'Doe';
        $client->gender = Client::GENDER_FEMALE;
        $client->birth_date = '1990-05-01';

        $client->save();
        $this->assertNotNull($client->status, 'Status should be set by default');
    }

    public function testClubLinking()
    {
        $client = new Client([
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => Client::GENDER_MALE,
            'birth_date' => '1995-02-02',
            'status' => 10
        ]);
        $this->assertTrue($client->save());

        $club = new Club(['name' => 'Test Club', 'address' => 'Test Address']);
        $this->assertTrue($club->save());

        $client->link('clubs', $club);

        $this->assertContains($club->id, $client->getClubs()->select('id')->column());
    }
}
