<?php
use pendalf89\filemanager\Module;
use pendalf89\filemanager\assets\ModalAsset;

/* @var $this yii\web\View */

$this->title = Module::t('main', 'Files');
$this->params['breadcrumbs'][] = ['label' => Module::t('main', 'Upload file'), 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;

ModalAsset::register($this);
?>

<?= alexantr\elfinder\ElFinder::widget([
    'connectorRoute' => ['site/connector'],
    'settings' => [
        'height' => 640,
    ],
    'buttonNoConflict' => true,
])?>