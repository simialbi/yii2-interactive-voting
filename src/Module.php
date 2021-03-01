<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2\voting;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class Module extends \simialbi\yii2\base\Module implements BootstrapInterface
{
    /**
     * {@inheritDoc}
     */
    public $controllerNamespace = 'simialbi\yii2\voting\controllers';

    /**
     * @var string The field of the user identity class containing the mobile number
     */
    public $mobileField = 'mobile';

    /**
     * @var string The field of the user identity class containing the username
     */
    public $usernameField = 'username';

    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->registerTranslations();

        $identity = new Yii::$app->user->identityClass;
        if (!($identity instanceof UserInterface)) {
            throw new InvalidConfigException('The "identityClass" must extend "simialbi\yii2\models\UserInterface"');
        }
        if (!Yii::$app->hasModule('gridview')) {
            $this->setModule('gridview', [
                'class' => 'kartik\grid\Module',
                'bsVersion' => '4',
                'exportEncryptSalt' => 'ror_HTbRh0Ad7K7DqhAtZOp50GKyia4c',
                'i18n' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@kvgrid/messages',
                    'forceTranslation' => true
                ]
            ]);
        }

        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'simialbi\yii2\voting\commands';
        }
    }
}
