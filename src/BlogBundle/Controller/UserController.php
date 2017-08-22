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

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.encoder_factory')->getEncoder($user)->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            $this->sendMail('Registration at blog', $user->getEmail(),
                '<h1>You have been successfull registred</h1>', $this->getParameter('mail.contacts'));

            $this->addFlash('success', "You have been successfully registred, now you can login");
            return $this->redirectToRoute('homepage');
        }

        return $this->render('BlogBundle:user:register.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    protected function sendMail($subject, $to, $body, $from=null)
    {
        $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to)
                    ->setFrom($from)
                    ->setBody($body, 'text/html');
        return $this->get('mailer')->send($message);
    }
}