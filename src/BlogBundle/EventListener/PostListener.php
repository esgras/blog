<?php

namespace BlogBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use BlogBundle\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $post = $args->getObject();

        if (!$post instanceof Post) {
            return;
        }

        $this->updateFrequncy($post->getOldTags(), $post->getTags());
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $post = $args->getObject();

        if (!$post instanceof Post) {
            return;
        }

        $this->updateFrequncy($post->getOldTags(), $post->getTags());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $post = $args->getObject();

        if (!$post instanceof Post) return;

        $this->updateFrequncy($post->getTags(), '');
    }

    public function updateFrequncy($oldTags, $newTags)
    {
        $this->container->get('blog.tagservice')->updateFrequency($oldTags, $newTags);
    }

}