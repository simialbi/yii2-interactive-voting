<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\Invitee;
use simialbi\yii2\voting\models\LoginForm;
use simialbi\yii2\voting\models\Question;
use simialbi\yii2\voting\models\QuestionAnswer;
use simialbi\yii2\voting\models\Voting;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $votings = Voting::find()
            ->alias('v')
            ->innerJoinWith('invitees i')
            ->where(['{{i}}.[[user_id]]' => Yii::$app->user->id])
            ->andWhere(['{{v}}.[[is_active]]' => true])
            ->andWhere(['{{v}}.[[is_finished]]' => false]);

        return $this->render('index', [
            'votings' => $votings->all()
        ]);
    }

    public function actionView($votingId)
    {
        $model = $this->findVotingModel($votingId);

        $query = $model->getQuestions()->alias('q')->orderBy(['{{q}}.[[created_at]]' => SORT_ASC]);
        $subQuery = QuestionAnswer::find()
            ->select(new Expression('COUNT({{qa}}.[[id]])'))
            ->alias('qa')
            ->where('{{qa}}.[[question_id]] = {{q}}.[[id]]')
            ->andWhere(['{{qa}}.[[session_id]]' => Yii::$app->session->id]);
        $query2 = clone $query;
        $question = $query
            ->where(['{{q}}.[[is_active]]' => true, '{{q}}.[[is_finished]]' => false])
            ->andWhere(['=', $subQuery, 0]);
        $lastQuestion = $query2
            ->joinWith([
                'questionAnswers qa' => function ($query) {
                    /** @var $query \yii\db\ActiveQuery */
                    $query->andOnCondition(['{{qa}}.[[session_id]]' => Yii::$app->session->id]);
                }
            ])
            ->where(['{{q}}.[[is_active]]' => true])
            ->andWhere([
                'or',
                ['{{q}}.[[is_finished]]' => true],
                ['not', ['{{qa}}.[[id]]' => null]]
            ])
            ->orderBy(['{{q}}.[[created_at]]' => SORT_DESC]);


//        echo "<pre>";
//        var_dump($query->createCommand()->rawSql);
//        exit("</pre>");

        return $this->render('view', [
            'voting' => $model,
            'question' => $question->one(),
            'lastQuestion' => $lastQuestion->one()
        ]);
    }

    public function actionSaveAnswer($questionId)
    {
        $question = $this->findQuestionModel($questionId);

        if (Yii::$app->request->post() && null !== ($answer = Yii::$app->request->getBodyParam('answer'))) {
            $anonymous = Yii::$app->request->getBodyParam('anonymous', false);
            $questionAnswer = new QuestionAnswer([
                'user_id' => $anonymous ? null : (string)Yii::$app->user->id,
                'user_ip' => Yii::$app->request->userIP,
                'session_id' => Yii::$app->session->id,
                'question_id' => $questionId,
                'answer_id' => $answer
            ]);
            $questionAnswer->save();

            return $this->redirect(['view', 'votingId' => $question->voting_id]);
        }

        return $this->renderAjax();
    }

    public function actionChartData($questionId)
    {
        $model = $this->findQuestionModel($questionId);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = [];
        foreach ($model->answers as $answer) {
            $data[$answer->id] = [
                'answer' => $answer->text,
                'count' => 0
            ];
        }
        foreach ($model->questionAnswers as $questionAnswer) {
            $data[$questionAnswer->answer_id]['count']++;
        }

        return array_values($data);
    }

    public function actionStatus($votingId)
    {
        $model = $this->findVotingModel($votingId);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $newQuestion = $model->getQuestions()->alias('q')->orderBy(['{{q}}.[[created_at]]' => SORT_ASC]);
        $subQuery = QuestionAnswer::find()
            ->select(new Expression('COUNT({{qa}}.[[id]])'))
            ->alias('qa')
            ->where('{{qa}}.[[question_id]] = {{q}}.[[id]]')
            ->andWhere(['{{qa}}.[[session_id]]' => Yii::$app->session->id]);
        $question = $newQuestion
            ->where(['{{q}}.[[is_active]]' => true, '{{q}}.[[is_finished]]' => false])
            ->andWhere(['=', $subQuery, 0]);

        if ($question->count('{{q}}.[[id]]')) {
            return ['action' => 'redirect', 'target' => Url::to(['default/view', 'votingId' => $votingId], true)];
        }

        return ['action' => 'poll'];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            $identities = ArrayHelper::index(
                call_user_func([Yii::$app->user->identityClass, 'findIdentities']),
                'email'
            );
            $id = ArrayHelper::getValue($identities, [$model->username, 'id']);

            if ($id) {
                $invitee = Invitee::findOne(['user_id' => $id, 'code' => $model->code]);
                if ($invitee) {
                    $identity = call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $id);
                    Yii::$app->user->login($identity, 3600 * 3);

                    return $this->redirect(['index']);
                }
            }

            $model->addError(
                'username',
                Yii::t('simialbi/voting/default', 'Could not find an invitation with this credentials')
            );
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Voting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Voting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findVotingModel($id)
    {
        if (($model = Voting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('simialbi/voting/question', 'The requested page does not exist.'));
    }

    /**
     * Finds the Question model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Question the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findQuestionModel($id)
    {
        if (($model = Question::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('simialbi/voting/question', 'The requested page does not exist.'));
    }
}