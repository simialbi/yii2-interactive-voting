<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\models;

use Yii;
use yii\base\Model;

/**
 * Class LoginForm
 * @package simialbi\yii2\voting\models
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $code;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['username', 'email'],
            ['code', 'string', 'length' => 10],

            [['username', 'code'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('simialbi/voting/model/login-form', 'Username'),
            'code' => Yii::t('simialbi/voting/model/login-form', 'Code')
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeHints(): array
    {
        return [
            'username' => Yii::t('simialbi/voting/model/login-form', 'You\'re E-Mail'),
            'code' => Yii::t('simialbi/voting/model/login-form', 'The Code you received by SMS')
        ];
    }
}
