<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostRepository extends EntityRepository
{
    public function getPostsForPage($page, $limit, $asc=false)
    {
        $offset = ($page - 1) * $limit;
        $query = $this->createQueryBuilder('p')->select('p')
                            ->setFirstResult($offset)
                            ->setMaxResults($limit);
        if ($asc) {
            $query->addOrderBy('p.id', 'ASC');
        } else {
            $query->addOrderBy('p.id', 'DESC');
        }

        $paginator =  new Paginator($query, true);

        return $paginator;
    }

    public function getPostsCount()
    {
        return (int) $this->createQueryBuilder('p')->select('COUNT(p)')
                        ->getQuery()->getSingleScalarResult();
    }
}