<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\sms;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * Connection represents a connection to an sms api
 *
 * The following example shows how to create a Connection instance and establish
 * the API connection:
 * ```php
 * $connection = new \simialbi\yii2\voting\sms\Connection([
 *     'baseUrl' => 'https://api.sms.com',
 *     'token' => 'asdf1234asdf1234asdf1234asdf1234'
 * ]);
 * ```
 *
 * Connection is often used as an application component and configured in the application
 * configuration like the following:
 *
 * ```php
 * 'components' => [
 *     'sms' => [
 *         'class' => '\simialbi\yii2\voting\sms\Connection',
 *         'baseUrl' => 'https://api.sms.com',
 *         'token' => 'asdf1234asdf1234asdf1234asdf1234'
 *     ],
 * ],
 * ```
 */
class Connection extends Component
{
    /**
     * @var string The base url of the sms provider
     */
    public $baseUrl;

    /**
     * @var string The url path to send messages
     */
    public $sendUrl;

    /**
     * @var string The bearer token for identification against the provider
     */
    public $token;

    /**
     * @var string the class used to create new api [[Message]] objects
     * @see createMessage
     */
    public $messageClass = 'simialbi\yii2\voting\sms\Message';

    /**
     * @var Client
     */
    private $_client;

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (empty($this->baseUrl)) {
            throw new InvalidConfigException('Connection::baseUrl cannot be empty.');
        }

        $this->_client = new Client([
            'baseUrl' => $this->baseUrl,
            'requestConfig' => [
                'class' => 'yii\httpclient\Request',
                'format' => Client::FORMAT_RAW_URLENCODED
            ],
            'responseConfig' => [
                'class' => 'yii\httpclient\Response',
                'format' => Client::FORMAT_RAW_URLENCODED
            ],
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        if ($this->token) {
            $this->_client->requestConfig['headers']['Authorization'] = "Bearer {$this->token}";
        }

        parent::init();
    }

    /**
     * Creates a message for sending
     * @return Message
     * @throws InvalidConfigException
     */
    public function createMessage()
    {
        $config = ['class' => 'simialbi\yii2\voting\sms\Message'];
        if ($this->messageClass !== $config['class']) {
            $config['class'] = $this->messageClass;
        }
        $config['api'] = $this;
        /** @var Message $message */
        $message = Yii::createObject($config);
        return $message;
    }

    /**
     * @param Message $message
     * @param string $method
     * @param array $headers
     * @return Response
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     * @throws \yii\base\Exception
     */
    public function send(Message $message, $method = 'post', $headers = [])
    {
        $request = $this->_client
            ->createRequest()
            ->setMethod($method)
            ->setUrl($this->sendUrl)
            ->setData($message->build())
            ->addHeaders($headers);

        $response = $request->send();

        $result = new Response();
        $result->setAttributes($response->data);
        $result->validate();

        return $result;
    }
}