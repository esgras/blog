<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use BlogBundle\Form\Type\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function loginAction()
    {
        return $this->render('BlogBundle:user:login.html.twig');
    }

    public function adminAction()
    {
        return $this->render('BlogBundle:user:admin.html.twig');
    }

    public function registerAction()
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest(Request::createFromGlobals());

//        var_dump($user); die;
//        var_dump($form->isValid()); die;

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.encoder_factory')->getEncoder($user)->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', "You have been successfully registred, now you can login");
            return $this->redirectToRoute('homepage');
        }

        return $this->render('BlogBundle:user:register.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}