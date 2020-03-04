<?php

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\Invitee;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * InviteeController implements the CRUD actions for Invitee model.
 */
class InviteeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Invitee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $votingId
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate($votingId)
    {
        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->getBodyParam('users', []);
            foreach ($ids as $id) {
                $model = new Invitee([
                    'voting_id' => $votingId,
                    'user_id' => $id
                ]);
                $model->save();

                if ($model->errors) {
                    echo "<pre>";
                    var_dump($model->errors);
                    exit("</pre>");
                }
            }

            return $this->redirect(['voting/view', 'id' => $votingId]);
        }
        $users = ArrayHelper::map(call_user_func([Yii::$app->user->identityClass, 'findIdentities']), 'id', 'name');

        return $this->renderAjax('create', [
            'users' => $users
        ]);
    }

    /**
     * Deletes an existing Invitee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $voting_id
     * @param string $user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($voting_id, $user_id)
    {
        $this->findModel($voting_id, $user_id)->delete();

        return $this->redirect(['voting/view', 'id' => $voting_id]);
    }

    /**
     * Finds the Invitee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $voting_id
     * @param string $user_id
     * @return Invitee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($voting_id, $user_id)
    {
        if (($model = Invitee::findOne(['voting_id' => $voting_id, 'user_id' => $user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
