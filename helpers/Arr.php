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
class Arr extends \yii\helpers\BaseArrayHelper
{
    /**
     * Get difference between two sets (arrays) as [added, removed] subsets
     *
     * @param mixed[] $actualStateArray
     * @param mixed[] $inputStateArray
     * @return [mixed[], mixed[]]
     */
    public static function completion($actualStateArray, $inputStateArray)
    {
        $addedValuesArray   = array_diff_assoc($inputStateArray, $actualStateArray);
        $removedValuesArray = array_diff_assoc($actualStateArray, $inputStateArray);

        return [$addedValuesArray, $removedValuesArray];
    }

    /**
     *
     * @param mixed $value
     * @param mixed[] $templateArray
     * @return mixed
     */
    public static function wrapBy($value, &$templateArray)
    {
        return self::getValue($templateArray, $value, $value);
    }

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
            ? self::log(self::map($relatedModel::findAll(['id' => $array]), $arrayMap[0], $arrayMap[1]))
            : self::log($array);
    }
}