<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php echo Html::csrfMetaTags() ?>
        <title><?php echo Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition login-page">
    <?php $this->beginBody() ?>
    <div class="login-box">
        <div class="login-logo">
            <a href="javascript:void(0);"><b>File Manager</b>Login</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?php echo $form->field($model, 'username', [
                'options' => ['class' => 'form-group has-feedback'],
                'template' => "{label}\n{input}\n{hint}\n{error}".'<span class="glyphicon glyphicon-user form-control-feedback"></span>'
            ])->textInput(['autofocus' => true, 'placeholder' => 'Username'])->label(false) ?>
            <?php echo $form->field($model, 'password', [
                'options' => ['class' => 'form-group has-feedback'],
                'template' => "{label}\n{input}\n{hint}\n{error}".'<span class="glyphicon glyphicon-lock form-control-feedback"></span>'
            ])->passwordInput(['placeholder' => 'Password'])->label(false) ?>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-8">
                    <?php echo Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                </div>
                <!-- /.col -->
            </div>
            <?php ActiveForm::end(); ?>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>