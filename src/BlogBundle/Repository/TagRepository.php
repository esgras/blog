<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    public function getTagsByNameArray($tags)
    {
        $tags = "('" . join("', '", $tags) . "')";
        $query = $this->createQueryBuilder('t')->select('t')
            ->where('t.name IN '. $tags )->orderBy('t.id')->getQuery();

        return $query->getResult();
    }
}