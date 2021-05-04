<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\controllers;

use simialbi\yii2\voting\models\Invitee;
use simialbi\yii2\voting\models\LoginForm;
use simialbi\yii2\voting\models\LoginMobileForm;
use simialbi\yii2\voting\models\Question;
use simialbi\yii2\voting\models\QuestionAnswer;
use simialbi\yii2\voting\models\Voting;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * Class DefaultController
 * @package simialbi\yii2\voting\controllers
 *
 * @property-read \simialbi\yii2\voting\Module $module
 */
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
                        'actions' => ['login', 'login-mobile', 'login-token'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['live', 'chart-data', 'status']
                    ]
                ]
            ]
        ];
    }

    /**
     * List all active votings
     * @return string
     */
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

    /**
     * Display active question or result of a specific voting
     *
     * @param integer $votingId
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($votingId)
    {
        $model = $this->findVotingModel($votingId);

        $query = $model->getQuestions()->alias('q')->orderBy(['{{q}}.[[started_at]]' => SORT_ASC]);
        $query2 = clone $query;
        $subQuery = (new Query())
            ->select(new Expression('COUNT({{qa}}.[[question_id]])'))
            ->from(['qa' => '{{%voting_question_user_answered}}'])
            ->where('{{qa}}.[[question_id]] = {{q}}.[[id]]')
            ->andWhere(['{{qa}}.[[user_id]]' => Yii::$app->user->id]);
        $question = $query->where(['=', $subQuery, 0]);
        $lastQuestion = $query2->leftJoin(
            ['qa' => '{{%voting_question_user_answered}}'],
            '{{qa}}.[[question_id]] = {{q}}.[[id]] AND {{qa}}.[[user_id]] = :userId',
            [':userId' => Yii::$app->user->id]
        );
        if ($model->is_moderated) {
            $question->andWhere(['{{q}}.[[is_active]]' => true, '{{q}}.[[is_finished]]' => false]);
            $lastQuestion
                ->where(['{{q}}.[[is_active]]' => true])
                ->andWhere([
                    'or',
                    ['{{q}}.[[is_finished]]' => true],
                    ['not', ['{{qa}}.[[question_id]]' => null]]
                ])
                ->orderBy(['{{q}}.[[started_at]]' => SORT_DESC]);
        } else {
            $question->orderBy(['{{q}}.[[created_at]]' => SORT_ASC]);
            $lastQuestion
                ->where(['not', ['{{qa}}.[[question_id]]' => null]])
                ->orderBy(['{{q}}.[[id]]' => SORT_ASC]);
        }

        return $this->render('view', [
            'voting' => $model,
            'question' => $question->one(),
            'lastQuestion' => $model->is_moderated ? [$lastQuestion->one()] : $lastQuestion->all()
        ]);
    }

    /**
     * Display live results of the latest question of a voting
     *
     * @param integer $votingId
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionLive($votingId)
    {
        $model = $this->findVotingModel($votingId);
        $query = $model->getQuestions()->alias('q')->orderBy(['{{q}}.[[created_at]]' => SORT_ASC]);
        $lastQuestion = $query
            ->where(['{{q}}.[[is_active]]' => true])
            ->orderBy(['{{q}}.[[started_at]]' => SORT_DESC]);

        return $this->render('live', [
            'voting' => $model,
            'lastQuestion' => $lastQuestion->one()
        ]);
    }

    /**
     * Save answer
     *
     * @param integer $questionId
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSaveAnswer($questionId)
    {
        $question = $this->findQuestionModel($questionId);

        if (Yii::$app->request->post() && null !== ($answers = Yii::$app->request->getBodyParam('answer'))) {
            if (!is_array($answers)) {
                $answers = [$answers];
            }
            if (!$question->multiple_answers_allowed) {
                $answers = [$answers[0]];
            }
            $anonymous = Yii::$app->request->getBodyParam('anonymous', false);
            foreach ($answers as $answer) {
                $questionAnswer = new QuestionAnswer([
                    'user_id' => $anonymous ? null : (string)Yii::$app->user->id,
                    'user_ip' => Yii::$app->request->userIP,
                    'session_id' => Yii::$app->session->id,
                    'question_id' => $questionId,
                    'answer_id' => $answer
                ]);
                $questionAnswer->save();
            }
            Yii::$app->db->createCommand()->insert('{{%voting_question_user_answered}}', [
                'question_id' => $questionId,
                'user_id' => Yii::$app->user->id
            ])->execute();
        } else {
            Yii::$app->session->addFlash('warning', Yii::t('simialbi/voting', 'You must select one of the options.'));
        }

        return $this->redirect(['view', 'votingId' => $question->voting_id]);
    }

    /**
     * Load updated chart data. Result is a JSON response.
     *
     * @param integer $questionId
     *
     * @return array
     * @throws NotFoundHttpException
     */
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

    /**
     * Poll voting status. Answer is a JSON response.
     *
     * @param integer $votingId
     * @param string|null $referrer
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionStatus($votingId, $referrer = null)
    {
        $model = $this->findVotingModel($votingId);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $question = $model->getQuestions()
            ->alias('q')
            ->where(['{{q}}.[[is_active]]' => true, '{{q}}.[[is_finished]]' => false])
            ->orderBy(['{{q}}.[[created_at]]' => SORT_ASC]);
        if (!Yii::$app->user->isGuest && null === $referrer) {
            $subQuery = (new Query())
                ->select(new Expression('COUNT({{qa}}.[[question_id]])'))
                ->from(['qa' => '{{%voting_question_user_answered}}'])
                ->where('{{qa}}.[[question_id]] = {{q}}.[[id]]')
                ->andWhere(['{{qa}}.[[user_id]]' => Yii::$app->user->id]);
            $question->andWhere(['=', $subQuery, 0]);
        }

        if ($question->count('{{q}}.[[id]]')) {
            if (null !== $referrer) {
                return [
                    'action' => 'redirect',
                    'target' => Url::to(["default/$referrer", 'votingId' => $votingId], true),
                    'newQuestion' => $question->one()->toArray()
                ];
            } else {
                return ['action' => 'redirect', 'target' => Url::to(['default/view', 'votingId' => $votingId], true)];
            }
        }

        return ['action' => 'poll'];
    }

    /**
     * Login with email and code
     *
     * @return string|Response
     */
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
                    Yii::$app->user->login($identity, 3600 * 5);

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
     * @return string
     * @throws UnauthorizedHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionLoginMobile()
    {
        $model = new LoginMobileForm();
        $model->scenario = $model::SCENARIO_STEP_1;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $identities = ArrayHelper::index(
                call_user_func([Yii::$app->user->identityClass, 'findIdentities']),
                $this->module->usernameField
            );
            /** @var \simialbi\yii2\models\UserInterface $identity */
            $identity = ArrayHelper::remove($identities, $model->username);
            $id = ArrayHelper::getValue($identity, 'id');

            if (!$id) {
                $identity = ArrayHelper::remove($identities, '0' . $model->username);
                $id = ArrayHelper::getValue($identity, 'id');

                if (!$id) {
                    throw new UnauthorizedHttpException();
                }
            }
            $query = Invitee::find()
                ->alias('i')
                ->innerJoinWith('voting v')
                ->where(['{{i}}.[[user_id]]' => $id])
                ->andWhere(['{{v}}.[[is_with_mobile_registration]]' => true])
                ->andWhere(['{{v}}.[[is_active]]' => true])
                ->andWhere(['{{v}}.[[is_finished]]' => false]);
            if (!$query->count('id')) {
                throw new UnauthorizedHttpException();
            }
            /** @var \simialbi\yii2\voting\models\Invitee $invitee */
            $invitee = $query->one();
            $mobile = ArrayHelper::getValue($identity, $this->module->mobileField);
            switch ($model->scenario) {
                case $model::SCENARIO_STEP_1:
                default:
                    if (empty($mobile)) {
                        $model->scenario = $model::SCENARIO_STEP_2;
                        break;
                    } else {
                        $model->mobile = $mobile;
                    }
                case $model::SCENARIO_STEP_2:
                    $identity->{$this->module->mobileField} = $model->mobile;
                    call_user_func([$identity, 'save']);
                    call_user_func([$invitee->user, 'refresh']);

                    $response = $this->sendLoginCode($invitee);

                    if (!$response->isOk) {
                        Yii::$app->session->addFlash('danger', Yii::t(
                            'simialbi/voting/notifications',
                            'There was an error sending you your code: {error}',
                            ['error' => $response->statusMessage]
                        ));
                        $model->scenario = $model::SCENARIO_STEP_1;
                    }

                    $model->scenario = $model::SCENARIO_STEP_3;
                    break;
                case $model::SCENARIO_STEP_3:
                    $invitee = $query->andWhere(['code' => $model->code])->one();
                    if (!$invitee) {
                        Yii::$app->session->addFlash('danger', Yii::t(
                            'simialbi/voting/notifications',
                            'The user member combination is not known'
                        ));
                        $model = new LoginMobileForm();
                        $model->scenario = $model::SCENARIO_STEP_1;
                    }

                    Yii::$app->user->login($identity, 3600 * 5);

                    return $this->redirect(['index']);
            }
        }

        return $this->render("login-{$model->scenario}", [
            'model' => $model
        ]);
    }

    /**
     * Log in a user by identity token
     * @param string $token
     * @throws UnauthorizedHttpException
     */
    public function actionLoginToken($token)
    {
        if (!Yii::$app->user->loginByAccessToken($token)) {
            throw new UnauthorizedHttpException();
        }

        return $this->redirect(['index']);
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

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
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

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Send login code
     * @param Invitee $invitee
     * @return \simialbi\yii2\websms\Response
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected function sendLoginCode($invitee)
    {
        /** @var \simialbi\yii2\websms\Connection $sms */
        $sms = $this->module->get('sms', true);
        $voting = $invitee->voting;
        $message = $sms->createMessage();
        $message
            ->id("voting-{$voting->id}-code-{$invitee->user_id}")
            ->category($message::CATEGORY_INFORMATIONAL)
            ->content(Yii::t('simialbi/voting', "Your Code for {voting}\n{code}", [
                'voting' => $voting->subject,
                'code' => $invitee->code
            ]))
            ->type($message::MESSAGE_TYPE_TEXT)
            ->addRecipient(preg_replace('#[^0-9]#', '', $invitee->user->{$this->module->mobileField}));
        return $message->send();
    }
}
