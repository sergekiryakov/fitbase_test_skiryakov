<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\Client;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var Client $model */
/** @var yii\widgets\ActiveForm $form */
/** @var frontend\models\Club[] $clubs */

$clubsList = ArrayHelper::map($clubs, 'id', 'name'); // ключ => label

?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->dropDownList(
        Client::optsGender(),
        ['prompt' => 'Select Gender']
    ) ?>

    <?= $form->field($model, 'birth_date')->widget(DatePicker::class, [
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(
        ArrayHelper::map(Yii::$app->statusManager->allForEntity('client'), 'id', 'label'),
        ['prompt' => 'Select Status']
    ) ?>

    <?= $form->field($model, 'clubIds')->dropDownList(
        $clubsList,
        [
            'multiple' => true,
            'class' => 'form-select',
            'size' => 5,

        ]
    ) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>