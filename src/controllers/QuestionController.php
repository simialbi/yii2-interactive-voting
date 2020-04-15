<?php

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\Answer;
use simialbi\yii2\voting\models\Question;
use simialbi\yii2\voting\models\SearchAnswer;
use simialbi\yii2\voting\models\SearchQuestion;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * QuestionController implements the CRUD actions for Question model.
 */
class QuestionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
                        'actions' => ['activate', 'deactivate'],
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
     * Lists all Question models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchQuestion();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Question model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */


    /**
     * Show question details (answers)
     *
     * @param integer|null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id = null)
    {
        if (null === $id && Yii::$app->request->isPost) {
            $id = Yii::$app->request->getBodyParam('expandRowKey');
        }

        $model = $this->findModel($id);
        $searchModel = new SearchAnswer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);
        $users = ArrayHelper::map(call_user_func([Yii::$app->user->identityClass, 'findIdentities']), 'id', 'name');

        return $this->renderAjax('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => $users
        ]);
    }

    /**
     * Creates a new Question model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $votingId The votings id
     *
     * @return mixed
     */
    public function actionCreate($votingId)
    {
        $model = new Question([
            'voting_id' => $votingId
        ]);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $answerModel = new Answer([
                    'question_id' => $model->id
                ]);
                return $this->renderAjax('create-answers', [
                    'model' => $model,
                    'answerModel' => $answerModel
                ]);
            }

            return $this->renderAjax('_form', [
                'model' => $model
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Question model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Question model.
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

        return $this->redirect(['voting/view', 'id' => $model->voting_id]);
    }

    /**
     * Activates an existing Question model.
     * If another question or another voting is active, a warning will be displayed and question not activated.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);

        if (!$model->voting->is_active) { // TODO: move to events
            $voting = $model->voting;
            $voting->is_active = true;
            $voting->save();
        }

        $query = $model->voting->getQuestions()->where(['is_active' => true, 'is_finished' => false])
            ->andWhere(['<>', 'id', $model->id]);
        if ($query->count()) {
            Yii::$app->session->addFlash('warning', Yii::t(
                'simialbi/voting/question',
                'There is another question. Please end it first.'
            ));

            return $this->redirect(['voting/view', 'id' => $model->voting_id]);
        }

        $model->is_active = true;
        $model->save();

        return $this->redirect(['voting/view', 'id' => $model->voting_id]);
    }

    /**
     * Deactivates an existing Question model.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDeactivate($id)
    {
        $model = $this->findModel($id);

        $model->is_finished = true;
        $model->save();

        return $this->redirect(['voting/view', 'id' => $model->voting_id]);
    }

    /**
     * Finds the Question model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Question the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Question::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
