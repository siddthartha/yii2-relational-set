<?php
/**
 * RelationalSetBehavior
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace siddthartha\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Sets fields with corresponding many-to-many relations.
 * [ 'someField' => 'someM2MRelation', ... ]
 * Field rule must be [ 'someField' , 'each', 'rule' => [ 'integer' ] ]
 * or some another type of primary field ('rule' => ['string'] for example)
 *
 * Usage: $form->field( $model, 'someField')->checkboxList()
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 *
 * @property ActiveRecord $owner
 * @property string[] $attributes
 *
 */
class RelationalSetBehavior extends Behavior
{
    public  $attributes = [];

    /** @var \yii\caching\Cache */
    public $cache;

    //
    private $masterPrimaryFields;
    private $slaveModels;
    private $slavePrimaryFields;
    private $linkTables;
    private $linkPrimaryFields;
    private $linkSlaveFields;


    public function init()
    {
        parent::init();

        if(!$this->cache instanceof \yii\caching\Cache)
        {
            $this->cache = \Yii::createObject(['class' => \yii\caching\DummyCache::class]);
        }
    }

    /**
     *
     * @return string[]
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => '_validateRelationalSets',
            ActiveRecord::EVENT_AFTER_INSERT    => '_storeRelationalSets',
            ActiveRecord::EVENT_AFTER_UPDATE    => '_storeRelationalSets',
            ActiveRecord::EVENT_AFTER_FIND      => '_restoreRelationalSets',
            ActiveRecord::EVENT_AFTER_REFRESH   => '_restoreRelationalSets',
            ActiveRecord::EVENT_BEFORE_DELETE   => '_eraseRelationalSets',
        ];
    }

    /**
     *
     * @param ActiveRecord $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);

        foreach ($this->attributes as $attribute => $relation)
        {
            $this->linkTables[$relation]          = $this->owner->getRelation($relation)->via->from[0];
            $this->masterPrimaryFields[$relation] = $this->owner->primaryKey()[0];
            $this->linkPrimaryFields[$relation]   = array_flip($this->owner->getRelation($relation)->via->link)[$this->masterPrimaryFields[$relation]];
            $this->slaveModels[$relation]         = $this->owner->getRelation($relation)->modelClass;
            $this->slavePrimaryFields[$relation]  = (new $this->slaveModels[$relation])->primaryKey()[0];
            $this->linkSlaveFields[$relation]     = $this->owner->getRelation($relation)->link[$this->slavePrimaryFields[$relation]];
        }
    }

    /**
     *
     */
    public function detach()
    {
        parent::detach();

        unset($this->linkTables);
        unset($this->masterPrimaryFields);
        unset($this->linkPrimaryFields);
        unset($this->slaveModels);
        unset($this->slavePrimaryFields);
        unset($this->linkSlaveFields);
    }

    /**
     *
     * @param \yii\base\ModelEvent $event
     */
    public function _validateRelationalSets($event)
    {
        foreach ($this->attributes as $attribute => $relation)
        {
            if (is_array($this->owner->{$attribute}) || is_string($this->owner->{$attribute}))
            {
                //
                $this->owner->{$attribute} = is_string($this->owner->{$attribute}) && $this->owner->{$attribute} === ""
                    ? []
                    : array_unique($this->owner->{$attribute});
            }
        }
    }

    /**
     *
     * @param \yii\base\ModelEvent $event
     */
    public function _restoreRelationalSets($event)
    {
        foreach ($this->attributes as $attribute => $relation)
        {
            if ($this->cache->exists($this->rsHash($relation)))
            {
                $this->owner->{$attribute} = $this->cache->get($this->rsHash($relation));

                continue;
            }

            if (!empty($this->owner->relatedRecords[$relation]))
            {
                //
                foreach ($this->owner->relatedRecords[$relation] as $object)
                { // non indexed list!
                    $this->owner->{$attribute}[$object->{$this->slavePrimaryFields[$relation]}] = $object->{$this->slavePrimaryFields[$relation]};
                }
            }
            else
            {
                $this->owner->{$attribute} = $this->owner->getRelation($relation)
                    ->select([$this->slavePrimaryFields[$relation]])
                    ->indexBy($this->slavePrimaryFields[$relation])
                    ->column();
            }

            $this->cache->add($this->rsHash($relation), $this->owner->{$attribute});
        }
    }

