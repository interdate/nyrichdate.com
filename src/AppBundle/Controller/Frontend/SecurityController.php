<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\User;
use AppBundle\Form\Type\ActivationType;
use AppBundle\Form\Type\SignUpOneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints;


class SecurityController extends Controller
{

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/password", name="password_recovery")
     */
    public function passwordAction(Request $request)
    {
        $success = false;
        
        $form = $this->createFormBuilder()
            ->add('email', 'text', array(
                'label' => 'Email:',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(array(
                        'message' => 'Email {{ value }} is incorrect',
                        'checkMX' => true,
                    ))
                )
            ))
            ->getForm();

        if($request->isMethod('POST')){
            $form->submit($request->request->get($form->getName()));
            if($form->isValid() && $form->isSubmitted()){
                $success = false;
                $requestData = $request->get('form');
                $email = $requestData['email'];

                $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByEmail($email);
                if($user){
                    $em = $this->getDoctrine()->getManager();
                    $pass = substr(sha1(uniqid(mt_rand(), true)), 0 , 7);
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $pass);
                    $user->setPassword($encoded);
                    $em->persist($user);
                    $em->flush();

                    $subject = $request->getHost() . ' | ' . $this->get('translator')->trans('Reset Password') . ' | ' . date('m/d/Y H:i:s');

                    $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);
                    $text = '
                        <div>' .
                            $this->get('translator')->trans('Hello') . ' ' .
                            $user->getUsername() . ',<br />' .
                            $this->get('translator')->trans('Your new password is:') .
                            '<input type="text" onfocus="this.select();" onmouseup="return false;" value="' . $pass . '"/>
                            <br><br>' .
                            $this->get('translator')->trans('For questions or concerns will be happy to be of service at:') . ' ' . $settings->getContactEmail() . '
                            <br><br><br>' .
                            $this->get('translator')->trans('Yours, <br> Team NYRichdate.com') .
                            '<br>
                            ' . $request->getBaseURL() . '
                        </div>
                    ';

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    //$headers .= 'From: info@' . $request->getHost() . ' <info@' . $request->getHost() . '>' . "\r\n";
                    $headers .= 'From: ' . $settings->getContactFromEmail() . ' <' . $settings->getContactFromEmail() . '>' . "\r\n";
                    mail($user->getEmail(),$subject,$text,$headers);
                    $success = true;


                    /*$message = \Swift_Message::newInstance()
                        ->setSubject('greendate.co.il | איפיס סיסמה |' . date('d/m/Y H:i:s'))
                        ->setFrom('info@greendate.co.il')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                'frontend/emails/password_recovery.html.twig',
                                array(
                                    'username' => $user->getUsername(),
                                    'password' => $pass,
                                )
                            ),
                            'text/html'
                        )
                    ;

                    $this->get('mailer')->send($message);*/



                }else{
                    $form->get('email')->addError(new FormError("Email doesn't exist"));
                }
            }
        }

        return $this->render('frontend/security/password.html.twig', array(
            'form' => $form->createView(),
            'success' => $success
        ));

    }


    /**
     * @Route("/sign_up/helper", name="sign_up_helper")
     */
    public function signUpHelperAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $user = new User();
            $form = $this->createForm(SignUpOneType::class, $user);
            $form->handleRequest($request);
            return $this->render('frontend/security/sign_up/1.html.twig', array(
                'form' => $form->createView(),
                'errors' => false,
            ));
        }
    }


    /**
     * @Route("/sign_up", name="sign_up")
     */
    public function signUpAction(Request $request)
    {
        $errors = false;
        $user = new User();
        $flow = $this->get('SignUpFlow');
        $this->setZipCode($request, $user);
        $flow->bind($user);
        $form = $flow->createForm();

        if($request->isMethod('POST')) {
            if($form->has('email')) {
                $emailInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:EmailBlocked')->findOneByValue(strtolower($form->get('email')->get('first')->getData()));
                if($emailInBlocked) {
                    $form->get('email')->get('first')->addError(new FormError('Email already exists in the system'));
                    $errors = true;
                }
            }
            if($form->has('phone')) {
                $phoneInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:PhoneBlocked')->findOneByValue($form->get('phone')->getData());
                if($phoneInBlocked) {
                    $form->get('phone')->addError(new FormError('Phone already exists in the system'));
                    $errors = true;
                }
            }

            $postKey = 'sign_up_one';
            $post3 = $request->request->get('sign_up_one', false);
            if($post3 and empty($post3['agree'])){
                $form->get('agree')->addError(new FormError('You need to comfirm Term and Conditions'));
                $errors = true;
            }
            if(!$post3) {
                $postKey = 'sign_up_two';
                $post3 = $request->request->get('sign_up_two', false);
            }
            if(!$post3) {
                $postKey = 'sign_up_three';
                $post3 = $request->request->get('sign_up_three', false);
            }
            if($post3) {
                $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
                $post3 = $userRepo->removeWordsBlocked($post3,array('username','occupation','about','looking'));
                $request->request->set($postKey, $post3);
            }
            if(!$errors and $flow->isValid($form)) {
                $flow->saveCurrentStepData($form);

                if($flow->nextStep()) {
                    $form = $flow->createForm();
                }
                else{

                    $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);
                	$text = '
                    <div>' .
                        $this->get('translator')->trans('Hi') . ' ' . $user->getUsername() . ',<br />' .
                        $this->get('translator')->trans('Thank you for registering on NYRichdate. <br> Here are your login information site: <br> E-mail:') . ' ' . $user->getEmail() . '<br>' .
                        $this->get('translator')->trans('Password:') . ' ' . $user->getPassword() . '<br><br>' .
                        $this->get('translator')->trans('For questions or concerns will be happy to be of service at:') . ' ' . $settings->getContactEmail() . '
						<br><br><br>' .
                        $this->get('translator')->trans('Yours, <br> Team NYRichdate') .
						'<br>' . $request->getBaseURL() . '
                    </div>';

                    $rolesRepo = $this->getDoctrine()->getRepository('AppBundle:Role');
                    $role = $rolesRepo->find(2);
                    $user->setRole($role);

                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($encoded);

                    $user->setSignUpDate(new \DateTime());
                    $user->setLastActivityAt(new \DateTime());
                    $user->setLastloginAt(new \DateTime());
                    if($user->getGender()->getId() == 2) {
                        $user->setIsActivated(true);
                    }elseif ($user->getGender()->getId() == 1){
                        $user->setIsActivated(false);
                        $user->setPhone(null);
                    }
                    $user->setZodiac($this->getDoctrine()->getManager()->getRepository('AppBundle:Zodiac')->getZodiacByDate($user->getBirthday()));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $flow->reset();
                    //$this->get('mailer')->send($message);
                    
                    $subject = $this->get('translator')->trans('Welcome to NYRichdate') . ' ' . $request->getBaseURL() . '!';
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: ' . $settings->getContactFromEmail() . ' <' . $settings->getContactFromEmail() . '>' . "\r\n";
                    mail($user->getEmail(),$subject,$text,$headers);

                    $session = new Session();
                    $session->set('userId', $user->getId());

                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.token_storage')->setToken($token);

                    return $this->redirect($this->generateUrl('sign_up_photos'));
                }
            }
            elseif(!$request->request->get('flow_signUp_transition')) {
                $errors = true;
            }


        }

        return $this->render('frontend/security/sign_up/index.html.twig', array(
            'form' => $form->createView(),
            'terms' => $this->getDoctrine()->getManager()->getRepository('AppBundle:Page')->find(4), //Terms and Conditions
            'flow' => $flow,
            'errors' => $errors,
        ));

    }

    /**
     * @Route("/sign_up/photo", name="sign_up_photo")
     */
    public function photoAction()
    {
        return $this->render('frontend/security/sign_up/index.html.twig');
    }

    /**
     * @Route("/sign_up/photos", name="sign_up_photos")
     */
    public function photosAction()
    {
        return $this->render('frontend/security/sign_up/index.html.twig', array(
            'page_policy' => $this->getDoctrine()->getManager()->getRepository('AppBundle:Page')->find(6), //Photos Policy
        ));
    }

    /**
     * @Route("/sign_up/subscription", name="sign_up_subscription")
     * @Route("/user/subscription", name="subscription")
     */
    public function subscriptionAction(Request $request)
    {

        $route = $request->attributes->get('_route');
        if($route == 'subscription' and ($this->getUser()->getGender()->getId() == 2 or $this->getUser()->isPaying()) and $this->getUser()->getId() != 111 ){
            return $this->redirectToRoute('user_homepage');
        }

        return $this->render('frontend/' . (($route == 'subscription') ? 'user' : 'security/sign_up') . '/subscription.html.twig', array(
            //'route' => $route
        ));
    }

    /**
     * @Route("/sign_up/activation", name="sign_up_activation")
     */
    public function activationAction(Request $request)
    {
        $error = false;
        $errorMessage = null;
        $sent = false;
        $resent = false;
        $user = $this->getUser();

        if($request->isMethod('POST')){
            $phone = $request->request->get('phone', null);
            $code = $request->request->get('code', null);
            $em = $this->getDoctrine()->getManager();
            $resent = (boolean)$request->request->get('resent', false);

            if($resent and $user->getSmsCount() == 3){
                $error = true;
                $errorMessage = $this->get('translator')->trans('Option Resent SMS is no longer available. For activate your account, please contact us');
            }
            //var_dump($errorMessage);die;
            if(null !== $phone) {
                if (preg_match('/[^0-9,]/', $phone)){
                    $error = true;
                    $errorMessage = $this->get('translator')->trans('The phone must contain only numbers');
                }
                if(!$error) {
                    if (substr($phone, 0, 1) == '0') {
                        $phone = '972' . substr($phone, 1);
                    }
                    if (substr($phone, 0, 1) != '0') {
                        $phone = '1' . ((substr($phone, 0, 1) != '1') ? $phone : substr($phone, 1));
                    }
                    //var_dump($phone);die;
                    $phoneInBlocked = $em->getRepository('AppBundle:PhoneBlocked')->findOneByValue($phone);
                    $existsPhone = $em->getRepository('AppBundle:User')->findOneByPhone($phone);
                       // var_dump($existsPhone->getId(),$user->getId());die;
                    if(is_object($existsPhone) and $existsPhone->getId() == $user->getId()){
                        $existsPhone = false;
                    }

                    if ($phoneInBlocked or $existsPhone) {
                        $error = true;
                        $errorMessage = $this->get('translator')->trans('Phone already exists in the system');
                    } elseif($user->getSmsCount() == 3){
                        $error = true;
                        $errorMessage = $this->get('translator')->trans('Option Resent SMS is no longer available. For activate your account, please contact us');
                    } else {

                        $code = rand(10000, 99999);
                        /*
                        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                                [
                                    'api_key' => 'eadca325',
                                    'api_secret' => '5e5205c0',
                                    'to' => $phone,
                                    'from' => '12082254688',//'NYSD',
                                    'text' => 'Activation code: ' . $code,
                                ]
                            );

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        */
                        $postdata = http_build_query(array(
                            'to' => $phone,
                            'from' => '12082254688',//'NYSD',
                            'text' => 'Activation code: ' . $code,
                        ));
                        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(array(
                                'api_key' => 'eadca325',
                                'api_secret' => '5e5205c0'
                            ));

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        $response = curl_exec($ch);
                        curl_close($ch);

                        $response = json_decode($response);
                        $status = $response->messages[0]->status;
                        //var_dump($url,$response);die;
                        if ($status == 0) {
                            $user->setCode($code);
                            $user->setPhone($phone);
                            $user->setSmsCount($user->getSmsCount() + 1);
                            $sent = true;
                        } else {
                            $error = true;
                            $errorMessage = $this->get('translator')->trans('Phone number is incorrect, please try again');
                        }
                    }
                }
            }
            elseif(null !== $code){

                if($code == $user->getCode()){
                    $user->setIsActivated(true);
                }
                else{
                    $error = true;
                    $errorMessage = $this->get('translator')->trans('Activation code is incorrect, please try again');
                }

                $sent = true;

            }

            if(!$error) {
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('frontend/security/sign_up/activation.html.twig', array(
            'sent' => $sent,
            'resent' => $resent,
            'error' => $error,
            'errorMessage' => $errorMessage,
        ));
    }


    public function setUpCloudinary(){
        /*
        \Cloudinary::config(array(
            "cloud_name" => "greendate",
            "api_key" => "333193447586872",
            "api_secret" => "rT6Kccy2ZHThaBlFzlOeLKE085o"
        ));
        */
    }

    public function setUserZipCodeFromSession($user)
    {
        $session = new Session();
        $sessionData = $session->all();
        //$session->clear();

        if (isset($sessionData['craue_form_flow'])) {

            foreach ($sessionData['craue_form_flow']['signUp'] as $item) {

                if (array_key_exists('1', $item['data'])) {
                    $array = $item['data'][1];
                    $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($array['zipCode']));
                }
            }
        }
    }

    public function setZipCode($request, $user)
    {
        $post  = $request->request->get('sign_up_one');
        if(isset($post['zipCode'])) {
            $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($post['zipCode']));
        }
        else{
            $this->setUserZipCodeFromSession($user);
        }
    }

/*
    public function getCurrentUser()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getUser();
        } else {
            $session = new Session();
            //$session->start();
            $userId = $session->get('userId');
            $usersRepo = $this->getDoctrine()->getRepository('AppBundle:User');
            return $usersRepo->find($userId);
        }

        return null;
    }
*/
    








}
