<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BlogBundle\Helpers\Pager;
use Symfony\Component\HttpFoundation\Response;

class HelperController extends BaseController
{
    public function pagerAction($routeName, $params, $count, $perPage)
    {
        $perPage = $this->getParameter('default.per_page');
        $maxButtons = $this->getParameter('pagination.max_buttons');
        $page = $params['page'];

        $params['page'] = 1;
        $url = $this->generateUrl($routeName, $params);
        #return new Response($url);

        $pager = new Pager($page, $perPage, $count, $maxButtons);

        $pageList = $pager->getChunk();

        if (count($pageList) <= 1) {
            return new Response('');
        }

        return $this->render('BlogBundle:helper:pagination.html.twig', [
            'page' => $page,
            'pageList' => $pageList,
            'routeName' => $routeName,
            'url' => $url,
            'pageCount' => $pager->getPageCount()
        ]);
   }

    public function commentsAction($post, $comments)
    {
        return $this->render('BlogBundle:helper:comments.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
   }

    public function tagCloudAction()
    {
        $tags = $this->em->getRepository('BlogBundle:Tag')->findAll();

        return $this->render('BlogBundle:helper:tag_cloud.html.twig', [
            'tags' => $tags
        ]);
   }
}