<?php
/**
 * Class common\helpers\Naming
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace siddthartha\helpers;

/**
 * Description of Naming
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Naming
{

    /**
     *
     * @param string|\yii\base\Object $entity
     * @return boolean
     */
    public static function shortClass($entity)
    {
        if(is_string($entity))
        {
            $parts = explode('\\', $entity);
            $name = array_pop($parts);
            return $name;
        }

        if(is_object($entity))
        {
            return self::shortClass(get_class($entity));
        }

        return false;
    }

    /**
     *
     * @param string $name
     * @return string
     */
    public static function dashed2camel($name)
    {
        return str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));;
    }

    /**
     *
     * @param string $name
     * @return string
     */
    public static function camel2dashed($name)
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $name));
    }
}
