<?php

namespace simialbi\yii2\voting\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%voting}}".
 *
 * @property integer $id
 * @property string $subject
 * @property string $description Details to the voting
 * @property boolean $is_active Currently activated voting
 * @property boolean $is_finished Is the voting finished?
 * @property boolean $is_moderated Is the voting moderated?
 * @property boolean $is_with_mobile_registration Does the user can enter his mobile number in login form and get's the code via SMS
 * @property boolean $show_results
 * @property string $finished_message
 * @property string|integer $created_by
 * @property string|integer $updated_by
 * @property string|integer $created_at
 * @property string|integer $updated_at
 *
 * @property-read Invitee[] $invitees
 * @property-read Question[] $questions
 * @property-read \simialbi\yii2\models\UserInterface $creator
 * @property-read \simialbi\yii2\models\UserInterface $updater
 */
class Voting extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%voting}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['description'], 'string'],
            [['subject'], 'string', 'max' => 255],
            [['is_active', 'is_finished', 'is_moderated', 'is_with_mobile_registration'], 'boolean'],

            [['is_moderated'], 'default', 'value' => true],
            [['is_active', 'is_finished', 'is_with_mobile_registration'], 'default', 'value' => false],
            ['show_results', 'default', 'value' => true, 'when' => function ($model) {
                /** @var static $model */
                return !$model->is_moderated;
            }],

            [['subject', 'is_active', 'is_finished', 'is_moderated', 'is_with_mobile_registration'], 'required'],
            ['finished_message', 'required', 'when' => function ($model) {
                /** @var static $model */
                return !$model->show_results;
            }, 'whenClient' => 'function (attribute, value) {
                var $el = jQuery(\'#' . Html::getInputId($this, 'show_results') . '\');
                return $el.length && !$el.is(\':checked\');
            }'],
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
                    self::EVENT_BEFORE_UPDATE => 'updated_at'
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
            'id' => Yii::t('simialbi/voting/model/voting', 'ID'),
            'subject' => Yii::t('simialbi/voting/model/voting', 'Subject'),
            'description' => Yii::t('simialbi/voting/model/voting', 'Description'),
            'is_active' => Yii::t('simialbi/voting/model/voting', 'Is active'),
            'is_finished' => Yii::t('simialbi/voting/model/voting', 'Is finished'),
            'is_moderated' => Yii::t('simialbi/voting/model/voting', 'Is moderated'),
            'is_with_mobile_registration' => Yii::t('simialbi/voting/model/voting', 'With mobile registration'),
            'show_results' => Yii::t('simialbi/voting/model/voting', 'Show results'),
            'finished_message' => Yii::t('simialbi/voting/model/voting', 'Message after voting ended'),
            'created_by' => Yii::t('simialbi/voting/model/voting', 'Created by'),
            'updated_by' => Yii::t('simialbi/voting/model/voting', 'Updated by'),
            'created_at' => Yii::t('simialbi/voting/model/voting', 'Created at'),
            'updated_at' => Yii::t('simialbi/voting/model/voting', 'Updated at'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeHints(): array
    {
        return [
            'is_with_mobile_registration' => Yii::t(
                'simialbi/voting/model/voting',
                'Does the user can enter his mobile number in login form and get\'s the code via SMS.'
            )
        ];
    }

    /**
     * Gets query for [[VotingInvitees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitees(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Invitee::class, ['voting_id' => 'id']);
    }

    /**
     * Gets query for [[VotingQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Question::class, ['voting_id' => 'id']);
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
