<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Client $model */

$this->title = 'Update Client: ' . $model->last_name . " " . $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clubs' => $clubs,
        'statuses' => $statuses
    ]) ?>

</div>