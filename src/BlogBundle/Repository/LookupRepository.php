<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LookupRepository extends EntityRepository
{
    private static $items = [];

    public static function items($type)
    {
        if (empty(self::$items[$type])) {
            self::loadItems($type);
        }
        return self::$items[$type];
    }

    public static function item($type, $code)
    {
        if (!isset(self::$items[$type])) {
            self::loadItems($type);
        }

        return isset(self::$items[$type][$code]) ? self::$items[$type][$code] : false;
    }

    public static function loadItems($type)
    {
//        $query = $this->createQueryBuilder('i')
//                        ->where(['type' => ':type'])->setParameter(':type', $type)
//                        ->addOrderBy('i.code', 'ASC');
    }
}