<?php
/**
 * @var $loginType
 * @var $loginId
 * @var $this yii\web\View
 * @var $user app\models\dao\Customer
 */
use yii\helpers\Html;

$loginLink = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>
<p>Dear <?php echo $user->full_name; ?>,</p>

<p>Welcome to <?php echo APP_NAME; ?>. </p>
<p>Your account has been created and is now ready to use.</p>
<div style="text-align: center; min-height: 300px; margin: 15px; background-color: #efefef; border: solid 1px #ccc">
    <p>Your account information:</p>
    <p style="color: #64434a">Login: <?php echo $user->email;?></p>
    <p style="color: #64434a; margin-bottom: 15px">Email: <?php echo $user->email;?></p>
    <p><?= Html::a('Login now', $loginLink, ['style' => 'display: inline-block; padding: 7px; border: solid 1px #ddd; margin: 10px; background-color: #fff']) ?></p>
</div>

<br/>

<p>If you have any questions, please contact administrator.</p>
<p>Sincerely,<br>
<p><?php echo APP_NAME; ?></p>