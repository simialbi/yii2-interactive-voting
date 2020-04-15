<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\migrations;

use Yii;
use yii\db\Migration;

class m200304_075259_init_rbac extends Migration
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $administrateVoting = $auth->createPermission('administrateVoting');
        $administrateVoting->description = 'Create / update and delete new votings and questions';
        $auth->add($administrateVoting);

        $administrateVotingInvitations = $auth->createPermission('administrateVotingInvitations');
        $administrateVotingInvitations->description = 'Crate / update and delete voting invitations';
        $auth->add($administrateVotingInvitations);

        $manageVoting = $auth->createPermission('manageVoting');
        $manageVoting->description = 'Guide through the voting. Set questions live and stop them. Close voting.';
        $auth->add($manageVoting);

        $votingAdministrator = $auth->createRole('votingAdministrator');
        $votingAdministrator->description = 'Can create votings, invitations and guide through voting.';
        $auth->add($votingAdministrator);

        $votingManager = $auth->createRole('votingManager');
        $votingManager->description = 'Can create invitations ans guide through voting.';
        $auth->add($votingManager);

        $auth->addChild($votingManager, $manageVoting);
        $auth->addChild($votingManager, $administrateVotingInvitations);
        $auth->addChild($votingAdministrator, $administrateVoting);
        $auth->addChild($votingAdministrator, $votingManager);
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $administrateVoting = $auth->getPermission('administrateVoting');
        $administrateVotingInvitations = $auth->getPermission('administrateVotingInvitations');
        $manageVoting = $auth->getPermission('manageVoting');
        $votingAdministrator = $auth->getRole('votingAdministrator');
        $votingManager = $auth->getRole('votingManager');

        $auth->removeChild($votingAdministrator, $votingManager);
        $auth->removeChild($votingAdministrator, $administrateVoting);
        $auth->removeChild($votingManager, $administrateVotingInvitations);
        $auth->removeChild($votingManager, $manageVoting);

        $auth->remove($administrateVoting);
        $auth->remove($administrateVotingInvitations);
        $auth->remove($manageVoting);
        $auth->remove($votingAdministrator);
        $auth->remove($votingManager);
    }
}