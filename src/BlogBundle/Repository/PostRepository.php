<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PostRepository extends BaseRepository
{
    public function getEntitiesCount($params=[])
    {
        $query = $this->createQueryBuilder('e')->select('COUNT(e)');
//            if (!empty($params['tag'])) {
//                $query->where('e.tags LIKE %'.$params['tag'].'%');
//            }
            return (int) $query->getQuery()->getSingleScalarResult();
    }

    public function getEntitesForPage($page, $limit, $asc=false, $params=[])
    {
        $offset = ($page - 1) * $limit;
        $query = $this->createQueryBuilder('e')->select('e');
//        if (!empty($params['tag'])) {
//            $query->where('e.tags LIKE %'.$params['tag'].'%');
//        }

           $query->setFirstResult($offset)
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