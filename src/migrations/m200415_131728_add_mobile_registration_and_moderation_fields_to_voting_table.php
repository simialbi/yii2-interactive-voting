<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\migrations;

use yii\db\Migration;

class m200415_131728_add_mobile_registration_and_moderation_fields_to_voting_table extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%voting}}',
            'is_moderated',
            $this->boolean()->notNull()->defaultValue(1)->after('is_finished')
                ->comment('Is this voting moderated?')
        );
        $this->addColumn(
            '{{%voting}}',
            'is_with_mobile_registration',
            $this->boolean()->notNull()->defaultValue(0)->after('is_moderated')
                ->comment('Does the user can enter his mobile number in login form and get\'s the code via SMS')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%voting}}', 'is_moderated');
        $this->dropColumn('{{%voting}}', 'is_with_mobile_registration');
    }
}