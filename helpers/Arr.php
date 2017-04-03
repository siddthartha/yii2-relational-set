<?php
/**
 * Class siddthartha\helpers\Array
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace siddthartha\helpers;

/**
 * Description of Array
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Arr
{

    /**
     * Returns human readable array text-plain view
     *
     * @param mixed[] $array
     * @return string
     */
    public static function log(array $array)
    {
        $result = [];

        array_walk($array, function(&$e, $i) use (&$result) {
            $result[] = "#$i: $e";
        });

        return count($array) . ":[ " . implode(', ', $result) . " ]\n";
    }

    /**
     *
     * @param mixed[] $array
     * @param string $relatedModel
     * @return string
     */
    public static function rsLog(array $array, $relatedModel = null, array $arrayMap = ['id', 'name'])
    {
        return is_string($relatedModel) && class_exists($relatedModel)
            ? self::log(\yii\helpers\ArrayHelper::map($relatedModel::findAll(['id' => $array]), $arrayMap[0], $arrayMap[1]))
            : self::log($array);
    }
}