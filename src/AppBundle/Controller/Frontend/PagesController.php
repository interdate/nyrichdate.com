<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Page;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PagesController extends Controller
{
    /**
     * @Route("pages/{uri}", name="pages_page")
     */
    public function indexAction(Request $request, $uri)
    {
        return $this->render('frontend/pages/index.html.twig', array(
            'page' => $this->getDoctrine()->getManager()  ->getRepository('AppBundle:Page')->findOneByUri($uri),
        ));
    }

    /**
     * @Route("faq", name="faq")
     */
    public function faqAction()
    {
        return $this->render('frontend/pages/faq.html.twig', array(
            'categories' => $this->getDoctrine()->getManager()->getRepository('AppBundle:FaqCategory')->findByIsActive(true),
            'seo' => $this->getDoctrine()->getRepository('AppBundle:Seo')->findOneByPage('faq'),
        ));
    }

    /**
     * @Route("contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $sent = false;

        $user = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->getUser()
            : new User()
        ;

        $form = $this->createForm(ContactType::class, $user);

        if($request->isMethod('Post')){
            $form->handleRequest($request);
            if($form->isValid() && $form->isSubmitted()){
                $subject = $request->getHost() . " | Desktop | Contact Form | " . date('d/m/Y H:i') . " | " . $form->get('subject')->getData();
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ' . $form->getData()->getEmail() . ' <' . $form->getData()->getEmail() . '>' . "\r\n";
                $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);
                mail($settings->getContactEmail(),$subject,$form->get('text')->getData(),$headers);
                $sent = true;
            }

        }
        if($user->getId() == 111) {
            //var_dump(111); //$form->getErrors()
        }

        return $this->render('frontend/pages/contact.html.twig', array(
            'form' => $form->createView(),
            'sent' => $sent,
        ));
    }
}
