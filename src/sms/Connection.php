<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\sms;

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
class Connection extends \yii\base\Component
{
    /**
     * @var string The base url of the sms provider
     */
    public $baseUrl;

    /**
     * @var string The bearer token for identification against the provider
     */
    public $token;

    /**
     * @var Client
     */
    private $_client;

    /**
     * {@inheritDoc}
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
            ]
        ]);
        if ($this->token) {
            $this->_client->requestConfig['headers']['Authorization'] = $this->token;
        }

        parent::init();
    }
}