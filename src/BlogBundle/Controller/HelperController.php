<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BlogBundle\Helpers\Pager;
use Symfony\Component\HttpFoundation\Request;
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
        if (strrpos($url, '/') != strlen($url) - 1) {
            $url .= '/';
        }

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
        $tags = $this->em->getRepository('BlogBundle:Tag')->getTagsWithWeight();

        return $this->render('BlogBundle:helper:tag_cloud.html.twig', [
            'tags' => $tags
        ]);
   }

    public function menuAction()
    {
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');

        return $this->render('BlogBundle:helper:menu.html.twig', [
            'route' => $currentRoute
        ]);
   }

    public function latestCommentsAction($limit=3)
    {

        $latestComments = $this->em->getRepository('BlogBundle:Comment')->getLatestComments($limit);

        return $this->render('BlogBundle:helper:latest_comments.html.twig', [
            'latestComments' => $latestComments
        ]);
   }
}