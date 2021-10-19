<?php

namespace app\controllers;

use app\models\ImageForm;
use app\services\AddPreviewByColor;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
        $model = new ImageForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {
                $service = new AddPreviewByColor($model->file);
                return $service->execute();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
