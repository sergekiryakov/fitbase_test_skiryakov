<?php

use frontend\models\Client;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <p>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Full Name',
                'value' => function (Client $model) {
                    return trim($model->last_name . ' ' . $model->first_name);
                }
            ],
            [
                'attribute' => 'gender',
                'label' => 'Gender',
                'value' => function (Client $model) {
                    return $model->displayGender();
                }
            ],
            [
                'label' => 'Clubs',
                'value' => function (Client $model) {
                    return implode(', ', $model->clubs ? ArrayHelper::getColumn($model->clubs, 'name') : []);
                }
            ],
            [
                'attribute' => 'status',
                'value' => fn($model) => Yii::$app->statusManager->getLabelFor($model, (int)$model->status),
                'filter' => ArrayHelper::map(
                    Yii::$app->statusManager->allForEntity('client'),
                    'id',
                    'label'
                ),
            ],
            'birth_date',
            'created_at',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Client $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>