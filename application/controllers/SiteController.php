<?php

namespace app\controllers;
use alexantr\elfinder\CKEditorAction;
use alexantr\elfinder\ConnectorAction;
use alexantr\elfinder\InputFileAction;
use alexantr\elfinder\TinyMCEAction;
use app\models\UploadForm;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\web\Response;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'upload' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'connector' => [
                'class' => ConnectorAction::className(),
                'options' => [
                    'roots' => [
                        [
                            'driver' => 'LocalFileSystem',
                            'path' => Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'uploads',
                            'URL' => Yii::getAlias('@web') . '/uploads/',
                            'mimeDetect' => 'internal',
                            'imgLib' => 'gd',
                            'uploadAllow' => ['image/png', 'image/jpeg', 'image/gif'],
                            'uploadDeny' => ['all'],
                            'uploadOrder' => 'deny,allow',
                            'accessControl' => function ($attr, $path) {
                                // hide files/folders which begins with dot
                                return (strpos(basename($path), '.') === 0) ?
                                    !($attr == 'read' || $attr == 'write') :
                                    null;
                            },
                        ],
                    ],
                ],
            ],
            'input' => [
                'class' => InputFileAction::className(),
                'connectorRoute' => 'connector',
            ],
            'ckeditor' => [
                'class' => CKEditorAction::className(),
                'connectorRoute' => 'connector',
            ],
            'tinymce' => [
                'class' => TinyMCEAction::className(),
                'connectorRoute' => 'connector',
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = false;
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string
     */
    public function actionView(){
        return $this->render('file-manager');
    }

    public function actionUpload(){
        $preview = $config = $errors = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new UploadForm();
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost && Yii::$app->request->post('uploadToken')) {
            $model->file =  UploadedFile::getInstancesByName('file');
            if ($model->file && $model->validate()) {
                foreach ($model->file as $k => $file) {
                    $fileName = $file->baseName . '.' . $file->extension;
                    $fileSize = $file->size;
                    $fileId = $fileName . $k;
                    if ($fileName != "") {
                        $file->saveAs('uploads/' . $fileName);
                        $pathFile =  Url::base(TRUE) . '/uploads/' . $fileName;
//                        $preview[] = "<img src='$pathFile' class='file-preview-image kv-preview-data' />";
                        $preview['initialPreview'] = $pathFile;
                        $preview['initialPreviewAsData'] = true;
                        $a = "copyToClipboard('#link_'$k')";
                        $preview['initialPreviewConfig'][]['key'] = $k;
                        $html = "<div class='input-group row_link'>
                                      <input type='text' class='form-control' value='$pathFile' aria-describedby='basic-addon2' />
                                      <div class='input-group-append'>
                                        <button class='btn btn-default btnCopy' type='button'>Copy image link</button>
                                      </div>
                                 </div>";
                        $config[] = [
                            'key' => $fileId,
                            'caption' => $html,
                            'size' => $fileSize,
//                            'url' => 'http://localhost/delete.php', // server api to delete the file based on key
                        ];
                        $preview['initialPreviewConfig'] = $config;
                    }else{
                        $errors[] = $fileName;
                    }
                }
            }else{
                $errors[0] = 'Some thing error upload file!';
            }
        }
        $out = $preview;
        if (!empty($errors)) {
            $img = count($errors) === 1 ? 'file "' . $errors[0]  . '" ' : 'files: "' . implode('", "', $errors) . '" ';
            $out['error'] = 'We could not upload the ' . $img . 'now. Please try again later.';
        }
        return $out;
    }
//
//    // combine all chunks
//// no exception handling included here - you may wish to incorporate that
//    private function combineChunks($chunks, $targetFile) {
//        // open target file handle
//        $handle = fopen($targetFile, 'a+');
//
//        foreach ($chunks as $file) {
//            fwrite($handle, file_get_contents($file));
//        }
//
//        // you may need to do some checks to see if file
//        // is matching the original (e.g. by comparing file size)
//
//        // after all are done delete the chunks
//        foreach ($chunks as $file) {
//            @unlink($file);
//        }
//
//        // close the file handle
//        fclose($handle);
//    }
//
//// generate and fetch thumbnail for the file
//    private function getThumbnailUrl($path, $fileName) {
//        // assuming this is an image file or video file
//        // generate a compressed smaller version of the file
//        // here and return the status
//        $sourceFile = $path . '/' . $fileName;
//        $targetFile = $path . '/thumbs/' . $fileName;
//        return 'http://localhost/uploads/' . $fileName;
//        //
//        // generateThumbnail: method to generate thumbnail (not included)
//        // using $sourceFile and $targetFile
//        //
////        if (generateThumbnail($sourceFile, $targetFile) === true) {
////            return 'http://localhost/uploads/thumbs/' . $fileName;
////        } else {
////            return 'http://localhost/uploads/' . $fileName; // return the original file
////        }
//    }
}
