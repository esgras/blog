<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Post;
use BlogBundle\Form\Type\PostType;
use BlogBundle\Helpers\Pager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    /* @var EntityManager $em */
    private $em;

    /* @var Post */
    private $postRepo;

    public function setContainer(ContainerInterface $container = NULL)
    {
        parent::setContainer($container);

        $this->em = $this->getDoctrine()->getManager();
        $this->postRepo = $this->em->getRepository('BlogBundle:Post');
    }

    public function testAction()
    {
         throw $this->createAccessDeniedException('Denied Action');

        return new Response('Some text');
    }

    public function indexAction($page)
    {
        $perPage = $this->getParameter('posts.per_page');
        $postsCount = $this->postRepo->getPostsCount();
        $posts = $this->postRepo->getPostsForPage($page, $perPage);
        $pageCount = (new Pager($page, $perPage, $postsCount))->getPageCount();

        if ($page < 1 || $page > $pageCount) {
            throw $this->createNotFoundException();
        }

         return $this->render('BlogBundle:post:index.html.twig', [
            'posts' => $posts,
            'count' => $postsCount,
            'page' => $page
        ]);
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $post = new Post();
        $form = $this->createForm(PostType::class, $post, [
            'lookup' => $this->get('blog.lookupservice')
        ]);
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getUser()->addPost($post);
            $post->setTags($this->get('blog.tagservice')->normalizeTags($post->getTags()));
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Post was created');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('BlogBundle:post:create.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    public function updateAction($id)
    {
        $post = $this->em->getRepository('BlogBundle:Post')->find($id);
        $form = $this->createForm(PostType::class, $post, [
            'lookup' => $this->get('blog.lookupservice')
        ]);
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getUser()->addPost($post);
            $post->setTags($this->get('blog.tagservice')->normalizeTags($post->getTags()));
            $this->em->flush();
            $this->addFlash('success', 'Post was updated');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('BlogBundle:post:update.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    public function viewAction($id)
    {
        $post = $this->em->getRepository('BlogBundle:Post')->find($id);
        $user = $this->getUser();
        $auth = $this->get('security.authorization_checker');

        if ($post->getStatus() == Post::STATUS_DRAFT) {
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                #throw $this->createAccessDeniedException();
                throw $this->createNotFoundException('Requested post not found!');
            }
        }


        $commentsNeedApprove = $this->getParameter('comments.need_approve');

        return $this->render('BlogBundle:post:view.html.twig', [
            'post' => $post,
            'comments' => $post->getComments($commentsNeedApprove)
        ]);
    }

    public function deleteAction($id)
    {
        $post = $this->em->getRepository('BlogBundle:Post')->find($id);
        $this->em->remove($post);
        $this->em->flush();
        $this->addFlash('success', 'Post was successfuly deleted');
        return $this->redirectToRoute('homepage');
    }
}