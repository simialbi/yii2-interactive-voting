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
     * @param string $smsComponent
     *
     * @return integer Exit code
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSendSms($smsComponent = 'sms')
    {
        /** @var \simialbi\yii2\voting\sms\Connection $sms */
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
                $this->stderr('No number for user');
                $this->stderr($invitee->user->name, Console::FG_PURPLE);
                $this->stderr(' ... skipping', Console::FG_YELLOW);
                $this->stderr("\n");
                continue;
            }
            $message = $sms->createMessage();
            $message
                ->id("voting-{$voting->id}-code-{$invitee->user_id}")
                ->category($message::CATEGORY_INFORMATIONAL)
                ->content(Yii::t('simialbi/voting', "Your Code for {voting}\n{code}", [
                    'voting' => $voting->subject,
                    'code' => $invitee->code
                ]))
                ->type($message::MESSAGE_TYPE_TEXT)
                ->addRecipient(preg_replace('#[^0-9]#', '', $number));

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