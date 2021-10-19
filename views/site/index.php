<?php

/* @var $this yii\web\View */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Test task - detect main color';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Test task - detect main color</h1>

        <p class="lead">
            Определите основной цвет изображения и вставьте на него подходящий водяной знак.
            Если основной цвет картинки красный - то водяной знак (test) черный, далее основной цвет синий - водяной знак (test) желтый, приоритетный цвет зеленый - водяной знак (test) красный.
        </p>

        <p>
            <?= Html::a('Download examples', 'https://drive.google.com/drive/folders/1J-mqGTpYzGf98Rad9wEosILtQBNdJCEk?usp', [
                'class'  => 'btn btn-lg btn-success',
                'target' => '_blank',
            ]); ?>
        </p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Select file for download</h2>

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                    <?= $form->field($model, 'file')->fileInput([
                            'onChange' => '$(this).next(".form-text").text( $(this).val() );',
                        ])
                        ->hint(' ')
                        ->label('Select file', [
                            'class' => 'btn btn-outline-primary',
                        ]) ?>
                    <button class="btn btn-lg btn-dark">Upload</button>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
.field-imageform-file input {
    display: none;
}
CSS
);