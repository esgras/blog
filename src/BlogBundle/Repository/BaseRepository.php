<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BaseRepository extends EntityRepository
{
    public function getEntitiesCount($params=[])
    {
        return (int)$this->createQueryBuilder('e')->select('COUNT(e)')
            ->getQuery()->getSingleScalarResult();
    }

    public function getEntitesForPage($page, $limit, $asc=false, $params=[])
    {
        $offset = ($page - 1) * $limit;
        $query = $this->createQueryBuilder('e')->select('e')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        if ($asc) {
            $query->addOrderBy('e.id', 'ASC');
        } else {
            $query->addOrderBy('e.id', 'DESC');
        }

        $paginator =  new Paginator($query, true);

        return $paginator;
    }
}