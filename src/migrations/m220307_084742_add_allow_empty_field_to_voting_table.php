<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\voting\migrations;

class m220307_084742_add_allow_empty_field_to_voting_table extends \yii\db\Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%voting_question}}',
            'empty_allowed',
            $this->boolean()->notNull()->defaultValue(0)->after('multiple_answers_allowed')->comment('Allow empty answers?')
        );
        $this->alterColumn(
            '{{%voting_question_answer}}',
            'answer_id',
            $this->integer()->unsigned()->null()->defaultValue(null)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%voting_question}}', 'empty_allowed');
        $this->alterColumn(
            '{{%voting_question_answer}}',
            'answer_id',
            $this->integer()->unsigned()->notNull()
        );
    }
}
