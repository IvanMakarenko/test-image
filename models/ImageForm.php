<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ImageForm is the model behind the imae upload form.
 */
class ImageForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg'],
        ];
    }
}
