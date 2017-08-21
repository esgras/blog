<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class BaseController extends Controller
{
    /* @var EntityManager $em */
    protected $em;

    protected $repo;

    public function setContainer(ContainerInterface $container = NULL)
    {
        parent::setContainer($container);

        $this->em = $this->getDoctrine()->getManager();
        $parts = explode('\\', static::class);
        $entity = substr(array_pop($parts), 0,  -10);
        if (class_exists('BlogBundle\Entity\\' . $entity)) {
            $this->repo = $this->em->getRepository('BlogBundle:' . $entity);
        }
    }

    public function loadEntity($id)
    {
        if (empty($this->repo)) return null;

        $entity = $this->repo->find($id);
        if ($entity == NULL) {
            throw $this->createNotFoundException();
        }
        return $entity;
    }
}