<?php

namespace simialbi\yii2\voting\models;

use Yii;
use yii\base\ModelEvent;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%voting_question}}".
 *
 * @property integer $id
 * @property integer $voting_id
 * @property string $subject
 * @property string $description Details to the question
 * @property boolean $is_active Current question to be answered
 * @property boolean $is_finished Is the question finished?
 * @property boolean $multiple_answers_allowed Allow multiple answers?
 * @property string|integer $created_by
 * @property string|integer $updated_by
 * @property string|integer $created_at
 * @property string|integer $updated_at
 * @property string|integer $started_at When did the answering of this question start?
 * @property string|integer $ended_at When did the answering of this question end?
 *
 * @property-read Answer[] $answers
 * @property-read Voting $voting
 * @property-read QuestionAnswer[] $questionAnswers
 * @property-read \simialbi\yii2\models\UserInterface $creator
 * @property-read \simialbi\yii2\models\UserInterface $updater
 */
class Question extends ActiveRecord
{
    /**
     * @event ModelEvent an event that is triggered before stopping this question.
     */
    const EVENT_BEFORE_START = 'beforeStart';
    /**
     * @event ModelEvent an event that is triggered before stopping this question.
     */
    const EVENT_BEFORE_STOP = 'beforeStop';

    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%voting_question}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['voting_id'], 'integer'],
            [['description'], 'string'],
            [['subject'], 'string', 'max' => 255],
            [
                ['voting_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Voting::class,
                'targetAttribute' => ['voting_id' => 'id']
            ],
            [['is_active', 'is_finished', 'multiple_answers_allowed'], 'boolean'],

            [['is_active', 'is_finished', 'multiple_answers_allowed'], 'default', 'value' => false],

            [['voting_id', 'subject', 'is_active', 'is_finished'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => 'updated_by'
                ]
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                    self::EVENT_BEFORE_START => 'started_at',
                    self::EVENT_BEFORE_STOP => 'ended_at'
                ]
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('simialbi/voting/model/voting-question', 'ID'),
            'voting_id' => Yii::t('simialbi/voting/model/voting-question', 'Voting'),
            'subject' => Yii::t('simialbi/voting/model/voting-question', 'Subject'),
            'description' => Yii::t('simialbi/voting/model/voting-question', 'Description'),
            'is_active' => Yii::t('simialbi/voting/model/voting-question', 'Is active'),
            'is_finished' => Yii::t('simialbi/voting/model/voting-question', 'Is finished'),
            'multiple_answers_allowed' => Yii::t('simialbi/voting/model/voting-question', 'Allow multiple answers?'),
            'created_by' => Yii::t('simialbi/voting/model/voting-question', 'Created by'),
            'updated_by' => Yii::t('simialbi/voting/model/voting-question', 'Updated by'),
            'created_at' => Yii::t('simialbi/voting/model/voting-question', 'Created at'),
            'updated_at' => Yii::t('simialbi/voting/model/voting-question', 'Updated at'),
            'started_at' => Yii::t('simialbi/voting/model/voting-question', 'Started at'),
            'ended_at' => Yii::t('simialbi/voting/model/voting-question', 'Ended at'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if (!$insert) {
            if ($this->isAttributeChanged('is_active')) {
                $event = new ModelEvent();
                $this->trigger(self::EVENT_BEFORE_START, $event);
            } elseif ($this->isAttributeChanged('is_finished')) {
                $event = new ModelEvent();
                $this->trigger(self::EVENT_BEFORE_STOP, $event);
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[VotingAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Answer::class, ['question_id' => 'id']);
    }

    /**
     * Gets query for [[Voting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVoting(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Voting::class, ['id' => 'voting_id']);
    }

    /**
     * Gets query for [[VotingQuestionAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionAnswers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(QuestionAnswer::class, ['question_id' => 'id']);
    }

    /**
     * Get creator
     * @return \simialbi\yii2\models\UserInterface|null
     */
    public function getCreator(): ?\simialbi\yii2\models\UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->created_by);
    }

    /**
     * Get user last updated
     * @return \simialbi\yii2\models\UserInterface|null
     */
    public function getUpdater(): ?\simialbi\yii2\models\UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->updated_by);
    }
}
