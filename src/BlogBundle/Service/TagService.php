<?php

namespace BlogBundle\Service;

use BlogBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;

class TagService
{
    protected $em;

    /* @var Tag */
    protected $tagsRepo;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->tagsRepo = $this->em->getRepository(Tag::class);
    }

    public function array2string($tags)
    {
        return join(', ', $tags);
    }

    public function string2array($tags)
    {
        return preg_split('#\s*,\s*#', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    }

    public function normalizeTags($tags)
    {
        return $this->array2string(array_unique($this->string2array($tags)));
    }

    public function updateFrequency($oldTags, $newTags)
    {
//        var_dump($oldTags);
//        var_dump($newTags);
//        die;
        $oldTags = $this->string2array($oldTags);
        $newTags = $this->string2array($newTags);
        $this->addTags(array_values(array_diff($newTags, $oldTags)));
        $this->removeTags(array_values(array_diff($oldTags, $newTags)));
    }

    public function addTags($tags)
    {
        foreach ($tags as $tag) {
            if ($t = $this->tagsRepo->findOneBy(['name' => $tag])) {
                $t->setFrequency($t->getFrequency() + 1);
            } else {
                $t = new Tag;
                $t->setName($tag);
                $t->setFrequency(1);
                $this->em->persist($t);
            }
        }

        $this->em->flush();
        #var_dump($this->em->getUnitOfWork()->getScheduledEntityInsertions()); die;
    }

    public function removeTags($tags)
    {
        if (empty($tags))
            return;
        foreach ($tags as $tag) {
            $t = $this->tagsRepo->findOneBy(['name' => $tag]);
            if (!$t) continue;

            $frequency = $t->getFrequency();
            if ($frequency > 1) {
                $t->setFrequency($frequency - 1);
            } else {
                $this->em->remove($t);
            }
        }
        $this->em->flush();
    }

}