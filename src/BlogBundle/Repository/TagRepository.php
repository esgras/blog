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

    public function getTagsWithWeight($tags=NULL)
    {
        $tags = $this->createQueryBuilder('t')->select('t')
                          ->getQuery()->getResult();
        $total = 0;
        foreach ($tags as $tag) {
            $total += $tag->getFrequency();
        }
        $result = [];
        foreach ($tags as $tag) {
            $percent = round($tag->getFrequency() / $total, 3) * 100;
            $result[] = ['name' => $tag->getName(), 'weight' => $this->getWeight($percent)];
        }
        shuffle($result);
        return $result;
    }

    public function getWeight($percent)
    {
        if ($percent <= 3) return 'weight-1';
        if ($percent <= 5 && $percent > 3) return 'weight-2';
        if ($percent > 5 && $percent <= 7) return 'weight-3';
        if ($percent > 7 && $percent <= 10) return 'weight-4';
        return 'weight-5';
    }
}