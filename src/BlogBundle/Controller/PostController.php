<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Comment;
use BlogBundle\Entity\Post;
use BlogBundle\Form\Type\CommentType;
use BlogBundle\Form\Type\PostType;
use BlogBundle\Helpers\Pager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use BlogBundle\Repository\PostRepository;

class PostController extends BaseController
{

    /* @var PostRepository */
    protected $repo;

    public function testAction()
    {
         throw $this->createAccessDeniedException('Denied Action');

        return new Response('Some text');
    }

    public function indexAction($page, $tag)
    {
        $perPage = $this->getParameter('default.per_page');
        $postsCount = $this->repo->getEntitiesCount(['tag' => $tag]);

        $posts = $this->repo->getEntitesForPage($page, $perPage, false, ['tag' => $tag]);
        $pageCount = (new Pager($page, $perPage, $postsCount))->getPageCount();

        #var_dump($pageCount); die;

        if ($page < 1 || $page > $pageCount) {
            throw $this->createNotFoundException();
        }

         return $this->render('BlogBundle:post:index.html.twig', [
            'posts' => $posts,
            'count' => $postsCount,
            'page' => $page,
            'perPage' => $perPage,
            'tag' => $tag
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
        $this->checkStatus($post);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $commentsNeedApprove = $this->getParameter('comments.need_approve');

        return $this->render('BlogBundle:post:view.html.twig', [
            'post' => $post,
            'comments' => $post->getComments($commentsNeedApprove),
            'form' => $form->createView()
        ]);
    }

    public function viewHandleAction($id)
    {
        $post = $this->repo->find($id);
        $this->checkStatus($post);

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid()) {
            $post->addComment($comment);
            $status = $this->getParameter('comments.need_approve') ? Comment::STATUS_PENDING : Comment::STATUS_APPROVED;
            $comment->setStatus($status);
            $this->em->persist($comment);
            $this->em->flush();
            $this->addFlash('success', 'Your comment has been added');
            $this->addFlash('success', 'Second flash message');
            return $this->redirectToRoute('blogbundle_post_view', ['id' => $id]);
        }

        $commentsNeedApprove = $this->getParameter('comments.need_approve');

        return $this->render('BlogBundle:post:view.html.twig', [
            'post' => $post,
            'comment' => $comment,
            'comments' => $post->getComments($commentsNeedApprove)
        ]);
    }

    private function checkStatus(Post $post)
    {
        if ($post->getStatus() == Post::STATUS_DRAFT
            && !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createNotFoundException('Requested post not found...');
        }
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