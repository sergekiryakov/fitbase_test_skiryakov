<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use frontend\models\Client;
use yii\helpers\Url;
use common\fixtures\UserFixture;
use frontend\tests\fixtures\ClientClubFixture;
use frontend\tests\fixtures\ClientFixture;
use frontend\tests\fixtures\ClubFixture;

/**
 * Functional tests for Client CRUD
 */
class ClientCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'author.php'
            ],
            'club' => [
                'class' => ClubFixture::class,
                'dataFile' => codecept_data_dir() . 'club.php'
            ],
            'client' => [
                'class' => ClientFixture::class,
                'dataFile' => codecept_data_dir() . 'client.php'
            ],
            'client_club' => [
                'class' => ClientClubFixture::class,
                'dataFile' => codecept_data_dir() . 'client_club.php'
            ]
        ];
    }

    private string $userId = '00000000-0000-0000-0000-000000000001';
    private string $clubId = '00000000-0000-0000-0000-000000000100';
    private string $clientId = '00000000-0000-0000-0000-000000001000';
    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->userId);
        $I->amOnPage(Url::to(['/client/index']));
    }

    public function ensureIndexPageAccessible(FunctionalTester $I)
    {
        $I->see('Clients', 'h1');
        $I->seeLink('Create Client');
    }

    public function ensureCreateClientWorks(FunctionalTester $I)
    {
        $I->amOnPage(Url::to(['/client/create']));
        $I->see('Create Client', 'h1');

        $I->seeElement('select[name="Client[clubIds][]"]');
        $I->see('Test Club', 'select[name="Client[clubIds][]"] option');

        $I->fillField('Client[first_name]', 'John');
        $I->fillField('Client[last_name]', 'Doe');
        $I->fillField('Client[middle_name]', 'Q');
        $I->selectOption('Client[gender]', Client::GENDER_MALE);
        $I->fillField('Client[birth_date]', '1990-01-01');

        $I->selectOption('Client[status]', (string)\Yii::$app->statusManager->getIdByCode('active'));
        $I->selectOption('Client[clubIds][]', $this->clubId);
        $I->click('Save');
        $I->see('Doe');
        $I->seeRecord(Client::class, ['last_name' => 'Doe']);
    }

    public function ensureViewPageShowsData(FunctionalTester $I)
    {
        $I->amOnPage(Url::to(['/client/view', 'id' => $this->clientId]));
        $I->see('Test');
        $I->see('Client');
        $I->see('Test Club');
    }

    public function ensureUpdateClientWorks(FunctionalTester $I)
    {
        $I->seeRecord(Client::class, ['last_name' => 'Client']);
        $I->amOnPage(Url::to(['/client/update', 'id' => $this->clientId]));
        $I->see('Save', 'button, a');
        $I->fillField('Client[first_name]', 'Johnny');
        $I->click('Save');
        $I->seeRecord(Client::class, ['first_name' => 'Johnny']);
        $I->see('Johnny');
    }

    public function ensureDeleteClientSoftDeletes(FunctionalTester $I)
    {
        $I->seeRecord(Client::class, [
            'last_name' => 'Client',
            'status' => (string)\Yii::$app->statusManager->getIdByCode('active')

        ]);
        $I->seeRecord(Client::class, ['last_name' => 'Client']);
        $I->amOnPage(Url::to(['/client/view', 'id' => $this->clientId]));
        $I->see('Delete', 'button, a');
        $deleteUrl = Url::to(['/client/delete', 'id' => $this->clientId]);
        $I->sendAjaxPostRequest($deleteUrl);

        $I->seeRecord(Client::class, [
            'last_name' => 'Client',
            'status' => (string)\Yii::$app->statusManager->getIdByCode('deleted')

        ]);
    }
}
