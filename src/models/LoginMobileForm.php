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
 * Class LoginMobileForm
 * @package simialbi\yii2\voting\models
 */
class LoginMobileForm extends Model
{
    const SCENARIO_STEP_1 = 'step1';
    const SCENARIO_STEP_2 = 'step2';
    const SCENARIO_STEP_3 = 'step3';

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $mobile;

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
            ['username', 'string', 'length' => [4, 10]],
            ['mobile', 'string'],
            ['code', 'string', 'length' => [8, 10]],
            ['scenario', 'safe'],

            ['username', 'filter', 'filter' => function ($value) {
                return rtrim($value, '_');
            }],

            [['username'], 'required', 'on' => [self::SCENARIO_STEP_1]],
            [['mobile'], 'required', 'on' => [self::SCENARIO_STEP_2]],
            [['code'], 'required', 'on' => [self::SCENARIO_STEP_3]]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('simialbi/voting/model/login-form', 'Member number'),
            'mobile' => Yii::t('simialbi/voting/model/login-form', 'Mobile'),
            'code' => Yii::t('simialbi/voting/model/login-form', 'Code')
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeHints(): array
    {
        return [
            'username' => Yii::t('simialbi/voting/model/login-form', 'Your member number'),
            'mobile' => Yii::t('simialbi/voting/model/login-form', 'Your mobile phone number'),
            'code' => Yii::t('simialbi/voting/model/login-form', 'The Code you received by SMS')
        ];
    }
}
