<?php

namespace simialbi\yii2\voting\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%voting_question_answer}}".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $answer_id
 * @property string $user_id
 * @property string|integer $user_ip
 * @property string $session_id
 * @property string|integer $created_at
 *
 * @property-read Question $question
 * @property-read Answer $answer
 */
class QuestionAnswer extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return '{{%voting_question_answer}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'answer_id'], 'integer'],
            [['user_id'], 'string', 'max' => 64],
            [['session_id'], 'string', 'max' => 255],
            [
                ['question_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Question::class,
                'targetAttribute' => ['question_id' => 'id']
            ],
            [
                ['answer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Answer::class,
                'targetAttribute' => ['answer_id' => 'id']
            ],
            [['question_id'], 'unique', 'targetAttribute' => ['question_id', 'user_id', 'session_id']],
            ['user_ip', 'ip'],

            [['question_id', 'answer_id', 'user_ip', 'session_id'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_at',
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
            'id' => Yii::t('simialbi/voting/model/voting-question-answer', 'ID'),
            'question_id' => Yii::t('simialbi/voting/model/voting-question-answer', 'Question'),
            'answer_id' => Yii::t('simialbi/voting/model/voting-question-answer', 'Answer'),
            'user_id' => Yii::t('simialbi/voting/model/voting-question-answer', 'User'),
            'user_ip' => Yii::t('simialbi/voting/model/voting-question-answer', 'User Ip'),
            'session_id' => Yii::t('simialbi/voting/model/voting-question-answer', 'Session'),
            'created_at' => Yii::t('simialbi/voting/model/voting-question-answer', 'Created at'),
        ];
    }

    /**
     * {@inheritDoc}
     * @throws \yii\base\NotSupportedException
     */
    public function beforeSave($insert)
    {
        if ($this->user_ip && !is_numeric($this->user_ip)) {
            $this->user_ip = ip2long($this->user_ip);
        }

        return parent::beforeSave($insert);
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
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::class, ['id' => 'answer_id']);
    }
}
