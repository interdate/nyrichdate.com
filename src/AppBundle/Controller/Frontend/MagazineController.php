<?php

namespace AppBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MagazineController extends Controller
{
    /**
     * @Route("/blog/{page}", defaults={"page" = 1}, requirements={"page": "\d+"}, name="magazine")
     */
    public function indexAction(Request $request, $page)
    {
        $articlesRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Article');
        $articles = $articlesRepo->getAllOrderedBy('date', 'DESC', array(
            'paginator' => $this->get('knp_paginator'),
            'page' => $page,
            'perPage' => 10,
        ));

        return $this->render('frontend/magazine/index.html.twig', array(
            'articles' => $articles,
            'header' => 'Blog',
            'seo' => $this->getDoctrine()->getRepository('AppBundle:Seo')->findOneByPage('magazine'),
        ));
    }

    /**
     * @Route("/blog/{uri}", name="magazine_article")
     */
    public function articleAction($uri)
    {
        return $this->render('frontend/magazine/index.html.twig', array(
            'article' => $this->getDoctrine()->getRepository('AppBundle:Article')->findOneByUri($uri),
        ));
    }

}
