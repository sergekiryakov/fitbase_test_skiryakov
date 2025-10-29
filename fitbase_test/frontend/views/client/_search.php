<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;
use frontend\models\Client;

/** @var yii\web\View $this */
/** @var frontend\models\ClientSearch $model */
/** @var yii\widgets\ActiveForm $form */

$statusList = ArrayHelper::map(
    Yii::$app->statusManager->allForEntity('client'),
    'id',
    'label'
);

if (empty($statusList)) {
    $statusList = ArrayHelper::map(
        Yii::$app->statusManager->allForEntity(null),
        'id',
        'label'
    );
}
?>
<div class="client-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name')->textInput(['placeholder' => 'Full Name']) ?>

    <?= $form->field($model, 'status')->dropDownList(
        $statusList,
        ['prompt' => 'Select status']
    ) ?>

    <?= $form->field($model, 'gender')->dropDownList(
        Client::optsGender(),
        ['prompt' => 'Select gender']
    ) ?>

    <?= $form->field($model, 'birth_date_range')->widget(\kartik\daterange\DateRangePicker::class, [
        'convertFormat' => true,
        'pluginOptions' => [
            'locale' => [
                'format' => 'Y-m-d',
                'separator' => ' - ',
            ],
            'opens' => 'right',
            'drops' => 'down',
            'showDropdowns' => true,
            'singleDatePicker' => false,
            'autoUpdateInput' => false,
            'linkedCalendars' => false,
            'minDate' => '1900-01-01',
            'maxDate' => date('Y-m-d'),
        ],
        'options' => [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'placeholder' => 'Select birth date range',
        ],
        'pluginEvents' => [
            'apply.daterangepicker' => "function(ev, picker) { 
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        }",
            'cancel.daterangepicker' => "function(ev, picker) { 
            $(this).val('');
        }",
        ],
    ])->label("Birth Date") ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>