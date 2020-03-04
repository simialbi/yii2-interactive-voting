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
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        if ($auth) {

        }
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($auth) {

        }
    }
}