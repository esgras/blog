<?php

namespace BlogBundle\Service;

use BlogBundle\Entity\Lookup;
use Doctrine\ORM\EntityManager;

class LookupService
{
    private $items = [];
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function items($type)
    {
        if (empty($this->items[$type])) {
            $this->loadItems($type);
        }
        return $this->items[$type];
    }

    public function item($type, $code)
    {
        if (!isset($this->items[$type])) {
            $this->loadItems($type);
        }

        return isset($this->items[$type][$code]) ? $this->items[$type][$code] : false;
    }

    public function loadItems($type)
    {
        $lookups = $this->em->createQueryBuilder()
                        ->select('l')
                        ->from(Lookup::class, 'l')
                        ->where('l.type = :type')
                        ->addOrderBy('l.code', 'ASC')
                        ->setParameter('type', $type)
                        ->getQuery()
                        ->getResult();

        foreach ($lookups as $lookup) {
            // Специално под ChoiceType
            $this->items[$lookup->getType()][$lookup->getName()] = $lookup->getCode();
        }
    }
}