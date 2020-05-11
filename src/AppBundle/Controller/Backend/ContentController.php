<?php

namespace AppBundle\Controller\Backend;

use AppBundle\Entity\Faq;
use AppBundle\Entity\FaqCategory;
use AppBundle\Entity\FooterHeader;
use AppBundle\Entity\HomePage;
use AppBundle\Entity\Page;
use AppBundle\Entity\Slide;
use AppBundle\Form\Type\FaqCategoryType;
use AppBundle\Form\Type\FaqType;
use AppBundle\Form\Type\PageType;
use AppBundle\Form\Type\SlideType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    /**
     * @Route("/admin/content", name="admin_content")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();

        return $this->render('backend/content/index.html.twig', array(
            'pages' => $manager->getRepository('AppBundle:Page')->findAll(),
            'slides' => $manager->getRepository('AppBundle:Slide')->findAll(),
            'homePageBlocks' => $manager->getRepository('AppBundle:HomePage')->findAll(),
            'homePageSeo' => $manager->getRepository('AppBundle:Seo')->findOneByPage('homepage'),
            'faqPageSeo' => $manager->getRepository('AppBundle:Seo')->findOneByPage('faq'),
            'footerHeaders' => $manager->getRepository('AppBundle:FooterHeader')->findAll(),
            'faqCategories' => $manager->getRepository('AppBundle:FaqCategory')->findAll(),
        ));
    }

    /**
     * @Route("/admin/content/page", name="admin_content_page")
     */
    public function pageAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        return $this->processPageForm($request, $form, $page);
    }

    /**
     * @Route("/admin/content/page/{id}", name="admin_content_page_edit")
     */
    public function editPageAction(Request $request, Page $page)
    {
        $form = $this->createForm(PageType::class, $page);
        return $this->processPageForm($request, $form, $page);
    }

    public function processPageForm($request, $form, $page)
    {
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                if(empty(trim($page->getUri()))){
                    $page->setUri(str_replace(" ", "_", $page->getName()));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($page);
                $em->flush();
                return $this->redirect($this->generateUrl('admin_content'));
            }
        }

        return $this->render('backend/content/page.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
        ));
    }

    /**
     * @Route("/admin/content/page/{id}/{property}/{value}", name="admin_content_set_page_property")
     */
    function setPagePropertyAction(Page $page, $property, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $setter = 'set' . ucfirst($property);
        $page->$setter($value);
        $em->persist($page);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/content/page/{id}/delete", name="admin_content_page_delete")
     */
    function deletePageAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/content/slide/image/{id}", name="admin_content_slide_image_edit")
     */
    public function editSlideImageAction(Request $request, Slide $slide)
    {
        $file = $request->files->get('image');
        if($file instanceof UploadedFile){
            $slide->setFile($file);
            $em = $this->getDoctrine()->getManager();
            $slide->preUpload();
            $slide->upload();
            $em->persist($slide);
            $em->flush();
        }
        return new Response();
    }

    /**
     * @Route("/admin/content/slide/{id}", name="admin_content_slide_edit")
     */
    public function editSlideAction(Request $request, Slide $slide)
    {
        $form = $this->createForm(SlideType::class, $slide);
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($slide);
                $em->flush();
            }
        }

        return $this->render('backend/content/slide.html.twig', array(
            'form' => $form->createView(),
            'slide' => $slide,
        ));
    }

    /**
     * @Route("/admin/content/homepage/block/{id}", name="admin_content_homepage_block_edit")
     */
    public function editHomePageBlockAction(Request $request, HomePage $homePageBlock)
    {
        $name = $request->request->get('name', null);
        $headerType = $request->request->get('headerType', null);
        $block1 = $request->request->get('block1', null);
        $block2 = $request->request->get('block2', null);
        $block3 = $request->request->get('block3', null);
        $block4 = $request->request->get('block4', null);

        if(null !== $name){
            $homePageBlock->setName($name);
        }

        if(null !== $headerType){
            $homePageBlock->setHeaderType($headerType);
        }

        if(null !== $block1){
            $homePageBlock->setBlock1($block1);
        }

        if(null !== $block2){
            $homePageBlock->setBlock2($block2);
        }

        if(null !== $block3){
            $homePageBlock->setBlock3($block3);
        }

        if(null !== $block4){
            $homePageBlock->setBlock4($block4);
        }

        $em = $this->getDoctrine()->getManager();

        if($homePageBlock->getChildren()->count() > 0){
            foreach ($homePageBlock->getChildren() as $key => $childrenBlock){
                $blockName = $request->request->get('blockName' . ($key + 1) , null);
                $blockHeaderType = $request->request->get('blockHeaderType' . ($key + 1), null);
                $blockText = $request->request->get('blockText' . ($key + 1), null);

                if(null !== $blockName){
                    $childrenBlock->setName($blockName);
                }

                if(null !== $blockHeaderType){
                    $childrenBlock->setHeaderType($blockHeaderType);
                }

                if(null !== $blockText){
                    $childrenBlock->setBlock1($blockText);
                }
                $em->persist($childrenBlock);
                $em->flush();
            }
        }

        $em->persist($homePageBlock);
        $em->flush();

        return new Response();
    }


    /**
     * @Route("/admin/content/pages/seo", name="admin_content_page_seo")
     */
    public function pageSeoAction(Request $request)
    {
        $page = $this->getDoctrine()->getManager()->getRepository('AppBundle:Seo')->findOneByPage(
            $request->request->get('page')
        );

        $page->setTitle($request->request->get('title'));
        $page->setDescription($request->request->get('description'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();

        return new Response();
    }


    /**
     * @Route("/admin/content/footer/header/{id}", name="admin_content_footer_header_edit")
     */
    public function editFooterHeaderAction(Request $request, FooterHeader $footerHeader)
    {
        $name = $request->request->get('name', null);

        if(null !== $name){
            $footerHeader->setName($name);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($footerHeader);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/admin/content/faq/category", name="admin_content_faq_category")
     */
    public function faqCategoryAction(Request $request)
    {
        $category = new FaqCategory();
        $form = $this->createForm(FaqCategoryType::class, $category);
        return $this->processFaqCategoryForm($request, $form, $category);
    }

    /**
     * @Route("/admin/content/faq/category/{id}", defaults={"id" = null}, name="admin_content_faq_category_edit")
     */
    public function editFaqCategoryAction(Request $request, FaqCategory $category)
    {
        $form = $this->createForm(FaqCategoryType::class, $category);
        return $this->processFaqCategoryForm($request, $form, $category);
    }

    public function processFaqCategoryForm($request, $form, $category)
    {
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                return $this->render('backend/content/faq_section.html.twig', array(
                    'active_tab' => $request->request->get('active_tab'),
                    'faqCategories' => $this->getDoctrine()
                        ->getManager()
                        ->getRepository('AppBundle:FaqCategory')
                        ->findAll(),
                    'faqPageSeo' => $this->getDoctrine()
                        ->getManager()
                        ->getRepository('AppBundle:Seo')
                        ->findOneByPage('faq'),
                ));
            }
        }

        return $this->render('backend/content/faq_category.html.twig', array(
            'form' => $form->createView(),
            'category' => $category,
        ));
    }

    /**
     * @Route("/admin/content/faq/category/{id}/{property}/{value}", defaults={"id" = null}, name="admin_content_faq_category_set_property")
     */
    public function setFaqCategoryPropertyAction(FaqCategory $category, $property, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $setter = 'set' . ucfirst($property);
        $category->$setter($value);
        $em->persist($category);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/content/faq/category/{id}/delete", name="admin_content_faq_category_delete")
     */
    function deleteFaqCategoryAction(FaqCategory $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/content/faq", name="admin_content_faq")
     */
    public function faqAction(Request $request)
    {
        $faq = new Faq();
        $form = $this->createForm(FaqType::class, $faq);
        return $this->processFaqForm($request, $form, $faq);
    }

    /**
     * @Route("/admin/content/faq/{id}", requirements={"id": "\d+"}, name="admin_content_faq_edit")
     */
    public function editFaqAction(Request $request, Faq $faq)
    {
        $form = $this->createForm(FaqType::class, $faq);
        return $this->processFaqForm($request, $form, $faq);
    }


    /**
     * @Route("/admin/content/faq/{id}/delete", requirements={"id": "\d+"}, name="admin_content_faq_delete")
     */
    function deleteFaqAction(Faq $faq)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($faq);
        $em->flush();
        return new Response();
    }


    public function processFaqForm($request, $form, $faq)
    {
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($faq);
                $em->flush();
                return $this->render('backend/content/faq_section.html.twig', array(
                    'active_tab' => $request->request->get('active_tab'),
                    'faqCategories' => $this->getDoctrine()
                        ->getManager()
                        ->getRepository('AppBundle:FaqCategory')
                        ->findAll(),
                    'faqPageSeo' => $this->getDoctrine()
                        ->getManager()
                        ->getRepository('AppBundle:Seo')
                        ->findOneByPage('faq'),
                ));
            }
        }

        return $this->render('backend/content/faq.html.twig', array(
            'form' => $form->createView(),
            'faq' => $faq,
        ));
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
