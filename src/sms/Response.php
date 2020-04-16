<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\sms;

use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Response represents SMS api response.
 *
 * @property-read boolean $isOk  Whether response is OK. This property is read-only.
 */
class Response extends Model
{
    const STATUS_OK = 2000;
    const STATUS_OK_QUEUED = 2001;
    const STATUS_INVALID_CREDENTIALS = 4001;
    const STATUS_INVALID_RECIPIENT = 4002;
    const STATUS_INVALID_SENDER = 4003;
    const STATUS_INVALID_MESSAGE_TYPE = 4004;
    const STATUS_INVALID_MESSAGE_ID = 4008;
    const STATUS_INVALID_TEXT = 4009;
    const STATUS_MSG_LIMIT_EXCEEDED = 4013;
    const STATUS_UNAUTHORIZED_IP = 4014;
    const STATUS_INVALID_MESSAGE_PRIORITY = 4015;
    const STATUS_INVALID_COD_RETURNADDRES = 4016;
    const STATUS_PARAMETER_MISSING = 4019;
    const STATUS_INVALID_ACCOUNT = 4021;
    const STATUS_ACCESS_DENIED = 4022;
    const STATUS_THROTTLING_SPAMMING_IP = 4023;
    const STATUS_THROTTLING_TOO_MANY_RECIPIENTS = 4025;
    const STATUS_MAX_SMS_PER_MESSAGE_EXCEEDED = 4026;
    const STATUS_RECIPIENTS_BLACKLISTED = 4031;
    const STATUS_SMS_DISABLED = 4035;
    const STATUS_INVALID_CONTENT_CATEGORY = 4040;
    const STATUS_INVALID_VALIDITY_PERIODE = 4041;
    const STATUS_INTERNAL_ERROR = 5000;
    const STATUS_SERVICE_UNAVAILABLE = 5003;

    /**
     * @var integer Status code
     */
    public $statusCode;
    /**
     * @var string Description of the response status code.
     */
    public $statusMessage;
    /**
     * @var string Contains the message id defined in the request.
     */
    public $clientMessageId;
    /**
     * @var string Unique identifier that is set after successful processing of the request.
     */
    public $transferId;
    /**
     * @var integer The actual number of generated SMS.
     */
    public $smsCount;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['smsCount'], 'integer'],
            [['statusMessage', 'clientMessageId', 'transferId'], 'string'],
            [
                ['statusCode'],
                'in',
                'range' => [
                    self::STATUS_OK,
                    self::STATUS_OK_QUEUED,
                    self::STATUS_INVALID_CREDENTIALS,
                    self::STATUS_INVALID_RECIPIENT,
                    self::STATUS_INVALID_SENDER,
                    self::STATUS_INVALID_MESSAGE_TYPE,
                    self::STATUS_INVALID_MESSAGE_ID,
                    self::STATUS_INVALID_TEXT,
                    self::STATUS_MSG_LIMIT_EXCEEDED,
                    self::STATUS_UNAUTHORIZED_IP,
                    self::STATUS_INVALID_MESSAGE_PRIORITY,
                    self::STATUS_INVALID_COD_RETURNADDRES,
                    self::STATUS_PARAMETER_MISSING,
                    self::STATUS_INVALID_ACCOUNT,
                    self::STATUS_ACCESS_DENIED,
                    self::STATUS_THROTTLING_SPAMMING_IP,
                    self::STATUS_THROTTLING_TOO_MANY_RECIPIENTS,
                    self::STATUS_MAX_SMS_PER_MESSAGE_EXCEEDED,
                    self::STATUS_RECIPIENTS_BLACKLISTED,
                    self::STATUS_SMS_DISABLED,
                    self::STATUS_INVALID_CONTENT_CATEGORY,
                    self::STATUS_INVALID_VALIDITY_PERIODE,
                    self::STATUS_INTERNAL_ERROR,
                    self::STATUS_SERVICE_UNAVAILABLE
                ]
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'statusCode' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'statusMessage' => AttributeTypecastBehavior::TYPE_STRING,
                    'clientMessageId' => AttributeTypecastBehavior::TYPE_STRING,
                    'transferId' => AttributeTypecastBehavior::TYPE_STRING,
                    'smsCount' => AttributeTypecastBehavior::TYPE_INTEGER
                ],
                'typecastAfterFind' => false,
                'typecastAfterSave' => false,
                'typecastBeforeSave' => false,
                'typecastAfterValidate' => true
            ]
        ];
    }

    /**
     * Checks if response status code is OK (status code = 20x)
     * @return boolean whether response is OK.
     */
    public function getIsOk()
    {
        return $this->validate() && ($this->statusCode === self::STATUS_OK || $this->statusCode === self::STATUS_OK_QUEUED);
    }
}