<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo ucfirst(Yii::$app->controller->id); ?>
            <small>it all starts here</small>
        </h1>
        <?php echo \yii\widgets\Breadcrumbs::widget([
            'tag' => 'ol',
            'homeLink' => [
                'label' => '<i class="fa fa-dashboard"></i> Home',
                'url' => ['site/index'],
                'encode' => false,
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
            <div class="alert alert-<?php echo $key; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $message; ?>
            </div>
        <?php endforeach; ?>
        <?php echo $content; ?>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->