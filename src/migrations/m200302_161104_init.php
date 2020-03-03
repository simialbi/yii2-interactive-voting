<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\migrations;

use yii\db\Migration;

/**
 * Class m200302_161104_init
 * @package simialbi\yii2\voting\migrations
 */
class m200302_161104_init extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%voting}}', [
            'id' => $this->primaryKey()->unsigned(),
            'subject' => $this->string(255)->notNull(),
            'description' => $this->text()->null()->defaultValue(null)->comment('Details to the voting'),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%voting_question}}', [
            'id' => $this->primaryKey()->unsigned(),
            'voting_id' => $this->integer()->unsigned()->notNull(),
            'subject' => $this->string(255)->notNull(),
            'description' => $this->text()->null()->defaultValue(null)->comment('Details to the question'),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%voting_invitee}}', [
            'voting_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'PRIMARY KEY ([[voting_id]], [[user_id]])'
        ]);
        $this->createTable('{{%voting_answer}}', [
            'id' => $this->primaryKey()->unsigned(),
            'question_id' => $this->integer()->unsigned()->notNull(),
            'text' => $this->string(255)->notNull(),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%voting_question_answer}}', [
            'id' => $this->primaryKey()->unsigned(),
            'question_id' => $this->integer()->unsigned()->notNull(),
            'answer_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->string(64)->null()->defaultValue(null),
            'user_ip' => $this->integer()->unsigned()->notNull(),
            'session_id' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull()
        ]);

        $this->addForeignKey(
            '{{%voting_question_ibfk_1}}',
            '{{%voting_question}}',
            'voting_id',
            '{{%voting}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%voting_invitee_ibfk_1}}',
            '{{%voting_invitee}}',
            'voting_id',
            '{{%voting}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%voting_answer_ibfk_1}}',
            '{{%voting_answer}}',
            'question_id',
            '{{%voting_question}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%voting_question_answer_ibfk_1}}',
            '{{%voting_question_answer}}',
            'question_id',
            '{{%voting_question}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%voting_question_answer_ibfk_2}}',
            '{{%voting_question_answer}}',
            'answer_id',
            '{{%voting_answer}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%voting_question_answer_ibfk_2}}', '{{%voting_question_answer}}');
        $this->dropForeignKey('{{%voting_question_answer_ibfk_1}}', '{{%voting_question_answer}}');
        $this->dropForeignKey('{{%voting_answer_ibfk_1}}', '{{%voting_answer}}');
        $this->dropForeignKey('{{%voting_invitee_ibfk_1}}', '{{%voting_invitee}}');
        $this->dropForeignKey('{{%voting_question_ibfk_1}}', '{{%voting_question}}');

        $this->dropTable('{{%voting_question_answer}}');
        $this->dropTable('{{%voting_answer}}');
        $this->dropTable('{{%voting_invitee}}');
        $this->dropTable('{{%voting_question}}');
        $this->dropTable('{{%voting}}');
    }
}