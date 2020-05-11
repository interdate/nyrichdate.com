<?php

namespace AppBundle\Controller\Backend;

use AppBundle\Entity\PaymentHistory;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use AppBundle\Form\Type\AdminAdvancedSearchType;
use AppBundle\Form\Type\AdminPropertiesType;
use AppBundle\Form\Type\ProfileOneAdminType;
use AppBundle\Form\Type\ChangePasswordType;
use AppBundle\Form\Type\ProfileOneType;
use AppBundle\Form\Type\ProfileThreeType;
use AppBundle\Form\Type\ProfileTwoType;
//use AppBundle\Form\Type\SubscriptionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UsersController extends Controller
{
    private $filters = array(
        'total' => array(
            'title' => 'All Users',
            'icon' => 'users',
        ),

        'active_and_not_frozen' => array(
            'title' => 'Active',
            'icon' => 'idea',
        ),

        'male' => array(
            'title' => 'Male',
            'icon' => 'male',
        ),

        'female' => array(
            'title' => 'Female',
            'icon' => 'female',
        ),

        'with_photos' => array(
            'title' => 'With Photos',
            'icon' => 'photo',
        ),

        'frozen' => array(
            'title' => 'Frozen',
            'icon' => 'asterisk',
        ),

        'inactive' => array(
            'title' => 'Inactive',
            'icon' => 'ban',
        ),

        'flagged' => array(
            'title' => 'Flagged',
            'icon' => 'flag',
        ),

        'paying' => array(
            'title' => 'Paying',
            'icon' => 'dollar',
        ),

        'search' => array(
            'title' => 'Search Results',
            'icon' => 'search',
        ),
    );


    /**
     * @Route("/admin/users/list/{filter}/{page}", defaults={"page" = 1, "filter" = "total"}, name="admin_users")
     */
    public function indexAction(Request $request, $page, $filter)
    {
        $reportsRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Report');
        $quickSearchSidebar = $request->request->get('admin_quick_search_sidebar', null);
        $advancedSearch = $request->request->get('admin_advanced_search', null);
        if($advancedSearch == null){
            $advancedSearch = $request->request->get('advancedSearch', null);
        }

        $report = null;

        if($filter == 'report'){
            $report = $reportsRepo->find($request->request->get('reportId'));
            $data = json_decode($report->getParams(), true);
        }
        else{
            $data = null !== $quickSearchSidebar
                ? $quickSearchSidebar
                : $advancedSearch
            ;
        }

        $data['filter'] = $filter;
        $usersRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $stat = $usersRepo->getAdminStat();
        $this->bindFiltersWithStat($stat);

        $reports = $reportsRepo->findByIsFlagged(true);
        //$reports = $usersRepo->setCountReport($reports);

        $users = $usersRepo->setAdminMode()->search(
            array(
                'current_user' => $this->getUser(),
                'data' => $data,
                'paginator' => $this->get('knp_paginator'),
                'page' => $page,
                'per_page' => 20,
            )
        );

        $count = 0;
//        $count = $usersRepo->setAdminMode()->search(
//            array(
//                'current_user' => $this->getUser(),
//                'data' => $data,
//                'getCount' => true
//            )
//        );

        $users->setTemplate('backend/pagination.html.twig');

        //var_dump($data);

        return $this->render('backend/users/index.html.twig', array(
            'count' => $count,
            'users' => $users,
            'data' => $data,
            'stat' => $stat,
            'reports' => $reports,
            'current_report' => $report,
            'filters' => $this->filters,
            'current_filter_data' => $this->getFilterData($filter, $report),
        ));
    }


    /**
     * @Route("/admin/ajax/users/list", name="admin_helper_users")
     */
    public function helperIndexAction(Request $request)
    {
        $reportsRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Report');
        $data = json_decode($request->get('data'),true);
        $usersRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

        $reports = $reportsRepo->findByIsFlagged(true);
        $reports = $usersRepo->setCountReport($reports, false);
        //var_dump($data);die;
        //$count = 0;
        $count = $usersRepo->setAdminMode()->search(
            array(
                'current_user' => $this->getUser(),
                'data' => $data,
                'getCount' => true
            )
        );
        return new Response(
            json_encode(array(
                'count' => $count,
                'reports' => $reports
            )),
            Response::HTTP_OK,
            array('content-type' => 'application/json')
        );
    }



    private function bindFiltersWithStat($stat)
    {
        foreach($stat as $key => $val){
            if(isset($this->filters[$key])) {
                $this->filters[$key]['total_number'] = $val;
            }
        }
    }

    private function getFilterData($filter, $report)
    {
        if(null !== $report){
            return array(
                'name' => 'report',
                'title' => $report->getName(),
                'icon' => 'bar chart',
            );
        }

        $filterData = $this->filters[$filter];
        $filterData['name'] = $filter;
        return $filterData;
    }

    /**
     * @Route("/admin/search/advanced", name="admin_search_advanced")
     */
    public function searchAction()
    {
        $form = $this->createForm(AdminAdvancedSearchType::class, new User());
        return $this->render('backend/users/advanced_search.html.twig', array(
            'form' => $form->createView(),
            'zipCodes' => $this->getDoctrine()->getRepository('AppBundle:ZipCode')->findAll(),
        ));
    }

    /**
     * @Route("/admin/search/advanced/helper", name="admin_advanced_search_helper")
     */
    public function searchHelperAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            $regionId = $request->request->get('regionId', null);
            $areaId = $request->request->get('areaId', null);

            if(null !== $regionId){
                $region = $this->getDoctrine()->getRepository('AppBundle:Region')->find($regionId);
                $list = $this->getDoctrine()->getRepository('AppBundle:Area')->getWrappedList($region, $region->getAreas());
            }
            elseif(null !== $areaId){
                $area =  $this->getDoctrine()->getRepository('AppBundle:Area')->find($areaId);
                $list = $this->getDoctrine()->getRepository('AppBundle:ZipCode')->getWrappedList($area, $area->getZipCodes());
            }

            return $this->render('backend/users/search_helper.html.twig', array(
                'list' => $list,
            ));
        }
    }

    /**
     * @Route("/admin/user/{id}/{property}/{value}", name="admin_user_active")
     */
    public function setPropertyAction(User $user, $property, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $setter = 'set' . ucfirst($property);
        $user->$setter($value);
        $em->persist($user);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        //var_dump(123);die;
        return new Response();
    }

    /**
     * @Route("/admin/users/save/ban/reason", name="admin_users_save_ban_reason")
     */
    public function saveBanReasonAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->saveBanReason(
            $request->request->get('users'),
            $request->request->get('reason')
        );

        return new Response();
    }

    /**
     * @Route("/admin/users/edit/profile/{id}/{tab}", defaults={"tab" = 1}, name="admin_users_edit_profile")
     */
    public function editProfileAction(Request $request, User $user, $tab)
    {
        $errors = false;
        $edited_form_view = null;
        $options = array('is_male' => $user->getGender()->getId() == 1);

        $form_1 = $this->createForm(ProfileOneAdminType::class, $user);
        $form_2 = $this->createForm(ProfileTwoType::class, $user, $options);
        $form_3 = $this->createForm(ProfileThreeType::class, $user);
        $form_4 = $this->createForm(AdminPropertiesType::class, $user);
        $form_5 = $this->createForm(ChangePasswordType::class, $user);
        $form_5->remove('oldPassword');


        if($request->isMethod('POST')){

            if($tab == 1){
                $post = $request->request->all();
                $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($post['profile_one_admin']['zipCode']));
            }

            if($tab == 5){
                $edited_form = $this->createForm($this->getProfileType($tab), $user);
                $edited_form->remove('oldPassword');
            }else{
                $edited_form = $this->createForm($this->getProfileType($tab), $user, $options);
            }

            $edited_form->handleRequest($request);

            if($edited_form->has('email')) {
                $emailInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:EmailBlocked')->findByValue($edited_form->get('email')->get('first')->getData());
                if($emailInBlocked) {
                    $edited_form->get('email')->get('first')->addError(new FormError('Email already exists in the system'));
                    $errors = true;
                }
            }
            if($edited_form->has('phone')) {
                $phoneInBlocked = $this->getDoctrine()->getManager()->getRepository('AppBundle:PhoneBlocked')->findByValue($edited_form->get('phone')->getData());

                if($phoneInBlocked) {
                    $edited_form->get('phone')->addError(new FormError('Phone already exists in the system'));
                    $errors = true;
                }
            }

            if($errors == false and $edited_form->isSubmitted() && $edited_form->isValid()){
                $zodiacRepo = $this->getDoctrine()->getRepository('AppBundle:Zodiac');
                $user->setZodiac($zodiacRepo->getZodiacByDate($user->getBirthday()));
                if($tab == 5){
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $encodedPassword = $encoder->encodePassword($user->getPassword(), null);
                    $user->setPassword($encodedPassword);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
            else{
                $errors = true;
            }

            $edited_form_view = $edited_form->createView();

        }

        return $this->render('backend/users/edited_profile.html.twig', array(
            'user' => $user,
            'tab' => $tab,
            'edited_form' => $edited_form_view,
            'form_1' => $form_1->createView(),
            'form_2' => $form_2->createView(),
            'form_3' => $form_3->createView(),
            'form_4' => $form_4->createView(),
            'form_5' => $form_5->createView(),
            'errors' => $errors,
        ));
    }

    /**
     * @Route("/admin/users/edit/profile/{id}/{tab}/profile_helper", defaults={"tab" = 1}, name="admin_user_profile_helper")
     */
    public function profileHelperAction(Request $request, User $user, $tab)
    {
        if($request->isXmlHttpRequest()){
            $form = $this->createForm(ProfileOneAdminType::class, $user);
            $form->handleRequest($request);

            return $this->render('frontend/user/profile/1.html.twig', array(
                'form' => $form->createView(),
                'errors' => false,
            ));
        }
    }

    /**
     * @Route("/admin/users/view/profile/{id}", name="admin_users_view_profile")
     */
    function viewProfileAction(User $user)
    {
        return $this->render('backend/users/profile.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/admin/users/user/{id}/photos", name="admin_users_user_photos")
     */
    function userPhotosAction(User $user)
    {
        return $this->render('backend/users/photos.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/admin/users/user/{id}/photos/photo/data", name="admin_users_user_photos_photo_data")
     */
    public function photoDataAction(User $user, Request $request)
    {
        $name = $request->request->get('name');
        $isMain = $request->request->get('mainPhotoAlreadyExists') == 1 ? false : true;

        $photo = new Photo();
        $photo->setUser($user);
        $photo->setName($name);
        $photo->setIsValid(true);
        $photo->setIsMain($isMain);

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/admin/users/user/photos/{id}/{property}/{value}", name="admin_users_user_photos_property")
     */
    public function setPhotoPropertyAction(Photo $photo, $property, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $setter = 'set' . ucfirst($property);

        if($property == 'isMain'){
            $photos = $photo->getUser()->getPhotos();

            foreach($photos as $userPhoto){
                if($userPhoto->getIsMain()){
                    $userPhoto->setIsMain(false);
                    $em->persist($userPhoto);
                }
            }
        }

        $photo->$setter($value);
        $em->persist($photo);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/admin/users/user/photos/{id}/delete", name="admin_users_user_photo_delete")
     */
    public function deletePhotoAction(Photo $photo)
    {
        $user = $photo->getUser();
        $wasMainPhoto = $photo->getIsMain();
        $em = $this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();

        if($wasMainPhoto){
            $photos = $user->getPhotos();
            if(isset($photos[0])){
                echo $photos[0]->getId();
            }

        }

        return $this->render('frontend/security/empty.html.twig');
    }

    /**
     * @Route("/admin/users/user/{id}/subscription", name="admin_users_user_subscription")
     */
    function userSubscriptionAction(Request $request, User $user){

        $interval = new \DateInterval('P1D');
        if(is_object($user->getEndSubscription()) and $user->getEndSubscription()->getTimestamp() > time()){
            $startDateObj = new \DateTime($user->getStartSubscription()->format('Y-m-d'));
            $startDateObj->add($interval);
            $startDate = $startDateObj->format('Y-m-d');
        }else{
            $startDate = date('Y-m-d');
        }

        $endDate = (is_object($user->getEndSubscription()) and $user->getEndSubscription()->getTimestamp() > time()) ? $user->getEndSubscription()->format('Y-m-d') : date('Y-m-d');

        $form = $this->createFormBuilder()
//            ->add('email', 'text', array(
//                'label' => 'Email:',
//                'constraints' => array(
//                    new Constraints\NotBlank(),
//                    new Constraints\Email(array(
//                        'message' => 'Email {{ value }} is incorrect',
//                        'checkMX' => true,
//                    ))
//                )
//            ))
            ->add('id', HiddenType::class, array(
                'data' => $user->getId()
            ))
            ->add('points', TextType::class, array(
                'label' => 'Points',
                'data' => $user->getPoints(),
            ))
            ->add('startSubscription', TextType::class, array(
                'label' => 'Subscription Start Day',
                'data' => $startDate,
                'required' => false
            ))
            ->add('period', ChoiceType::class, array(
                'label' => 'Subscription Period',
                'multiple' => false,
                'expanded' => false,
                'choices' => array('Choose period' => '0','2 week' => '5', '1 month' => '4','3 month' => '3', '6 month' => '2', '1 year' => '1'),
                'data' => '0',
                'required' => false,
            ))
            ->add('endSubscription', TextType::class, array(
                'label' => 'Subscription End Day',
                'data' => $endDate,
                'required' => false
            ))
            ->add('transactionId', TextType::class, array('label' => 'Transaction ID','required' => false,))
            ->add('recurringId', TextType::class, array('label' => 'Recurring Payment ID','required' => false,))
            ->add('note', TextareaType::class, array('label' => 'Note','required' => false,))
            ->getForm();
        $form->handleRequest($request);
        //$form = $this->createForm(new SubscriptionType($user, $this->getDoctrine()));
        $save = '0';

        if ($request->isMethod('POST') and $user->getGender()->getId() == 1) {
            if ($form->isSubmitted() && $form->isValid()) {
                // validate subscribe
                $data = $form->getData();
                if($data['startSubscription'] == $data['endSubscription']){
                    $form->get('endSubscription')->addError(new FormError('Choose Subscription Period'));
                }
                if(!empty($data['transactionId']) or !empty($data['recurringId'])){
                    if(empty($data['transactionId'])){
                        $form->get('transactionId')->addError(new FormError('Input Transaction ID'));
                    }
                    if(empty($data['recurringId'])){
                        $form->get('recurringId')->addError(new FormError('Input Recurring Payment ID'));
                    }
                }
                if($form->isValid()){
                    $em = $this->getDoctrine()->getManager();
                    $startDate = new \DateTime($data['startSubscription']);
                    if($user->getStartSubscription() != $startDate){
                        $user->setStartSubscription($startDate);
                    }
                    $endDate = new \DateTime($data['endSubscription']);
                    if($user->getEndSubscription() != $endDate){
                        $user->setEndSubscription($endDate);
                    }
                    if($user->getPoints() != $data['points']){
                        $user->setPoints($data['points']);
                    }

                    $historyRepo = $this->getDoctrine()->getRepository('AppBundle:PaymentHistory');
                    if(!empty($data['note']) and (empty($data['transactionId']) or empty($data['recurringId']))){
                        if(empty($data['transactionId'])){
                            $lastHist = $historyRepo->findOneBy(array(),array('id' => 'desc'));
                            $data['transactionId'] = 'admin-' . $user->getId() . '-' . $lastHist->getId();
                        }
                        if(empty($data['recurringId'])){
                            $data['recurringId'] = 'admin-' . $user->getId();
                        }
                    }
                    if(!empty($data['transactionId']) and $historyRepo->findOneBy(array('transactionId' => $data['transactionId'])) == null){
                        $history = new PaymentHistory();
                        $history->addUser($user);
                        $history->setNote($data['note']);
                        $history->setFullData($data);
                        $history->setPaymentDate($startDate);
                        $amount = '0.00';
                        $history->setAmount($amount);

                        $history->setTransactionId($data['transactionId']);
                        $history->setRecurringId($data['recurringId']);
                        $parent = $historyRepo->findOneBy(array('recurringId' => $data['recurringId'], 'parent' => null));
                        if ($parent) {
                            $history->setParent($parent);
                        }
                        $em->persist($history);
                        $em->flush();
                        $user->addPaymentHistory($history);
                    }
                    $em->persist($user);
                    $em->flush();
                    $save = '1';
                }
            }
        }elseif($request->isMethod('POST') and $user->getGender()->getId() == 2){
            $save = '3';
            //$lastHist = $this->getDoctrine()->getRepository('AppBundle:PaymentHistory')->findOneBy(array(),array('id' => 'desc'));
            //var_dump($lastHist->getId());
        }
        if($request->get('remove',0) == 1){
            $em = $this->getDoctrine()->getManager();
            $endDate = new \DateTime(date('Y-m-d H:i:s'));
            $endDate->sub(new \DateInterval('P1D'));
            $user->setEndSubscription($endDate);
            $em->persist($user);
            $em->flush();
            $save = '2';
        }

        return $this->render('backend/users/subscription.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'save' => $save,
        ));
    }

    /**
     * @Route("/admin/users/user/history/edit", name="admin_users_user_history_edit")
     */
    function userPaymentHistoryEditAction(Request $request){
        $id = (int)$request->get('id');
        if($id > 0){
            $em = $this->getDoctrine()->getManager();
            $history = $this->getDoctrine()->getRepository('AppBundle:PaymentHistory')->find($id);
            if($history){
                if($request->get('field') == 'note') {
                    $val = $request->get('value');
                    $history->setNote($val);
                }
                $em->persist($history);
                $em->flush();
            }
        }
        return new Response();
        /*$this->view(array(
            'success' => 1,
        ), Response::HTTP_OK);*/
    }

    /**
     * @Route("/admin/users/photos/waiting", name="admin_users_photos_waiting")
     */
    function waitingPhotosAction()
    {
        return $this->render('backend/users/waiting_photos.html.twig', array(
            'photos' => $this->getDoctrine()->getRepository('AppBundle:Photo')->findByIsValid(false),
        ));
    }

    /**
     * @Route("/admin/users/user/photo/rotate", name="admin_users_user_photo_rotate")
     */
    function photoRotateAction(Request $request){
        $res = '';
        $rotate = (int)$request->get('rotate',0);
        $photo = $this->getDoctrine()->getRepository('AppBundle:Photo')->find($request->get('id',0));
        if($photo and $rotate != 0){
            //var_dump($rotate);
            $res = $photo->rotate($rotate)->getWebPath();
        }
//        if($photo){
//            $res = $photo->getWebPath();
//        }
        return new Response(json_encode(array('url' => $res)), Response::HTTP_OK, array('content-type' => 'application/json'));
    }


    /**
     * @Route("/admin/users/photos/waiting/{id}/approve/{state}", name="admin_users_photos_waiting_approve")
     */
    function approvePhotoAction(Photo $photo, $state)
    {
        $em = $this->getDoctrine()->getManager();

        if($state == 1){
            $photo->setIsValid(true);
            $em->persist($photo);
        }
        else{

            $user = $photo->getUser();
            $wasMainPhoto = $photo->getIsMain();
            $em->remove($photo);

            if($wasMainPhoto){
                $photos = $user->getPhotos();
                if(isset($photos[0])){
                    $photos[0]->setIsMain(true);
                    $em->persist($photos[0]);
                }
            }
        }

        $em->flush();

        return new Response();
    }

    /**
     * @Route("/admin/users/reports/create", name="admin_users_reports_create")
     */
    function createReportAction(Request $request)
    {
        $report = new Report();
        $report->setName($request->request->get('name'))
            ->setIsFlagged($request->request->get('flagged', false))
            ->setParams(json_encode($request->request->get('advancedSearch')))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($report);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/admin/users/reports", name="admin_users_reports")
     */
    function reportsAction()
    {
        return $this->render('backend/users/reports.html.twig', array(
            'reports' => $this->getDoctrine()->getRepository('AppBundle:Report')->findAll(),
        ));
    }

    /**
     * @Route("/admin/users/reports/{id}/show_on_main_page/{state}", name="admin_users_reports_show_on_main_page")
     */
    function showOnMainPageAction(Report $report, $state)
    {
        $em = $this->getDoctrine()->getManager();
        $report->setIsFlagged($state);
        $em->persist($report);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/users/reports/{id}/delete", name="admin_users_reports_delete")
     */
    function deleteReportAction(Report $report)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($report);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/admin/users/export", name="admin_users_export")
     */
    public function exportAction(Request $request)
    {
        $data = $request->request->get('advancedSearch');
        $usersRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $result = $usersRepo->setAdminMode()->setExportMode()->search(
            array(
                'current_user' => $this->getUser(),
                'data' => $data,
            )
        );

        $fileName = $request->request->get('fileName');

        $response = new StreamedResponse();
        $response->setCallback(function () use($fileName, $result) {

            flush();
            $handle = fopen('php://output', 'r+');

            $i = 0;
            foreach ($result as $row) {
                if($i % 20 == 0){
                    fputcsv($handle, array_keys($row));
                }

                $values = array();
                foreach(array_values($row) as $value){
                    if($value instanceof \DateTime){
                        $value = $value->format("Y-m-d H:i:s");
                    }
                    $values[] = $value;
                }

                fputcsv($handle, $values);
                $i++;
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="' . $fileName . '.csv"');
        return $response;
    }

    /**
     * @Route("/admin/users/point/{toAll}", name="admin_users_point")
     */
    public function givePointAction($toAll)
    {
        $this->getDoctrine()->getRepository('AppBundle:User')->givePoint($toAll);
        return new Response();
    }

    /**
     * @Route("/admin/users/login/{id}", name="admin_users_login")
     */
    public function loginAction(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        return $this->redirect($this->generateUrl('user_homepage'));
    }



    public function getProfileType($tab)
    {

        switch($tab){
            case 1:
                return ProfileOneAdminType::class;
                break;

            case 2:
                return ProfileTwoType::class;
                break;

            case 3:
                return ProfileThreeType::class;
                break;

            case 4:
                return AdminPropertiesType::class;
                break;

            case 5:
                return ChangePasswordType::class;
                break;
        }

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
