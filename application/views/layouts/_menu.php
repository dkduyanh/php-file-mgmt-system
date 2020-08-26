<?php

use app\library\widgets\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <i class="user-image fa fa-user-circle-o fa-3x" style="color: #fff"></i>
            </div>
            <div class="pull-left info">
                <p><?php echo @Yii::$app->user->identity->username ? : 'Guest'; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php
        echo Nav::widget([
            'activateParents' => true,
            'activeCssClass' => 'active',
            'encodeLabels' => false,
            'linkTemplate' => '<a href="{url}">{label}</a>',
            'labelTemplate' => '{label}',
            'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
            'dropDownCaret' => 'fa fa-angle-left pull-right',
            'itemOptions' => [
                //'class' => 'treeview'
            ],
            'options' => [
                'class' => 'sidebar-menu',
                'data-widget' => "tree"
            ],
            'items' => [
                [
                    'label' => 'Dashboard',
                    'url' => ['/site/index'],
                    'icon' => 'fa fa-dashboard'
                ],
                [
                    'label' => 'CMS',
                    'options' => ['class' => 'header']
                ],
                [
                    'label' => 'Upload file',
                    'url' => ['/site/index'],
                    'icon' => 'fa fa-cloud-upload'
                ],
                [
                    'label' => 'File manager',
                    'url' => ['/site/view'],
                    'icon' => 'fa fa-file-image-o'
                ],
            ]
        ]);
        ?>
    </section>
    <!-- /.sidebar -->
</aside>
