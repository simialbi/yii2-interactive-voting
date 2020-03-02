# Interactive voting module for yii2

(badges)

## Resources

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require --prefer-dist simialbi/yii2-interactive-voting
```

or add

```
"simialbi/yii2-interactive-voting": "^1.0.0"
```

to the `require` section of your `composer.json`.

## Usage

In order to use this module, you will need to:

1. [Setup Module](#setup-module) your application so that the module is available.
2. [Create a user identity](#create-identity) class which extends UserInterface

### Setup Module

Configure the module in the modules section of your Yii configuration file.


### Create identity

Create an identity class which implements `simialbi\yii2\models\UserInterface` e.g.:
```php
<?php
use yii\db\ActiveRecord;
use simialbi\yii2\models\UserInterface;

class User extends ActiveRecord implements UserInterface
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritDoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritDoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritDoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * {@inheritDoc}
     */
    public static function findIdentities() {
        return static::find()->all();
    }
}
```

After creating this class define it as identity class in your application configuration:
```php
'components' => [
    'user' => [
        'identityClass' => 'app\models\User'
    ]
]
``` 

## Example Usage

Now you can access the interactive voting module by navigating to `/voting`.

## License

**yii2-interactive-voting** is released under MIT license. See bundled [LICENSE](LICENSE) for details.
