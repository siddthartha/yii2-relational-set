<?php
/**
 * Class siddthartha\tests\models\Host
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace siddthartha\tests\models;

/**
 * Description of Host
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Host extends \yii\db\ActiveRecord
{
    public $_slaves;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'sets' => [
                'class' => \siddthartha\behaviors\RelationalSetBehavior::class,
                'attributes' => [
                    '_slaves' => 'slaves',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getSlaves()
    {
        return $this->hasMany(Slave::class, ['id' => 'id_slave'])
            ->viaTable(HostSlave::tableName(), ['id_host' => 'id'])
            ->indexBy('id');
    }
}