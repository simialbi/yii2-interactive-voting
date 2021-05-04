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
use yii\helpers\Url;

/**
 * Provides SMS methods
 *
 * @property-read \simialbi\yii2\voting\Module $module
 */
class SmsController extends Controller
{
    const MODE_CODE = 'code';
    const MODE_AUTOLOGIN = 'autologin';

    /**
     * Send automatically all sms codes from all invitees to the invitees
     *
     * @param string $smsComponent
     * @param string $mode The login mode. Either 'code' to send all users a code or 'autologin' to generate a link.
     * If 'autologin' is used, be sure to have a url manager configured in the console config and the
     * parameter "hostInfo" set.
     *
     * @return integer Exit code
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSendSms($smsComponent = 'sms', $mode = self::MODE_CODE)
    {
        /** @var \simialbi\yii2\websms\Connection $sms */
        $sms = $this->module->get($smsComponent, true);

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
                $this->stderr('No number for user ');
                $this->stderr($invitee->user->name, Console::FG_PURPLE);
                $this->stderr(' ... skipping', Console::FG_YELLOW);
                $this->stderr("\n");
                continue;
            }
            $message = $sms->createMessage();
            $message
                ->id("voting-{$voting->id}-code-{$invitee->user_id}")
                ->category($message::CATEGORY_INFORMATIONAL)
                ->type($message::MESSAGE_TYPE_TEXT)
                ->addRecipient(preg_replace('#[^0-9]#', '', $number));
            if ($mode === self::MODE_AUTOLOGIN) {
                $message->content(Yii::t(
                    'simialbi/voting',
                    "You were invited to the voting {voting}\nClick the following link to vote\n{link}",
                    [
                        'voting' => $voting->subject,
                        'link' => Url::to([$this->module->id . '/default/login-token', 'token' => $invitee->user->getAuthKey()])
                    ]
                ));
            } else {
                $message->content(Yii::t('simialbi/voting', "Your Code for {voting}\n{code}", [
                    'voting' => $voting->subject,
                    'code' => $invitee->code
                ]));
            }

            $this->stdout("Sending code `{$invitee->code}` to: ");
            $this->stdout($number, Console::FG_PURPLE);

            $response = $message->send();

            if ($response->isOk) {
                $this->stdout(' ... success', Console::FG_GREEN);
            } else {
                $this->stderr(' ... failed', Console::FG_RED);
                if (!empty($response->statusMessage)) {
                    $this->stderr(": {$response->statusMessage}\n");
                }
            }
            $this->stdout("\n");
        }

        return ExitCode::OK;
    }
}