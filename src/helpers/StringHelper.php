<?php
/**
 * @package yii2-interactive-voting
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\voting\helpers;

/**
 * StringHelper
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * Generates a random string of specified length optionally with the characters in second parameter.
     *
     * @param integer $length the length of the key in characters
     * @param string|null $characters characters to be used to generate random string
     */
    public static function generateRandomString($length = 10, $characters = null)
    {
        if (null === $characters) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}