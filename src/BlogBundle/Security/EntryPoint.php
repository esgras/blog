<?php

namespace BlogBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class EntryPoint implements AuthenticationEntryPointInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $content = $this->container->get('twig')->render('BlogBundle:security:403.html.twig', []);
        return new Response($content, 403);
    }

}