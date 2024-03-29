<?php

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\SearchInvitee;
use simialbi\yii2\voting\models\SearchQuestion;
use simialbi\yii2\voting\models\SearchVoting;
use simialbi\yii2\voting\models\Voting;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * VotingController implements the CRUD actions for Voting model.
 */
class VotingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'auth' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['activate'],
                        'roles' => ['manageVoting']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['administrateVoting']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Voting models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new SearchVoting();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $users = ArrayHelper::map(call_user_func([Yii::$app->user->identityClass, 'findIdentities']), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => $users
        ]);
    }

    /**
     * Displays a single Voting model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        $questionSearchModel = new SearchQuestion();
        $questionDataProvider = $questionSearchModel->search(Yii::$app->request->queryParams, $id);
        $inviteeSearchModel = new SearchInvitee();
        $inviteeDataProvider = $inviteeSearchModel->search(Yii::$app->request->queryParams, $id);
        $users = ArrayHelper::map(call_user_func([Yii::$app->user->identityClass, 'findIdentities']), 'id', 'name');

        return $this->render('view', [
            'model' => $this->findModel($id),
            'questionSearchModel' => $questionSearchModel,
            'questionDataProvider' => $questionDataProvider,
            'inviteeSearchModel' => $inviteeSearchModel,
            'inviteeDataProvider' => $inviteeDataProvider,
            'users' => $users
        ]);
    }

    /**
     * Creates a new Voting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Voting();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['question/create', 'votingId' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Voting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['voting/view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Voting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id): \yii\web\Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Activates an existing Voting model.
     *
     * @param integer $id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionActivate(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        $model->is_active = true;
        $model->save();

        return $this->redirect(['voting/index']);
    }

    /**
     * Finds the Voting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param mixed $id
     * @return Voting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Voting
    {
        if (($model = Voting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
