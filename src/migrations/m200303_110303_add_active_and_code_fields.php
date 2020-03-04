<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting\migrations;

use yii\db\Migration;

class m200303_110303_add_active_and_code_fields extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%voting}}',
            'is_active',
            $this->boolean()->notNull()->defaultValue(0)->after('description')->comment('Current activated voting')
        );
        $this->addColumn(
            '{{%voting}}',
            'is_finished',
            $this->boolean()->notNull()->defaultValue(0)->after('is_active')->comment('Is the voting finished?')
        );
        $this->addColumn(
            '{{%voting_question}}',
            'is_active',
            $this->boolean()->notNull()->defaultValue(0)->after('description')->comment('Current question to be answered ')
        );
        $this->addColumn(
            '{{%voting_question}}',
            'is_finished',
            $this->boolean()->notNull()->defaultValue(0)->after('is_active')->comment('Is the question finished?')
        );
        $this->addColumn(
            '{{%voting_question}}',
            'started_at',
            $this->integer()->unsigned()->null()->defaultValue(null)->after('updated_at')
                ->comment('When did the answering of this question start?')
        );
        $this->addColumn(
            '{{%voting_question}}',
            'ended_at',
            $this->integer()->unsigned()->null()->defaultValue(null)->after('started_at')
                ->comment('When did the answering of this question end?')
        );
        $this->addColumn(
            '{{%voting_invitee}}',
            'code',
            $this->char(10)->notNull()->after('user_id')
        );
        $this->createIndex(
            '{{%voting_invitee_idx_unique_code}}',
            '{{%voting_invitee}}',
            'code',
            true
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        if ($this->isMSSQL()) {
            $tableName = $this->db->quoteTableName('{{%voting_question}}');
            $sql = <<<SQL
DECLARE @sql nvarchar(max)
SET @sql = ''
SELECT @sql = 'ALTER TABLE $tableName DROP CONSTRAINT ' + [name]  + ';'
FROM [sys].[default_constraints]
WHERE [parent_object_id] = OBJECT_ID('$tableName')
AND [parent_column_id] = COLUMNPROPERTY(OBJECT_ID('$tableName'), 'is_active', 'ColumnId')
AND [type] = 'D'
EXECUTE sp_executesql @sql
SQL;
            $this->execute($sql);

            $tableName = $this->db->quoteTableName('{{%voting}}');
            $sql = <<<SQL
DECLARE @sql nvarchar(max)
SET @sql = ''
SELECT @sql = 'ALTER TABLE $tableName DROP CONSTRAINT ' + [name]  + ';'
FROM [sys].[default_constraints]
WHERE [parent_object_id] = OBJECT_ID('$tableName')
AND [parent_column_id] = COLUMNPROPERTY(OBJECT_ID('$tableName'), 'is_active', 'ColumnId')
AND [type] = 'D'
EXECUTE sp_executesql @sql
SQL;
            $this->execute($sql);
        }

        $this->dropIndex('{{%voting_invitee_idx_unique_code}}', '{{%voting_invitee}}');
        $this->dropColumn('{{%voting_question}}', 'ended_at');
        $this->dropColumn('{{%voting_question}}', 'started_at');
        $this->dropColumn('{{%voting_question}}', 'is_finished');
        $this->dropColumn('{{%voting_question}}', 'is_active');
        $this->dropColumn('{{%voting}}', 'is_finished');
        $this->dropColumn('{{%voting}}', 'is_active');
    }

    /**
     * Checks if driver is mssql
     * @return boolean
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }
}