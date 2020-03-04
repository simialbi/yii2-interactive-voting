<?php

namespace simialbi\yii2\voting\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%voting_answer}}".
 *
 * @property integer $id
 * @property integer $question_id
 * @property string $text
 * @property string|integer $created_by
 * @property string|integer $updated_by
 * @property string|integer $created_at
 * @property string|integer $updated_at
 *
 * @property-read Question $question
 * @property-read Voting $voting
 * @property-read \simialbi\yii2\models\UserInterface $creator
 * @property-read \simialbi\yii2\models\UserInterface $updater
 */
class Answer extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return '{{%voting_answer}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['question_id'], 'integer'],
            [['text'], 'string', 'max' => 255],
            [
                ['question_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Question::class,
                'targetAttribute' => ['question_id' => 'id']
            ],

            [['question_id', 'text'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('simialbi/voting/model/voting-answer', 'ID'),
            'question_id' => Yii::t('simialbi/voting/model/voting-answer', 'Question'),
            'text' => Yii::t('simialbi/voting/model/voting-answer', 'Text'),
            'created_by' => Yii::t('simialbi/voting/model/voting-answer', 'Created by'),
            'updated_by' => Yii::t('simialbi/voting/model/voting-answer', 'Updated by'),
            'created_at' => Yii::t('simialbi/voting/model/voting-answer', 'Created at'),
            'updated_at' => Yii::t('simialbi/voting/model/voting-answer', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * Gets query for [[Voting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVoting()
    {
        return $this->hasOne(Voting::class, ['id' => 'voting_id'])
            ->via('question');
    }

    /**
     * Get creator
     * @return \simialbi\yii2\models\UserInterface|null
     */
    public function getCreator()
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->created_by);
    }

    /**
     * Get user last updated
     * @return \simialbi\yii2\models\UserInterface|null
     */
    public function getUpdater()
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->updated_by);
    }
}
