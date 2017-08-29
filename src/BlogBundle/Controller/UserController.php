<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use BlogBundle\Form\Type\LoginType;
use BlogBundle\Form\Type\RegisterType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends BaseController
{


    public function loginAction()
    {
        $authUtils = $this->get('security.authentication_utils');
        $errors = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('BlogBundle:user:login.html.twig', array(
            'last_username' => $lastUsername,
            'errors'         => $errors,
        ));
    }

    public function forgotAction()
    {
        $form = $this->createFormBuilder()
                            ->add('email', TextType::class, [
                                    'constraints' => [new Email(), new NotBlank()], 'required' => false
                            ])->add('reset', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']])
                            ->getForm();
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = trim($data['email']);
            /** @var $user User*/
            $user = $this->repo->findOneBy(['email' => $email]);
            if ($user == NULL) {
                $form->get('email')->addError(new FormError('There is no user with that email'));
            } else {
                $this->addFlash('success', 'Check your email to reset password');
                $subject = 'Forgot password';
                $hash = md5(uniqid(rand(), true));
                $user->setHash($hash);
                $this->em->flush();
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($this->getParameter('mail.contacts'))
                    ->setTo($email)
                    ->setBody($this->renderView('BlogBundle:mail:forgot.html.twig', ['subject' => $subject, 'hash' => $hash]), 'text/html');
                $this->get('mailer')->send($message);

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('BlogBundle:user:forgot.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function resetAction($hash)
    {
        $user = $this->repo->findOneBy(['hash' => $hash]);
        if ($user == NULL) {
            throw $this->createNotFoundException('User with that hash not found');
        }
        $password = substr(md5(uniqid(rand(), true)), 0, 10);
        $pass = $this->get('security.encoder_factory')->getEncoder($user)->encodePassword($password, $user->getSalt());
        $user->setPassword($pass);
        $this->em->flush();
        $this->sendMail('Your new password', 'esgras@ukr.net',
            $this->renderView('BlogBundle:mail:reset.html.twig', ['password' => $password]));
        return $this->render('BlogBundle:user:reset.html.twig');
    }

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
        $from = isset($from) ? $from : $this->getParameter('mail.contacts');
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