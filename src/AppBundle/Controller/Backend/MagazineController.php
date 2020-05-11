<?php

namespace AppBundle\Controller\Backend;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MagazineController extends Controller
{
    /**
     * @Route("/admin/magazine/list/{page}", defaults={"page" = 1}, name="admin_magazine")
     */
    public function indexAction(Request $request, $page)
    {
        $articlesRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Article');
        $articles = $articlesRepo->findAll();

        /*
        $users = $usersRepo->setAdminMode()->search(
            $this->getUser(),
            $data,
            $this->get('knp_paginator'),
            $page,
            20
        );
        */

        //$articles->setTemplate('backend/pagination.html.twig');

        return $this->render('backend/magazine/index.html.twig', array(
            'articles' => $articles,
            'seo' => $this->getDoctrine()->getRepository('AppBundle:Seo')->findOneByPage('magazine'),
        ));
    }

    /**
     * @Route("/admin/magazine/article", name="admin_magazine_article")
     */
    public function articleAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        return $this->processArticleForm($request, $form, $article);
    }

    /**
     * @Route("/admin/magazine/article/{id}", defaults={"id" = null}, name="admin_magazine_article_edit")
     */
    public function editArticleAction(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article);
        return $this->processArticleForm($request, $form, $article);
    }

    public function processArticleForm($request, $form, $article)
    {
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                if(empty($article->getId())){
                    $article->setDate(new \DateTime());
                }

                if(empty(trim($article->getUri()))){
                    $article->setUri(str_replace(" ", "_", $article->getName()));
                }

                $file = $request->files->get('image');
                if($file instanceof UploadedFile) {
                    $article->setFile($file);
                    $article->preUpload();
                    $article->upload();
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                return $this->redirect($this->generateUrl('admin_magazine'));
            }
        }

        return $this->render('backend/magazine/article.html.twig', array(
            'form' => $form->createView(),
            'article' => $article,
        ));
    }

    /**
     * @Route("/admin/magazine/article/{id}/{property}/{value}", name="admin_magazine_set_article_property")
     */
    function setArticlePropertyAction(Article $article, $property, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $setter = 'set' . ucfirst($property);
        $article->$setter($value);
        $em->persist($article);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/magazine/article/{id}/delete", name="admin_magazine_articles_delete")
     */
    function deleteArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        return new Response();
    }

    public function setUpCloudinary()
    {
        \Cloudinary::config(array(
            "cloud_name" => "greendate",
            "api_key" => "333193447586872",
            "api_secret" => "rT6Kccy2ZHThaBlFzlOeLKE085o"
        ));
    }


}
