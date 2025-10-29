<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Club;
use frontend\models\Client;
use frontend\models\ClientSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ],

        );
    }

    /**
     * Lists all Client models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->loadDefaultValues();
        if ($this->processClient($model)) {

            return $this->redirect(['view', 'id' => $model->id]);
        }
        $clubs = Club::find()->active()->all();

        return $this->render('create', [
            'model' => $model,
            'clubs' => $clubs,
            'statuses' => Yii::$app->statusManager->allForEntity('client')
        ]);
    }


    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->clubIds = \yii\helpers\ArrayHelper::getColumn($model->clubs, 'id');
        if ($this->processClient($model)) {

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'clubs' => Club::find()->active()->all(),
            'statuses' => Yii::$app->statusManager->allForEntity('client')
        ]);
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->softDelete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * processClient
     *
     * @param  mixed $model
     * @return bool
     */
    protected function processClient(Client $model): bool
    {
        $model->clubIds = (array)$model->clubIds;
        if (!$model->load(Yii::$app->request->post())) {

            return false;
        }
        if (!$model->save()) {

            return false;
        }
        $this->linkClubs($model);

        return true;
    }
    
    /**
     * linkClubs
     *
     * @param  mixed $model
     * @return void
     */
    protected function linkClubs(Client $model): void
    {
        if (!is_array($model->clubIds)) {
            $model->clubIds = [];
        }
        $model->unlinkAll('clubs', true);
        foreach ($model->clubIds as $clubId) {
            $club = Club::findOne($clubId);
            if (!$club) {
                continue;
            }
            $model->link('clubs', $club);
        }
    }
}
