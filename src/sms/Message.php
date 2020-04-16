<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\sms;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\validators\UrlValidator;

/**
 * Message represents a sms message.
 */
class Message extends Component
{
    const CATEGORY_INFORMATIONAL = 'informational';
    const CATEGORY_ADVERTISEMENT = 'advertisement';

    const MESSAGE_TYPE_TEXT = 'default';
    const MESSAGE_TYPE_VOICE = 'voice';

    const ADDRESS_TYPE_NATIONAL = 'national';
    const ADDRESS_TYPE_INTERNATIONAL = 'international';
    const ADDRESS_TYPE_ALPHANUMERIC = 'alphanumeric';
    const ADDRESS_TYPE_SHORTCODE = 'shortcode';

    /**
     * @var Connection The connection this message is associated with
     */
    public $api;

    /**
     * @var string May contain a freely definable message id.
     * @see id()
     */
    public $id;

    /**
     * @var string The content category that is used to categorize the message (used for blacklisting).
     * The following content categories are supported: [[CATEGORY_INFORMATIONAL]] or [[CATEGORY_ADVERTISEMENT]]. If no
     * content category is provided, the default setting is used (may be changed inside the onlinesms web interface).
     * @see category()
     */
    public $category;

    /**
     * @var integer Specifies the maximum number of SMS to be generated. If the system generates more than this number
     * of SMS, the status code 4026 is returned. The default value is 0. If set to 0, no limitation is applied.
     */
    public $maxSms;

    /**
     * @var string Message content
     */
    public $content;

    /**
     * @var string Specifies the message type. Allowed values are [[MESSAGE_TYPE_TEXT]] and [[MESSAGE_TYPE_VOICE]]. When
     * using the message type [[MESSAGE_TYPE_TEXT]], the outgoing message type is determined based on account settings.
     * Using the message type [[MESSAGE_TYPE_VOICE]] triggers a voice call.
     */
    public $type;

    /**
     * @var string When setting a notificationCallbackUrl all delivery reports are forwarded to this URL.
     */
    public $callbackUrl;

    /**
     * @var integer Priority of the message. Must not exceed the value configured for the account used to send the
     * message. For more information please contact our customer service.
     */
    public $priority;

    /**
     * @var array List of recipients (E164 formatted MSISDNs – see Wikipedia en.wikipedia.org/wiki/MSISDN) to whom the
     * message should be sent. The list of recipients may contains a maximum of 1000 entries.
     */
    public $recipients;

    /**
     * @var boolean If the message is sent as flash SMS (displayed directly on the screen of the mobile phone).
     * If false: The message is sent as standard text SMS
     */
    public $asFlash;

    /**
     * @var string Address of the sender (assigned to the account) from which the message is sent.
     */
    public $sender;

    /**
     * @var string The sender address type. The following address types are supported: [[ADDRESS_TYPE_NATIONAL]]
     * [[ADDRESS_TYPE_INTERNATIONAL]], [[ADDRESS_TYPE_ALPHANUMERIC]] or [[ADDRESS_TYPE_SHORTCODE]]).
     */
    public $senderType;

    /**
     * @var boolean If the transmission is only simulated, no SMS is sent. Depending on the number of recipients the
     * status code 2000 or 2001 is returned. If false, no simulation is done. The SMS is sent via the SMS Gateway.
     */
    public $test;

    /**
     * @var integer Specifies the validity periode (in seconds) in which the message is tried to be delivered to
     * the recipient. A minimum of 1 minute and a maximum of 3 days are allowed.
     */
    public $validity;

    /**
     * @return array
     * @throws \yii\base\Exception
     */
    public function build()
    {
        $id = $this->id;
        if (empty($id)) {
            $id = 'sms-' . Yii::$app->security->generateRandomString(12);
        }

        $data = [
            'clientMessageId' => $id,
            'messageContent' => $this->content,
            'recipientAddressList' => $this->recipients
        ];

        if ($this->category) {
            ArrayHelper::setValue($data, 'contentCategory', $this->category);
        }
        if ($this->maxSms) {
            ArrayHelper::setValue($data, 'maxSmsPerMessage', $this->maxSms);
        }
        if ($this->type) {
            ArrayHelper::setValue($data, 'messageType', $this->type);
        }
        if ($this->callbackUrl) {
            ArrayHelper::setValue($data, 'notificationCallbackUrl', $this->callbackUrl);
        }
        if ($this->priority) {
            ArrayHelper::setValue($data, 'priority', $this->priority);
        }
        if ($this->asFlash) {
            ArrayHelper::setValue($data, 'sendAsFlashSms', $this->asFlash);
        }
        if ($this->sender) {
            ArrayHelper::setValue($data, 'senderAddress', $this->sender);
        }
        if ($this->senderType) {
            ArrayHelper::setValue($data, 'senderAddressType', $this->senderType);
        }
        if ($this->test) {
            ArrayHelper::setValue($data, 'test', $this->test);
        }
        if ($this->validity) {
            ArrayHelper::setValue($data, 'validityPeriode', $this->validity);
        }

        return $data;
    }

    /**
     * Sends the message and returns the api response
     * @return Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function send()
    {
        return $this->api->send($this);
    }

    /**
     * Set message id
     * @param string $id A freely definable message id.
     * @return $this the message object itself
     */
    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set category
     * @param string $category The content category that is used to categorize the message (used for blacklisting).
     * The following content categories are supported: [[CATEGORY_INFORMATIONAL]] or [[CATEGORY_ADVERTISEMENT]]. If no
     * content category is provided, the default setting is used (may be changed inside the onlinesms web interface).
     * @return $this the message object itself
     */
    public function category($category)
    {
        if ($category == self::CATEGORY_INFORMATIONAL || $category == self::CATEGORY_ADVERTISEMENT) {
            $this->category = $category;
        }

        return $this;
    }

