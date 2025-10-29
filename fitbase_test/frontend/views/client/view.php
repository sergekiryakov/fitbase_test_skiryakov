<?php

use frontend\models\Client;

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\Client $model */

$this->title = $model->last_name . " " . $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'first_name',
            'last_name',
            'middle_name',
            [
                'attribute' => 'status',
                'value' => fn($model) => Yii::$app->statusManager->getLabelFor($model, (int)$model->status),
                'filter' => ArrayHelper::map(
                    Yii::$app->statusManager->allForEntity('club'),
                    'id',
                    'label'
                ),
            ],
            [
                'label' => 'Clubs',
                'value' => function (Client $model) {
                    return implode(', ', $model->clubs ? ArrayHelper::getColumn($model->clubs, 'name') : []);
                }
            ],
            'gender',
            'birth_date',
            'created_at',
            'updated_at',
            'deleted_at',
            [
                'attribute' => 'created_by',
                'value' => $model->author?->username,
            ],
            [
                'attribute' => 'updated_by',
                'value' => $model->editor?->username,
            ],
            [
                'attribute' => 'deleted_by',
                'value' => $model->deleter?->username,
            ],
        ],
    ]) ?>

</div>