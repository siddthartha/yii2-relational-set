<?php
/**
 * Class siddthartha\tests\models\HostSlave
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace siddthartha\tests\models;

/**
 * Description of HostSlave
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class HostSlave extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'host_slave';
    }
}