    /**
     * Sets the maximum number of sms used to send the specified message
     * @param integer $maxSms Specifies the maximum number of SMS to be generated. If the system generates more than
     * this number of SMS, the status code 4026 is returned. The default value is 0. If set to 0, no limitation is
     * applied.
     * @return $this the message object itself
     */
    public function maxSms($maxSms)
    {
        $maxSms = (int)$maxSms;
        if ($maxSms >= 0) {
            $this->maxSms = $maxSms;
        }

        return $this;
    }

    /**
     * Sets the message content
     * @param string $content Message content
     * @return $this the message object itself
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Sets the message type
     * @param string $type Specifies the message type. Allowed values are [[MESSAGE_TYPE_TEXT]] and [[MESSAGE_TYPE_VOICE]].
     * When using the message type [[MESSAGE_TYPE_TEXT]], the outgoing message type is determined based on account settings.
     * Using the message type [[MESSAGE_TYPE_VOICE]] triggers a voice call.
     * @return $this the message object itself
     */
    public function type($type)
    {
        if ($type == self::MESSAGE_TYPE_TEXT || $type == self::MESSAGE_TYPE_VOICE) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Sets the callback url
     * @param string $callbackUrl When setting a notificationCallbackUrl all delivery reports are forwarded to this URL.
     * @return $this the message object itself
     */
    public function callbackUrl($callbackUrl)
    {
        $validator = new UrlValidator([
            'enableIDN' => function_exists('idn_to_ascii')
        ]);
        if ($validator->validate($callbackUrl)) {
            $this->callbackUrl = $callbackUrl;
        }

        return $this;
    }

    /**
     * Sets message priority
     * @param integer $priority Priority of the message. Must not exceed the value configured for the account used to
     * send the message. For more information please contact our customer service.
     * @return $this the message object itself
     */
    public function priority($priority)
    {
        $priority = (int)$priority;
        if ($priority > 9) {
            $priority = 9;
        }
        if ($priority < 1) {
            $priority = 1;
        }
        $this->priority = $priority;

        return $this;
    }

    /**
     * Sets message recipients
     * @param array|string $recipients List of recipients (E164 formatted MSISDNs – see Wikipedia en.wikipedia.org/wiki/MSISDN)
     * to whom the message should be sent. The list of recipients may contains a maximum of 1000 entries.
     * @return $this the message object itself
     */
    public function recipients($recipients)
    {
        $this->recipients = $this->normalizeRecipients($recipients);

        return $this;
    }

    /**
     * Add more recipients to the message
     * @param array|string $recipient The recipient(s) to add. See [[recipients()]] for more details about the format
     * of this parameter.
     * @return $this the message object itself
     * @see recipients()
     */
    public function addRecipient($recipient)
    {
        $this->recipients = array_merge($this->recipients, $this->normalizeRecipients($recipient));

        return $this;
    }

    /**
     * Send the message as flash or not
     * @param boolean $asFlash If the message is sent as flash SMS (displayed directly on the screen of the mobile phone).
     * If false: The message is sent as standard text SMS
     * @return $this the message object itself
     */
    public function asFlash($asFlash)
    {
        $this->asFlash = (bool)$asFlash;

        return $this;
    }

    /**
     * Set the message sender. The sender specified here has to be assigned to the account.
     * @param string $sender Address of the sender (assigned to the account) from which the message is sent.
     * @return $this the message object itself
     */
    public function sender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Set the message sender display type
     * @param string $senderType The sender address type. The following address types are supported:
     * [[ADDRESS_TYPE_NATIONAL]], [[ADDRESS_TYPE_INTERNATIONAL]], [[ADDRESS_TYPE_ALPHANUMERIC]] or [[ADDRESS_TYPE_SHORTCODE]]).
     * @return $this the message object itself
     */
    public function senderType($senderType)
    {
        if ($senderType == self::ADDRESS_TYPE_INTERNATIONAL || $senderType == self::ADDRESS_TYPE_NATIONAL ||
            $senderType == self::ADDRESS_TYPE_ALPHANUMERIC || $senderType == self::ADDRESS_TYPE_SHORTCODE) {
            $this->senderType = $senderType;
        }

        return $this;
    }

    /**
     * Should the message sending should only be tested or not.
     * @param boolean $test If the transmission is only simulated, no SMS is sent. Depending on the number of recipients
     * the status code 2000 or 2001 is returned. If false, no simulation is done. The SMS is sent via the SMS Gateway.
     * @return $this the message object itself
     */
    public function test($test)
    {
        $this->test = (bool)$test;

        return $this;
    }

    /**
     * Sets the time period how long the message should tried to be sent to recipients
     * @param integer $validity Specifies the validity periode (in seconds) in which the message is tried to be delivered
     * to the recipient. A minimum of 1 minute and a maximum of 3 days are allowed.
     * @return $this the message object itself
     */
    public function validity($validity)
    {
        $validity = (int)$validity;
        if ($validity >= 60 && $validity <= 259200) {
            $this->validity = $validity;
        }

        return $this;
    }

    /**
     * Normalize recipients passed to [[recipients()]] or [[addRecipients()]]
     * @param array|string $recipients
     * @return array
     */
    protected function normalizeRecipients($recipients)
    {
        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }
        foreach ($recipients as $k => $recipient) {
            if (!is_numeric($recipient)) {
                unset($recipients[$k]);
            }
            if (!preg_match('/^\d{1,15}$/', $recipient) || preg_match('/^0/', $recipient)) {
                unset($recipients[$k]);
            }
        }

        return $recipients;
    }
}