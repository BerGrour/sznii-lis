<?php

namespace frontend\controllers;

use common\models\File;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * FileController implements the CRUD actions for Files model.
 */
class FileController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
    /**
     * Метод для открытия файла и проверки доступа
     * 
     * @param int $id инидекс файла
     * @return \yii\web\Response
     */
    public function actionDocument($id)
    {
        $file = File::findOne($id);

        if ($file->hasAccess()) {
            return Yii::$app->response->sendFile(
                $file->pathLink,
                $file->filename,
                ['inline' => true]
            );
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
    }
}
