<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\voting\migrations;

use yii\db\Migration;

class m200420_085124_add_show_results_field_to_voting_table extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%voting}}',
            'show_results',
            $this->boolean()->null()->defaultValue(null)->after('is_with_mobile_registration')
        );
        $this->addColumn(
            '{{%voting}}',
            'finished_message',
            $this->text()->null()->defaultValue(null)->after('show_results')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%voting}}', 'show_results');
        $this->dropColumn('{{%voting}}', 'finished_message');
    }
}