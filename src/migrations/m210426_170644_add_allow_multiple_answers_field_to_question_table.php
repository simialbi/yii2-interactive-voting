<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\voting\migrations;

use yii\db\Migration;

class m210426_170644_add_allow_multiple_answers_field_to_question_table extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%voting_question}}',
            'multiple_answers_allowed',
            $this->boolean()->notNull()->defaultValue(0)->after('is_finished')->comment('Allow multiple answers?')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%voting_question}}', 'multiple_answers_allowed');
    }
}
