<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function loginAction()
    {
        return $this->render('BlogBundle:user:login.html.twig');
    }

    public function adminAction()
    {
        return $this->render('BlogBundle:user:admin.html.twig');
    }
}