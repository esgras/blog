<?php

namespace BlogBundle\Repository;

use BlogBundle\Entity\Comment;

class CommentRepository extends BaseRepository
{
    public function getLatestComments($limit=3, $approved=true)
    {
        $query = $this->createQueryBuilder('c')->select('c');
        if ($approved) {
            $query->where('c.status=' . Comment::STATUS_APPROVED);
        }
        return $query->addOrderBy('c.id', 'DESC')->setFirstResult(0)
                   ->setMaxResults($limit)->getQuery()->getResult();
    }
}