    /**
     * сохраняет множества
     *
     * @param \yii\base\ModelEvent $event
     * @return boolean
     */
    public function _storeRelationalSets($event)
    {
        //TODO: validate relation and rules

        foreach ($this->attributes as $attribute => $relation)
        {
            $_deleted  = false;
            $_inserted = false;

            if (is_array($this->owner->{$attribute}) || is_string($this->owner->{$attribute}))
            {
                // about default checkboxList-generated params
                $_inputValuesArray = $this->owner->{$attribute} === "" ? [] : $this->owner->{$attribute};

                // fill arrays
                $_inputValuesArray  = \yii\helpers\ArrayHelper::index($_inputValuesArray, function ($item) { return $item; });
                $_actualValuesArray = $this->owner->getRelation($relation)->select([$this->slavePrimaryFields[$relation]])->asArray()->column();
                $_newValuesArray    = array_diff_assoc($_inputValuesArray, $_actualValuesArray);
                $_rmValuesArray     = array_diff_assoc($_actualValuesArray, $_inputValuesArray);

                // execute db changes if needed
                if (!empty($_rmValuesArray))
                {
                    $_rmWhereSql = [];

                    foreach ($_rmValuesArray as $rmValue)
                    {
                        $_rmWhereSql[] = "`{$this->linkPrimaryFields[$relation]}` = '{$this->owner->{$this->masterPrimaryFields[$relation]}}' AND `{$this->linkSlaveFields[$relation]}` = '{$rmValue}'";
                    }
                    $_rmWhereSql = '(' . implode(') OR (', $_rmWhereSql) . ')';
                    $_rmSql      = "DELETE FROM `{$this->linkTables[$relation]}` WHERE " . $_rmWhereSql;
                    $_deleted    = ($this->owner->getDb()->createCommand($_rmSql)->execute() > 0);
                }

                if (!empty($_newValuesArray))
                {
                    $_newValuesSql = [];

                    foreach ($_newValuesArray as $newValue)
                    {
                        $_newValuesSql[] = "('{$this->owner->{$this->masterPrimaryFields[$relation]}}', '{$newValue}')";
                    }
                    $_newValuesSql = implode(',', $_newValuesSql);
                    $_newSql       = "INSERT INTO `{$this->linkTables[$relation]}` (`{$this->linkPrimaryFields[$relation]}`, `{$this->linkSlaveFields[$relation]}`) VALUES " . $_newValuesSql;
                    $_inserted     = ($this->owner->getDb()->createCommand($_newSql)->execute() > 0);
                }

                if (($_deleted || $_inserted) && $this->cache->exists($this->rsHash($relation)))
                {
                    $this->cache->delete($this->rsHash($relation));
                }
            }
        }

        // восстанавливаем после изменений в полях массивы если это не очистка после удаления
        if ($event->name != 'beforeDelete')
        {
            $this->_restoreRelationalSets($event);
        }
    }

    /**
     * Before delete
     *
     * @param \yii\base\ModelEvent $event
     */
    public function _eraseRelationalSets($event)
    {
        foreach ($this->attributes as $attribute => $relation)
        {
            // будем очищать данную связь (это не сеттер)
            $this->owner->{$attribute} = [];
            //очищаем кэш
            $this->cache->delete($this->rsHash($relation));
        }

        // очищаем связи
        $this->_storeRelationalSets($event);

        $event->isValid = true;
    }

    /**
     * хэш ключ по имени связи модели и значению первичного ключа
     *
     * @param string $relation
     * @return string
     */
    public function rsHash($relation)
    {
        return implode('_', [crc32($this->owner->className()), $this->owner->{$this->masterPrimaryFields[$relation]}, $relation]);
    }
}