<?php


namespace app\models;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'gif, jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 10],
        ];
    }
}