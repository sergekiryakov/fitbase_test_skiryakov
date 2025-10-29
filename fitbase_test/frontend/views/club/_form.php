<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\Club $model */
/** @var yii\widgets\ActiveForm $form */

$statuses = Yii::$app->statusManager->allForEntity('club');
$statusList = ArrayHelper::map($statuses, 'id', 'label');
?>

<div class="club-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(
        $statusList,
        ['prompt' => Yii::t('app', 'Select status')]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>