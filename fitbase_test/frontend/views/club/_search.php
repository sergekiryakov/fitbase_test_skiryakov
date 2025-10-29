<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\ClubSearch $model */
/** @var yii\widgets\ActiveForm $form */
$statusList = ArrayHelper::map(Yii::$app->statusManager->allForEntity('club'), 'id', 'label');

?>

<div class="club-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>

     <?= $form->field($model, 'status')->dropDownList(
        $statusList,
        ['prompt' => Yii::t('app', 'Select status')]
    ) ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
