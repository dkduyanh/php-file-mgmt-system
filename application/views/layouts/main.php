<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?php echo Html::encode($this->title) ?> - <?php echo Yii::$app->name; ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini <?php if(isset($_COOKIE['toggleState']) && $_COOKIE['toggleState'] == 'closed'): ?> sidebar-collapse<?php endif; ?>">
<?php $this->beginBody() ?>

<div class="wrapper">
    <?php echo $this->render('_header.php'); ?>
    <?php echo $this->render('_menu.php'); ?>
    <?php echo $this->render('_content.php', ['content' => $content]) ?>
    <?php echo $this->render('_footer.php'); ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
