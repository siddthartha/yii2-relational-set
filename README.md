# yii2-relational-set
Represents Yii2 m2m junction relation as array field. Stores it's changes as difference without cleaning relation.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require siddthartha/yii2-relational-set
```

or add

```
"siddthartha/yii2-relational-set": "*"
```

to the `require` section of your `composer.json` file.

## Usage

### Host model
```php

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
```

### View code example
```php
    <?=$form->field( $model, '_slaves')->checkboxList(/*...*/)?>
```
Any changes to relation (junction table) will be executed as `insert` and/or `update` needed difference only! 