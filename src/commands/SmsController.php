<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\commands;

use simialbi\yii2\voting\models\Voting;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\httpclient\Client;

/**
 * Provides SMS methods
 *
 * @property-read \simialbi\yii2\voting\Module $module
 */
class SmsController extends Controller
{
    /**
     * Send automatically all sms codes from all invitees to the invitees
     *
     * @return integer Exit code
     * @throws \yii\httpclient\Exception
     */
    public function actionSendSms()
    {
        $client = new Client([
            'baseUrl' => $this->module->smsBaseUrl,
            'requestConfig' => [
                'class' => 'yii\httpclient\Request',
                'format' => Client::FORMAT_RAW_URLENCODED
            ],
            'responseConfig' => [
                'class' => 'yii\httpclient\Response',
                'format' => Client::FORMAT_RAW_URLENCODED
            ]
        ]);
        $headers = [];
        $votings = Voting::find()->select('subject')->where([
            'is_active' => true,
            'is_finished' => false
        ])->indexBy('id')->column();

        $this->stdout("Send all invited users their code\n\n");
        if (empty($votings)) {
            $this->stderr('There are no active votings');
            $this->stderr(' ... quitting', Console::FG_YELLOW);
            $this->stderr("\n");

            return ExitCode::OK;
        }
        $voting = Voting::findOne($this->select('For which voting would you like to send SMS codes?', $votings));

        if ($this->module->smsAuthToken) {
            $headers['Authorization'] = "Bearer {$this->module->smsAuthToken}";
        }

        if (!$voting->getInvitees()->count('user_id')) {
            $this->stderr('There are no invitees');
            $this->stderr(' ... quitting', Console::FG_YELLOW);
            $this->stderr("\n");

            return ExitCode::OK;
        }

        $this->stdout("\n");
        foreach ($voting->invitees as $invitee) {
            $number = ArrayHelper::getValue($invitee->user, $this->module->mobileField);
            if (!$number) {
                $this->stderr('No number for user');
                $this->stderr($invitee->user->name, Console::FG_PURPLE);
                $this->stderr(' ... skipping', Console::FG_YELLOW);
                $this->stderr("\n");
                continue;
            }
            $data = [
                'clientMessageId' => "voting-{$voting->id}-code-{$invitee->user_id}",
                'contentCategory' => 'informational',
                'messageContent' => Yii::t('simialbi/voting', "Your Code for {voting}\n{code}", [
                    'voting' => $voting->subject,
                    'code' => $invitee->code
                ]),
                'messageType' => 'default',
                'recipientAddressList' => preg_replace('#[^0-9]#', '', $number)
            ];

            $this->stdout("Sending code `{$invitee->code}` to: ");
            $this->stdout($number, Console::FG_PURPLE);

            $request = $client->post('/rest/smsmessaging/simple', $data, $headers);
            $response = $request->send();
            $status = ArrayHelper::getValue($response->data, 'statusCode', 4002);

            if (preg_match('#^200#', $status)) {
                $this->stdout(' ... success', Console::FG_GREEN);
            } else {
                $message = ArrayHelper::getValue($response->data, 'statusMessage');
                $this->stderr(' ... failed', Console::FG_RED);
                if ($message) {
                    $this->stderr(": $message\n");
                }
            }
            $this->stdout("\n");
        }

        return ExitCode::OK;
    }
}