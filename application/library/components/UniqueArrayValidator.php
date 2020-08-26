<?php

namespace app\library\components;

use Yii;
use yii\validators\Validator;

/**
 * Class UniqueArrayValidator
 * This class checks if array has duplicated items
 * @package app\library\components
 * @author: DuyAnh <dkduyanh17@gmail.com>
 */
class UniqueArrayValidator extends Validator
{
    /*public function validateAttribute($model, $attribute)
    {
        parent::validateAttribute($model, $attribute);

        if(!is_array($model->$attribute)){
            $this->addError($model, $attribute, Yii::t('yii', '{attribute} must be an array.'), ['attribute' => $attribute]);
        }

        if(count(array_unique($model->$attribute))<count($model->$attribute))
        {
            // Array has duplicates
            $this->addError($model, $attribute, Yii::t('yii', '{attribute} has duplicates.'), ['attribute' => $attribute]);
        }
    }*/

    protected function validateValue($value)
    {
        $valid = true;
        if(!is_array($value)){
            $valid = false;
            $this->message = Yii::t('yii', '{attribute} must be an array.');
        }

        // Array has duplicates
        if(count(array_unique($value))<count($value))
        {
            $valid = false;
            $this->message = Yii::t('yii', '{attribute} has duplicate values.');
        }

        return $valid ? null : [$this->message, []];
    }
}