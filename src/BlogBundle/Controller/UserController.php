<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use BlogBundle\Form\Type\LoginType;
use BlogBundle\Form\Type\RegisterType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends BaseController
{


    public function loginAction()
    {
        $data = ['username' => 'hello'];
        $form = $this->createForm(LoginType::class, $data);
        $form->handleRequest(Request::createFromGlobals());
        #$errors = $form->getErrors();
        #var_dump($errors); die;
        #$form->isSubmitted() && $form->isValid();

        $authUtils = $this->get('security.authentication_utils');
        $errors = $authUtils->getLastAuthenticationError();
        #var_dump($error); die;
        #$lastUsername = $authUtils->getLastUsername();
        #$form = $this->createFormForModel();


        return $this->render('BlogBundle:user:login.html.twig', array(
            #'last_username' => $lastUsername,
            'errors'         => $errors,
            'form' => $form->createView()
        ));
    }
//
//    public function loginAction(Request $request, AuthenticationUtils $authUtils)
//    {
//        $error = $authUtils->getLastAuthenticationError();
//        $lastUsername = $authUtils->getLastUsername();
//
//        return $this->render('BlogBundle:user:login.html.twig', [
//            'last_username' => $lastUsername,
//            'error' => $error
//        ]);
//    }

    public function adminAction()
    {
        return $this->render('BlogBundle:user:admin.html.twig');
    }

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $user->getPassword();
            $password = $this->get('security.encoder_factory')->getEncoder($user)->encodePassword($currentPassword, $user->getSalt());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            $this->sendMail('Registration at blog', $user->getEmail(),
                $this->renderView('BlogBundle:mail:registration.html.twig', [
                    'user' => $user,
                    'password' => $currentPassword
                ]),
                $this->getParameter('mail.contacts'));

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

    protected function createFormForModel($model)
    {
        /* ======================================================
         * Start building
         */
        $formOptions = array(
            'cascade_validation' => true,
            'intention' => 'authenticate', // Matches security.yml
            'csrf_protection' => true,
        );
        $constraintOptions = array();

        // The name here matches your security file
        $builder = $this->get('form.factory')->createNamed(
            'cerad_tourn_account_user_login',
            'form',$model,$formOptions);

        $builder->add('username','text', array(
            'required' => true,
            'label'    => 'Email',
            'trim'     => true,
            'constraints' => array(
                new UsernameOrEmailExistsConstraint($constraintOptions),
            ),
            'attr' => array('size' => 30),
        ));
        $builder->add('password','password', array(
            'required' => true,
            'label'    => 'Password',
            'trim'     => true,
            'constraints' => array(
                new NotBlankConstraint($constraintOptions),
            ),
            'attr' => array('size' => 30),
        ));
        $builder->add('remember_me','checkbox',  array('label' => 'Remember Me'));

        // Actually a form
        return $builder;
    }
}