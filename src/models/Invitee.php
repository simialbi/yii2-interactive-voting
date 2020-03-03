<?php

namespace simialbi\yii2\voting\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%voting_invitee}}".
 *
 * @property integer $voting_id
 * @property string $user_id
 * @property string $code
 * @property string|integer $created_by
 * @property string|integer $updated_by
 * @property string|integer $created_at
 * @property string|integer $updated_at
 *
 * @property-read Voting $voting
 * @property-read \simialbi\yii2\models\UserInterface $user
 * @property-read \simialbi\yii2\models\UserInterface $creator
 * @property-read \simialbi\yii2\models\UserInterface $updater
 */
class Invitee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return '{{%voting_invitee}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['voting_id'], 'integer'],
            [['user_id'], 'string', 'max' => 64],
            [['voting_id', 'user_id'], 'unique', 'targetAttribute' => ['voting_id', 'user_id']],
            [
                ['voting_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Voting::class,
                'targetAttribute' => ['voting_id' => 'id']
            ],
            ['code', 'string', 'length' => 10],
            ['code', 'unique'],

            [['voting_id', 'user_id'], 'required']
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
            'voting_id' => Yii::t('simialbi/voting/model/voting-invitee', 'Voting'),
            'user_id' => Yii::t('simialbi/voting/model/voting-invitee', 'User'),
            'code' => Yii::t('simialbi/voting/model/voting-invitee', 'Code'),
            'created_by' => Yii::t('simialbi/voting/model/voting-invitee', 'Created by'),
            'updated_by' => Yii::t('simialbi/voting/model/voting-invitee', 'Updated by'),
            'created_at' => Yii::t('simialbi/voting/model/voting-invitee', 'Created at'),
            'updated_at' => Yii::t('simialbi/voting/model/voting-invitee', 'Updated at'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert)
    {
        if ($insert && !$this->code) {
            $this->code = Yii::$app->security->generateRandomString(10);
        }

        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Voting]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVoting()
    {
        return $this->hasOne(Voting::class, ['id' => 'voting_id']);
    }

    /**
     * Get user
     * @return \simialbi\yii2\models\UserInterface|null
     */
    public function getUser()
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->user_id);
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
