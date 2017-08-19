<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BlogBundle\Helpers\Pager;
use Symfony\Component\HttpFoundation\Response;

class HelperController extends Controller
{
    public function pagerAction($routeName, $paramName, $page, $count)
    {
        $perPage = $this->getParameter('posts.per_page');
        $maxButtons = $this->getParameter('pagination.max_buttons');
        $pager = new Pager($page, $perPage, $count, $maxButtons);
        $pageList = $pager->getChunk();

        if (count($pageList) <= 1) {
            return new Response('');
        }

        return $this->render('BlogBundle:partials:pagination.html.twig', [
            'page' => $page,
            'pageList' => $pageList,
            'routeName' => $routeName,
            'paramName' => $paramName,
            'pageCount' => $pager->getPageCount()
        ]);
   }
}