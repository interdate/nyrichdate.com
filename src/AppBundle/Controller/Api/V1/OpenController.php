<?php
namespace AppBundle\Controller\Api\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\SignUpOneType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\FormError;
use AppBundle\Entity\Page;
use DocDocDoc\NexmoBundle\DocDocDocNexmoBundle;



#use Symfony\Component\BrowserKit\Request;

class OpenController extends FOSRestController
{


    /**
     * @ApiDoc(
     *   description = "Get page",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function getPageAction(Page $page){
        return $this->view(array(
            'page' => array(
                'title' => $page->getName(),
                'content' => $page->getContent(),
            ),
        ), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   description = "Get FAQ page",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getFaqAction(Request $request){
        $cats = $this->getDoctrine()->getManager()->getRepository('AppBundle:FaqCategory')->findByIsActive(true);
        $seo = $this->getDoctrine()->getRepository('AppBundle:Seo')->findOneByPage('faq');
        $categories = array();
        foreach($cats as $cat){
            $category = array('name' => $cat->getName(), 'faq' => array());
            foreach($cat->getFaq() as $faq){
                if($faq->getIsActive()){
                    $category['faq'][] = array(
                        "q" => $faq->getName(),
                        "a" => $faq->getContent()
                    );
                }
            }
            $categories[] = $category;
        }/*
        {% for category in categories %}
        {% set i = loop.index + 1 %}
        <div class="faq_inner">
                    <div class="faqin_title"><h4>{{ category.name }}</h4></div>
                    <div class="faq_text">
                        <ul>
                            {% for faq in category.faq %}
                                {% if faq.isActive %}
                                    <li>
                                        <div class="faq_quest faq_quest{{ i }}">{{ faq.name }}</div>
                                        <div class="faq_ans faq_ans{{ i }}">
                                            <span>{{ faq.content }}</span>
                                        </div>
                                    </li>
                                {% endif %}
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            {% endfor %}
*/
        return $this->view(array(
            'page' => array(
                'title' => $seo->getTitle(),
                'description' => $seo->getDescription()
            ),
            'content' => $categories,
        ), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   description = "Get sign up Form",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getSignUpAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(SignUpOneType::class, $user);
        $flow = $this->get('SignUpFlow');
        return $this->view(array(
            'form' => $this->transformForm($form),
        ), Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *   description = "Create user",
     *   parameters={
     *      {"name"="form", "dataType"="string", "required"=false, "description"="parametrs"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function postSignUpAction(Request $request){

        $errors = false;
        $user = new User();
        $flow = $this->get('SignUpFlow');

        $isSetZipCode = $this->setZipCode($request, $user);

        $flow->bind($user);
        $form = $flow->createForm();

        //var_dump($flow->getFormData()->getZipCode()->getId());

        if($request->isMethod('POST')) {

            if($form->has('email')) {
                $emailInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:EmailBlocked')->findOneByValue(strtolower($form->get('email')->get('first')->getData()));
                if($emailInBlocked) {
                    $form->get('email')->get('first')->addError(new FormError($this->get('translator')->trans('Email already exists in the system')));
                    $errors = true;
                }
            }
            if($form->has('phone')) {
                $phoneInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:PhoneBlocked')->findOneByValue($form->get('phone')->getData());
                if($phoneInBlocked) {
                    $form->get('phone')->addError(new FormError($this->get('translator')->trans('Phone already exists in the system')));
                    $errors = true;
                }
            }
            $firtPost = $request->request->get('sign_up_one', false);
            if($firtPost and !$isSetZipCode){
                $form->get('zipCode')->addError(new FormError($this->get('translator')->trans('You need to choose Zip Code')));
                $errors = true;
            }
            if($firtPost and empty($firtPost['agree'])){
                $form->get('agree')->addError(new FormError($this->get('translator')->trans('You need to comfirm Term and Conditions')));
                $errors = true;
            }
            $postKey = 'sign_up_one';
            $post3 = $request->request->get('sign_up_one', false);
            if(!$post3) {
                $postKey = 'sign_up_two';
                $post3 = $request->request->get('sign_up_two', false);

                if($post3 and is_object($flow->getFormData()->getGender()) and $flow->getFormData()->getGender()->getId() == 1){
                   /* foreach (array('status','netWorth','income') as $fieldName){
                        if(empty($post3[$fieldName])){
                            $form->get($fieldName)->addError(new FormError($this->get('translator')->trans('This value should not be blank')));
                            $errors = true;
                        }
                    }*/
                }

            }
            if(!$post3) {
                $postKey = 'sign_up_three';
                $post3 = $request->request->get('sign_up_three', false);
                if($post3){

                    if(empty($post3['about'])){
                        $form->get('about')->addError(new FormError($this->get('translator')->trans('Min 10 letters in About Me')));
                        $errors = true;
                    }
                    if(empty($post3['looking'])){
                        $form->get('looking')->addError(new FormError($this->get('translator')->trans('Min 10 letters in What I\'m Looking For')));
                        $errors = true;
                    }
                    //hobbies
                    if(count((array)$post3['hobbies']) == 0){
                        $form->get('hobbies')->addError(new FormError($this->get('translator')->trans('Please choose Hobbies')));
                        $errors = true;
                    }
                }
            }
            if($post3) {
                $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
                $post3 = $userRepo->removeWordsBlocked($post3,array('username','occupation','about','looking'));
                $request->request->set($postKey, $post3);
            }

            //var_dump($form->getErrors());die;

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
                        $this->get('translator')->trans('Thank you for registering on NY Sugar daddy. <br> Here are your login information site: <br> E-mail:') . ' ' . $user->getEmail() . '<br>' .
                        $this->get('translator')->trans('Password:') . ' ' . $user->getPassword() . '<br><br>' .
                        $this->get('translator')->trans('For questions or concerns will be happy to be of service at:') . ' ' . $settings->getContactEmail() . '
						<br><br><br>' .
                        $this->get('translator')->trans('Yours, <br> Team NY Sugar daddy') .
						'<br>
						' . $request->getBaseURL() . '
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
                    $em = $this->getDoctrine()->getManager();
                    $user->setZodiac($this->getDoctrine()->getManager()->getRepository('AppBundle:Zodiac')->getZodiacByDate($user->getBirthday()));
                    $em->persist($user);
                    $em->flush();

                    $flow->reset();
                    //$this->get('mailer')->send($message);

                    $subject = $this->get('translator')->trans('Welcome to NY Sugar daddy') . ' ' . $request->getBaseURL() . '!';
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: Admin <' . $settings->getContactFromEmail() . '>' . "\r\n";
                    mail($user->getEmail(),$subject,$text,$headers);

                    $session = new Session();
                    $session->set('userId', $user->getId());

                    //$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    //$this->get('security.token_storage')->setToken($token);

                    //$request->set('getUser', $user);

                    if($user->getGender()->getId() == 1 and !$user->getIsActivated()){
                        $res = array(
                            'status' => 'not_activated',
                            'phone' => array(
                                'label' => $this->get('translator')->trans('* Phone'),
                                'name' => 'phone',
                                'type' => 'text',
                            ),
                            'submit' => $this->get('translator')->trans('Send Me SMS'),
                            'description' => $this->get('translator')->trans('Activation code will be sent by SMS'),
                        );
                    }elseif($user->getGender()->getId() == 2){
                        $res = array(
                            'status' => 'no_photo',
                            'photos' => array(
                                array(
                                    'face' => $user->getNoPhoto(),
                                    'url' => $user->getNoPhoto()
                                )
                            ),
                            'photo' => $user->getNoPhoto(),
                            'texts' => array(
                               'approved' => $this->get('translator')->trans('Approved'),
                               'status' => $this->get('translator')->trans('Status'),
                               'delete' => $this->get('translator')->trans('Delete'),
                               'cancel' => $this->get('translator')->trans('Cancel'),
                               'waiting_for_approval' => $this->get('translator')->trans('Waiting for approval'),
                               'set_as_main_photo' => $this->get('translator')->trans('Set as Main Photo'),
                               'add_photo' => $this->get('translator')->trans('Add Photo'),
                               'choose_from_camera' => $this->get('translator')->trans('Choose from Camera'),
                               'choose_from_gallery' => $this->get('translator')->trans('Choose from Gallery'),
                               'register_end_button' => $this->get('translator')->trans('Finish'),
                                /*'description' => array(
                                    'text1' => $this->get('translator')->trans('Adding a photo to your profile boosts your chances of meeting new people times 20!<br>We strongly encourage you post a number of them.<br>*New photos awaiting admin’s approval.<br><br>Please notice these guidelines:<br><ul><li>Photos must be of yourself, and you must be recognizable in the photo.</li><li>Photos must not contain nudity or sexual content.</li></ul><br><br>For more information check out our'),
                                    'text2' => '/open_api/pages/6',
                                    'text3' => $this->get('translator')->trans('Photos Policy'),
                                    'text4' => $this->get('translator')->trans('page.<br>Having trouble uploading a photo? Send the photo to info@nyrichdate.com along with your nickname or the email you registered with, and we\'ll upload it to your profile.<br>'),
                                )*/
                                'description' => $this->get('translator')->trans('
                                    Adding a photo to your profile boosts your chances of meeting new people times 20!<br>
                                    We strongly encourage you post a number of them.<br>
                                    *New photos awaiting admin’s approval.<br>
                                    <br>
                                    Please notice these guidelines:
                                    <ul>
                                        <li>Photos must be of yourself, and you must be recognizable in the photo.</li>
                                        <li>Photos must not contain nudity or sexual content.</li>
                                     </ul><br>
                                     For more information check out our <a click="/open_api/pages/6">Photos Policy</a> page.<br>
                                     Having trouble uploading a photo? Send the photo to <a href="mailto:info@nyrichdate.com" >info@nyrichdate.com</a> along with your nickname or the email you registered with, and we\'ll upload it to your profile .<br >
                                '),
                            )
                        );
                    }
                    $res['id'] = $user->getId();
                    return $this->view($res, Response::HTTP_OK);
                }
            }


        }


        return $this->view(array('user' => array(
            'form' => $this->transformForm($form, $flow->getFormData()),
            //'flow' => $flow->getFormData(),
            'errors' => $form->getErrors(),
        )), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   description = "helper for sign up",
     *   parameters={
     *      {"name"="form", "dataType"="string", "required"=false, "description"="Parametrs"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function postSignUpHelperAction(Request $request)
    {
        if($request->request->get('sign_up_one', false)){
            $user = new User();
            $form = $this->createForm(SignUpOneType::class, $user);
            $form->handleRequest($request);

            return $this->view(array(
                'form' => $this->transformForm($form),
            ), Response::HTTP_OK);
        }
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Save Locations",
     *   parameters = {
     *  },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postLocationSaveAction(Request $request){
        $id = $request->get('id', null);
        $latitude = $request->get('latitude', null);
        $longitude = $request->get('longitude', null);

        if($latitude != null and $longitude != null and $id != null) {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
            if($user) {
                $user->setLatitude($latitude);
                $user->setLongitude($longitude);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        return $this->view(1, Response::HTTP_OK);
    }



    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Save Browser Hash",
     *   parameters = {
     *  },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postBrowserHashAction(Request $request){
        $id = $request->get('id', null);
        $token = $request->get('hash', null);
        $result = 0;
        if($token == 'false'){
            $heads = $request->headers->all();
            $str = $heads['accept-language'][0] . $heads['user-agent'][0] . $heads['x-forwarded-for'][0];
            //var_dump(md5($str));//$request->headers->get('User-Agent');
            $token = md5($str);
        }
        if($token != null and $id != null and !empty($token) and $token != 'false') {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
            if($user) {
                $messenger = $this->get('messenger');
                $result = array('success' => $messenger->setUserDevice('hash', $token, $id));
            }
        }
        return $this->view($result, Response::HTTP_OK);
    }



    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get User by Browser Hash",
     *   parameters = {
     *  },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getFindUserHashAction(Request $request){

        $hash = $request->get('hash', null);
        $messenger = $this->get('messenger');
        $route = $request->get('route', false);

        $result = false;
        $id = $messenger->getUserByHash($hash);
        if($id) {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
            $result = array(
                'user' => array(
                    'id' => $user->getId(),
                )
            );
            if($route == 'sign_up' and $user->getGender()->getId() == 1){
                //login user
                if($user->getIsActive()) {
                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                }
                $result['href'] = $this->generateUrl('user_homepage');
                $result['message'] = "You can register only once";
            }
        }
        return $this->view($result, Response::HTTP_OK);
    }



    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Save Browser Token",
     *   parameters = {
     *  },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postBrowserTokenSaveAction(Request $request){
        $id = $request->get('id', null);
        $token = $request->get('token', null);
        $result = 0;
        if($token != null and $id != null) {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
            if($user) {
                $messenger = $this->get('messenger');
                $result = array('success' => $messenger->setUserDevice('browser', $token, $id));
            }
        }
        return $this->view($result, Response::HTTP_OK);
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
                    if(isset($array['zipCode'])) {
                        $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($array['zipCode']));
                    }
                }
            }
        }
    }

    public function setZipCode($request, $user)
    {
        $post  = $request->request->get('sign_up_one');
        if(isset($post['zipCode']) and (int)$post['zipCode'] > 0) {
            $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($post['zipCode']));
            if(!is_object($user->getZipCode())){
                return false;
            }
            return true;
        }
        //return false;
        else{
            $this->setUserZipCodeFromSession($user);
        }
    }

    /**
     * @ApiDoc(
     *  description="Send contact form",
     *  input="AppBundle\Form\Type\ContactType",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function postContactAction(Request $request)
    {
        $sent = false;
        $user = new User();

        $form = $this->createForm(ContactType::class, $user);

        if($request->isMethod('Post')){
            $form->handleRequest($request);
            if($form->isValid() && $form->isSubmitted()){
                $subject = $request->getHost() . " | APP | Contact Form | " . date('d/m/Y H:i') . " | " . $form->get('subject')->getData();
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ' . $form->getData()->getEmail() . ' <' . $form->getData()->getEmail() . '>' . "\r\n";
                $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);
                mail($settings->getContactEmail(),$subject,$form->get('text')->getData(),$headers);
                $sent = true;
                $success = $this->get('translator')->trans('Thanks! the message was sent successfully.');
            }
        }
        $res = array(
            //'form' => $this->transformForm($form),
        	//'mail' => $settings->getContactEmail(),
            'errors' => $form->getErrors(),
            'send' => $sent,
        );
        if($sent){
            $res['success'] = $success;
        }

        return $this->view($res, Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  description="Send contact form",
     *  input="AppBundle\Form\Type\ContactType",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function getContactTestAction()
    {
    	return $this->view(mail('info@nycrichdate.com‬','test','test'), Response::HTTP_OK);
    }
    
    
    /**
     * @ApiDoc(
     *  description="Get contact form",
     *  output="AppBundle\Form\Type\ContactType",
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=false, "description"="String for translate"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function getContactAction(Request $request)
    {
    	$id = $request->get('id');
    	
    	$user = (int)$id == 0 ? $user = new User() : $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);

        $form = $this->createForm(ContactType::class, $user);

        return $this->view(array(
            'form' => $this->transformForm($form),
        	'userEmail'  => $user->getEmail(),
        ), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Login User",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Bad Request.",
     *     403 = "Returned when bad credentials were sent."
     *   }
     * )
     */

    public function postLoginAction(Request $request){
        $headers = $request->headers->all();
        $username = $headers['username'];
        $password = $headers['password'][0];

        $em = $this->getDoctrine()->getManager();
        $user = false;
        $userCheck = $em->getRepository('AppBundle:User')->loadUserByUsernameApi($username);



        if($userCheck){

            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($userCheck);
            //$encoded_pass = $encoder->encodePassword($password, $userCheck->getSalt());
            $validOldPassword = $encoder->isPasswordValid(
                $userCheck->getPassword(), // the encoded password
                $password,  // the submitted password
                null
            );


            if($validOldPassword){
                $user = $userCheck;
            }
        }

        if($user) {
            //$user = $this->get('security.token_storage')->getToken()->getUser();
            $user->setIp($_SERVER['REMOTE_ADDR']);
            $user->setIsFrozen(0);
            $user->setLastloginAt(new \DateTime());
            $loginFromRepo = $em->getRepository('AppBundle:LoginFrom');
            $mobileDetector = $this->get('mobile_detect.mobile_detector');
            if($mobileDetector->isAndroidOS()){
                $user->setLoginFrom($loginFromRepo->find(6));
            }
            if($mobileDetector->isIOS()){
                $user->setLoginFrom($loginFromRepo->find(5));
            }
            $em->persist($user);
            $em->flush();
            $status = $this->getUserStatus($user);
            return $this->view(array(
                'status' => $status,
                'isPay' => ($user->getGender()->getId() == 1 && $em->getRepository('AppBundle:Settings')->find(1)->getIsCharge()) ? (($user->getId() == 455) ? false : $user->isPaying()) : true,
                'photo' => (is_object($user->getMainPhoto())) ? $user->getMainPhoto()->getFaceWebPath() : $user->getNoPhoto(),
                'id' => $user->getId(),
                'texts' => array(
                    'photoMessage' => $this->get('translator')->trans('You need to upload at least one photo'),
                )
            ), Response::HTTP_OK);
        }else{
            return $this->view(array(
                'msg' => 'Error'
            ), Response::HTTP_FORBIDDEN);
        }
    }

    public function getUserStatus($user){

        if($user->getGender()->getId() == 1 && !$user->getIsActivated()) {
            $status = 'not_activated';
        }elseif($user->getGender()->getId() == 2 && !$user->hasPhotos()){
            $status = 'no_photo';
        }elseif($user->getGender()->getId() == 1 && !$user->isPaying() && $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1)->getIsCharge()){ /* && !$user->is2D() */
            $status = 'to_pay';
        }else{
            $status = 'login';
        }
        return $status;
    }

    /**
     * @ApiDoc(
     *  description="Send contact form",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function getLoginAction()
    {
        $this->get('translator')->trans('Hello World');
        return $this->view(array(
            'login' => array(
                'username' => array(
                    'label' => $this->get('translator')->trans('* Username'),
                    'type' => 'text',
                    'value' => '',
                ),
                'password' => array(
                    'label' => $this->get('translator')->trans('* Password'),
                    'type' => 'password',
                    'value' => '',
                ),
                'forgot_password' => $this->get('translator')->trans('Forgot Password'),
                'join_free' => $this->get('translator')->trans('Join Free'),
                'submit' => $this->get('translator')->trans('Login'),
            ),
            'errors' => array(
                'bad_credentials' => $this->get('translator')->trans('Incorrect username or password'),
                'account_is_disabled' => $this->get('translator')->trans('Account has been blocked by the Administrator')
            )
        ), Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  description="Get translate string. If translate string is empty then to get all translations strings",
     *  parameters={
     *      {"name"="string", "dataType"="array", "required"=false, "description"="String for translate"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function getTranslationsAction(Request $request){
        $string = $request->get('string',false);
        if(!$string) {
            $catalogue = $this->get('translator')->getCatalogue($request->getLocale());
            $messages = $catalogue->all();
            while ($catalogue = $catalogue->getFallbackCatalogue()) {
                $messages = array_replace_recursive($catalogue->all(), $messages);
            }
        }else{

            if(is_array($string)){
                foreach ($string as $str){
                    $messages[$str] = /** @Ignore */ $this->get('translator')->trans($str);
                }
            }else {
                $messages[$string] = /** @Ignore */ $this->get('translator')->trans($string);
            }
        }
        return $this->view($messages, Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  description="Send recovery password by email",
     *  parameters = {
     *     {"name"="form[email]", "dataType"="string", "required"=true, "description"="Email"},
     *     {"name"="form[_token]", "dataType"="string", "required"=true, "description"="token"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function postPasswordAction(Request $request)
    {
        return $this->view($this->passwordForm($request), Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  description="Get Password recovery form",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     *
     */
    public function getPasswordAction(Request $request){
        return $this->view($this->passwordForm($request), Response::HTTP_OK);
    }

    public function passwordForm($request){
        $success = false;
        $res = array();

        $form = $this->createFormBuilder()
            ->add('email', 'text', array(
                'label' => $this->get('translator')->trans('Email'),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(array(
                        'checkMX' => true,
                    ))
                )
            ))
            ->getForm();

        if($request->isMethod('POST')){
            $form->submit($request->request->get($form->getName()));
            //$form->handleRequest($request);
            //var_dump($form->isValid());die;
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

                    $subject = $request->getHost() . ' | ' . $this->get('translator')->trans('Reset Password') .' | ' . date('m/d/Y H:i:s');

                    $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);
                    $text = '
                        <div>' .
                            $this->get('translator')->trans('Hello') . ' ' .
                            $user->getUsername() . ',<br />' .
                            $this->get('translator')->trans('Your new password is:') .
                            '<input type="text" onfocus="this.select();" onmouseup="return false;" value="' . $pass . '"/>
                            <br><br>' .
                            $this->get('translator')->trans('For questions or concerns will be happy to be of service at:') . ' ' . $settings->getContactEmail() .
                            '<br><br><br>' .
                            $this->get('translator')->trans('Yours, <br> Team NYRichDate.com') .
                            '<br>' .
                            $request->getBaseURL() . '
                        </div>
                    ';

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: Admin <' . $settings->getContactFromEmail() . '>' . "\r\n";
                    mail($user->getEmail(),$subject,$text,$headers);
                    $success = true;
                    $res['success'] = $this->get('translator')->trans('New password was sent to the email you entered.');
                }else{
                    $form->get('email')->addError(new FormError($this->get('translator')->trans("Email doesn't exist")));
                }
            }
            $res['errors'] = $form->getErrors();
        }

        $res['form'] = $this->transformForm($form);
        $res['send'] = $success;

        return $res;
    }

    public function transformForm($form, $userData = false){
        $notShow = array('fields' => array(), 'values' => array());
        if($userData) {
            if(is_object($userData->getGender()) and $userData->getGender()->getId() == 2){
                $notShow = array('fields' => array('status','netWorth','income'), 'values' => array('features' => array(3)));
            }
        }
        $formArr = array();
        foreach ($form->createView()->children as $key => $field) {
            if(!in_array($key, (array)$notShow['fields'])) {
                if ($field->vars['block_prefixes'][count($field->vars['block_prefixes']) - 2] == 'repeated') {
                    foreach ($field as $key2 => $chield) {
                        $formArr[$key][$key2] = array(
                            'name' => $chield->vars['full_name'],
                            /** @Ignore */
                            'label' => $this->get('translator')->trans($chield->vars['label']),
                            'type' => $chield->vars['block_prefixes'][count($chield->vars['block_prefixes']) - 2],
                            'value' => $chield->vars['value'],
                        );
                    }
                } elseif (in_array($field->vars['block_prefixes'][count($field->vars['block_prefixes']) - 2], array('entity','choice'))) {
                    $choices = array();
                    foreach ($field->vars['choices'] as $chield) {
                        $arr = (isset($notShow['values'][$key])) ? $notShow['values'][$key] : array();
                        if(!in_array($chield->value, $arr)) {
                            $choices[] = array(
                                'value' => $chield->value,
                                'label' => $chield->label,
                            );
                        }
                    }
                    $formArr[$key] = array(
                        'name' => $field->vars['full_name'],
                        /** @Ignore */
                        'label' => $this->get('translator')->trans($field->vars['label']),
                        'type' => $field->vars['block_prefixes'][count($field->vars['block_prefixes']) - 2],
                        'value' => $field->vars['value'],
                        'choices' => $choices,
                    );
                } else {
                    $formArr[$key] = array(
                        'name' => $field->vars['full_name'],
                        /** @Ignore */
                        'label' => (
                            ($key == 'agree') ? '' : /** @Ignore */ $this->get('translator')->trans($field->vars['label'])
                        ),

                        'type' => $field->vars['block_prefixes'][count($field->vars['block_prefixes']) - 2],
                        'value' => $field->vars['value'],
                    );
                    if($key == 'agree') {
                        $formArr[$key]['text1'] = $this->get('translator')->trans('I confirm that I have read and agreed to the');
                        $formArr[$key]['text2'] = '/open_api/pages/4';
                        $formArr[$key]['text3'] = $this->get('translator')->trans('Terms and Conditions');
                        $formArr[$key]['text4'] = $this->get('translator')->trans('of Service of membership at NYSugarDdaddy.com.');
                    }
                }
            }
        }

        $formArr['submit'] = $this->get('translator')->trans('Send');

        return $formArr;
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Menu",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getMenuAction(){
        $menu = array(
            'Online',
            'The Arena',
            'Notifications',
            'Inbox',
            'Favorited',
            'Favorited Me',
            'Contact Us',
            'Search',
            'Contacts',
            'Viewed',
            'Viewed Me',
            'Contacted',
            'Contacted Me',
            'Blocked',
            'Edit Profile',
            'Edit Photos',
            'View my profile',
            'Change Password',
            'Freeze Account',
            'Settings',
            'Log Out',
            'Back',
            'Forgot Password',
            'Login',
            'Join Free',
            'Subscription'
        );
        $menu = $this->transformStringArray($menu);
        $menu['stats'] = $this->get('translator')->trans('Stats');
        return $this->view(array(
            'menu' => $menu
        ), Response::HTTP_OK);
    }

    public function transformStringArray($array){
        $newArray = array();
        foreach ($array as $key => $string){
            $newKey = (is_int($key)) ? $string : $key;
            $newKey = strtolower(str_replace(array('.',',',':',';','!','?',' '), array('','','','','','','_'), $newKey));
            /** @Ignore */
            $newStr = (is_int($key)) ? /** @Ignore */$this->get('translator')->trans($string) : $string;
            $newArray[$newKey] = $newStr;
        }
        return $newArray;
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get app banner",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function getBannerAction(Request $request){
        $res = array(
            'src' => '',
            'link' => ''
        );
        return $this->view($res, Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get app version",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function getVersionAction(Request $request){
        $version = '0.0.1';
        //var_dump('1232');die;
        $mobileDetector = $this->get('mobile_detect.mobile_detector');
        if($mobileDetector->isAndroidOS()){
            $version = '1.0.2';
            if($request->get('app') == 'new'){
                $version = array(
                    'version' => 3,
                    'title' => 'You Have a New Version Available',
                    'message' => 'Visit Play Store and update now!',
                    'cancel' => 'Later',
                    'update' => 'Update',
                    'url' => 'market://details?id=com.nyrd',
                    'mustUpdate' => '0'
                );
            }
        }
        if($mobileDetector->isIOS()){
            $version = '1.0.0';
        }
        return $this->view($version, Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Send sms api",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public function postSmsAction(Request $request){
        $pass = $request->headers->get('com');
        $res = false;
        if($pass === 'interdate') {

            //$message = new \DocDocDoc\NexmoBundle\Message\Simple("RichDate", "972546397683", "Test message 334455");
            //$res = $this->container->get('doc_doc_doc_nexmo')->send($message);
            $from = $request->request->get('from', false);
            $to = $request->request->get('to', false);
            $text = $request->request->get('text', false);

            if (!empty($from) and !empty($to) and !empty($text)) {
                $message = new \DocDocDoc\NexmoBundle\Message\Simple($from, $to, $text);
                //var_dump($res);die;
                $res = $this->container->get('doc_doc_doc_nexmo')->send($message);

            }
        }
        return $this->view($res, Response::HTTP_OK);
    }

}