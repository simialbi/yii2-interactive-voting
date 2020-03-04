<?php

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\Answer;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AnswerController implements the CRUD actions for Answer model.
 */
class AnswerController extends Controller
{
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
////        return [
////            'verbs' => [
////                'class' => VerbFilter::class,
////                'actions' => [
////                    'delete' => ['POST'],
////                ],
////            ],
////        ];
//    }

    /**
     * Creates a new Answer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $questionId The questions primary key
     *
     * @return mixed
     */
    public function actionCreate($questionId)
    {
        $model = new Answer([
            'question_id' => $questionId
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model = new Answer([
                'question_id' => $questionId
            ]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Answer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            $model = new Answer([
                'question_id' => $model->question_id
            ]);

            return $this->renderAjax('create', [
                'model' => $model
            ]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Answer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Answer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Answer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
