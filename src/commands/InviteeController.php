<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\voting\commands;

use simialbi\yii2\voting\models\Invitee;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Handle Invitee actions in
 */
class InviteeController extends Controller
{
    /**
     * Create invitees out of all addresses for a specific voting.
     *
     * @param int $votingId The id of the voting to create invitations for
     *
     * @return int Exit code
     */
    public function actionCreate(int $votingId): int
    {
        $users = ArrayHelper::map(call_user_func([Yii::$app->user->identityClass, 'findIdentities']), 'id', 'name');

        foreach ($users as $id => $user) {
            $invitee = new Invitee();
            $invitee->voting_id = $votingId;
            $invitee->user_id = (string)$id;

            $invitee->save();
        }

        $this->stdout(count($users), Console::FG_YELLOW, Console::BOLD);
        $this->stdout(" invitation created\n");

        return ExitCode::OK;
    }
}
