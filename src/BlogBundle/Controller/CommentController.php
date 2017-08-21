<?php

namespace BlogBundle\Controller;

use BlogBundle\Repository\CommentRepository;
use BlogBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Helpers\Pager;
use Symfony\Component\HttpFoundation\Response;
use BlogBundle\Entity\Comment;

class CommentController extends BaseController
{
    /** @var  CommentRepository */
    protected $repo;

    #public function approveAction($id)
    public function approveAction($id, $disapprove)
    {
       # $disapprove = false;
        $comment = $this->loadEntity($id);
        if ($disapprove) {
            $comment->disapprove();
        } else {
            $comment->approve();
        }
        $this->em->flush();
        return new Response($comment->getStatus() == Comment::STATUS_PENDING ? 'Approve' : 'Disapprove');
    }

    public function adminAction($page)
    {
        $perPage = $this->getParameter('default.per_page');
        $commentsCount = $this->repo->getEntitiesCount();
        $comments = $this->repo->getEntitesForPage($page, $perPage);
        $pageCount = (new Pager($page, $perPage, $commentsCount))->getPageCount();

        if ($page < 0 || $page > $pageCount) {
            throw $this->createNotFoundException();
        }

        return $this->render('BlogBundle:comment:admin.html.twig', [
            'comments' => $comments,
            'perPage' => $perPage,
            'count' => $commentsCount,
            'page' => $page
        ]);
    }

    public function deleteAction($id)
    {
        $comment = $this->loadEntity($id);
        $this->em->remove($comment);
        $this->em->flush();
        $this->addFlash('success', 'Comment have been deleted');
        return $this->redirectToRoute('blogbundle_comment_admin');
    }

    public function updateAction($id)
    {
        $comment = $this->loadEntity($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest(Request::createFromGlobals());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Comment have been updated');
            return $this->redirectToRoute('blogbundle_comment_admin');
        }

        return $this->render("BlogBundle:comment:update.html.twig", [
           'comment' => $comment,
           'form' => $form->createView()
        ]);
    }




}