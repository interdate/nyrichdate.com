<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\LikeMe;
use AppBundle\Entity\User;
use AppBundle\Entity\Photo;

use AppBundle\Form\Type\AdvancedSearchType;
use AppBundle\Form\Type\ChangePasswordType;
use AppBundle\Form\Type\ProfileOneType;
use AppBundle\Form\Type\ProfileTwoType;
use AppBundle\Form\Type\ProfileThreeType;
use AppBundle\Form\Type\QuickSearchSidebarType;
use AppBundle\Form\Type\QuickSearchType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user_homepage")
     */
    public function indexAction(Request $request)
    {
        $this->setUserLoginFrom();
        $ip = $request->getClientIp();
        if($ip == null or $ip == 'unknown'){
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->getUser()->setIp($ip);
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();

        $user = new User();
        $form = $this->createForm(QuickSearchType::class, $user);
        $usersRepo = $this->getDoctrine()->getRepository('AppBundle:User');
        $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);

        $advancedSearch = $request->get('advanced_search');
        $data['filter'] = $advancedSearch['filter'];
        //var_dump($request->get('pageNum', 1));die();
        $users = $usersRepo->getNew(
            array(
                'considered_as_new_days_number' => $settings->getUserConsideredAsNewAfterDaysNumber(),
                'paginator' => $this->get('knp_paginator'),
                'page' => $request->get('pageNum', 1),
                'data' => $data,
                'per_page' => $settings->getUsersPerPage(),
                'current_user' => $this->getUser(),
            )
        );

        if($request->isXmlHttpRequest()){
            $template = $request->request->get('is_mobile') == 0 ? 'users_list' : 'users_list_mobile';
            return $this->render('frontend/user/' . $template . '.html.twig', array(
                'users' => $users,
            ));
        }
        /*
        foreach ($users as $user) {
            if($user->getZipCode() == null){
                var_dump($user->getId());

            }
            die;
        }
        */
        return $this->render('frontend/user/index.html.twig', array(
            'form' => $form->createView(),
            'newUsers' => $users,
            'data' => $data,//$usersRepo->getData(),
        ));
    }

    /**
     * @Route("/user/settings", defaults={"tab" = 1}, name="user_settings")
     */
    public function settingsAction(Request $request){
        $user = $this->getUser();
        if($request->isMethod('POST')){
            $post = $request->request->all();
            //var_dump($post);die;
            $is_sent_email = ($post['is_sent_email'] == 'on');
            $is_sent_push = ($post['is_sent_push'] == 'on');
            $user->setIsSentEmail($is_sent_email);
            $user->setIsSentPush($is_sent_push);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('frontend/user/settings.html.twig', array());
    }

    /**
     * @Route("/user/profile/{tab}", defaults={"tab" = 1}, name="user_profile")
     */
    public function profileAction(Request $request, $tab)
    {
        if($tab == 4){
            //$this->setUpCloudinary();
            //$renderedCloudForm = cl_image_upload_tag('image_id');

            return $this->render('frontend/user/profile/index.html.twig', array(
                'tab' => $tab,
                'page_policy' => $this->getDoctrine()->getManager()->getRepository('AppBundle:Page')->find(6), //Photo Policy
                //'renderedCloudForm' => $renderedCloudForm,
                //'photos' => $this->getUser()->getPhotos(),
            ));
        }

        $user = $this->getUser();

        $form = $this->createForm($this->getProfileType($tab), $user, array(
            'is_male' => $user->getGender()->getId() == 1
        ));

        if($tab == 1){
            $form->remove('phone');
        }

        $errors = false;

        if($request->isMethod('POST')){
            $post = $request->request->all();
            $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
            if($tab == 1) {
                $user->setZipCode($this->getDoctrine()->getRepository('AppBundle:ZipCode')->find($post['profile_one']['zipCode']));
                if($user->getGender()->getId() == 1){
                    $malePhone = $user->getPhone();
                    $post['profile_one']['phone'] = $malePhone;
                    //$request->request->set('profile_one', $post['profile_one']);
                    //$post = $request->request->all();
                }
                $postKey = 'profile_one';
            }elseif ($tab == 2){
                $postKey = 'profile_two';
            }elseif ($tab == 3) {
                $postKey = 'profile_three';
            }
            $post[$postKey] = $userRepo->removeWordsBlocked($post[$postKey], array('username','occupation','about','looking'));
            $request->request->set($postKey, $post[$postKey]);


            $form->handleRequest($request);

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
            if(!$errors and $form->isSubmitted() and $form->isValid()){
                if(isset($malePhone)){
                    //$form->get('phone')->setData($malePhone);
                    $user->setPhone($malePhone);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                //var_dump($user->getPhone());die;
            }
            else{
                $errors = true;
            }
        }

        return $this->render('frontend/user/profile/index.html.twig', array(
            'tab' => $tab,
            'form' => $form->createView(),
            'errors' => $errors,
        ));
    }

    /**
     * @Route("/user/profile_helper", name="user_profile_helper")
     */
    public function profileHelperAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $user = $this->getUser();
            $form = $this->createForm(ProfileOneType::class, $user);
            $form->handleRequest($request);

            return $this->render('frontend/user/profile/1.html.twig', array(
                'form' => $form->createView(),
                'errors' => false,
            ));
        }
    }

    /**
     * @Route("/user/photo", name="user_photo", defaults={"id" = null})
     * @Route("/admin/users/user/{id}/photos/photo", name="admin_users_user_photos_photo")
     */
    public function photoAction(Request $request, $id)
    {
        $uploadedPhoto = $request->files->get('photo');
        /*
        if(!empty($request->get('photo1'))){
            $data = base64_decode($request->get('photo1'));
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/media/photos/' . $this->getUser()->getId() . "/photos/temp.png", $data);
        }
       */
        if(!$uploadedPhoto instanceof UploadedFile) {
            return new Response();
        }

        if($request->get('_route') == 'admin_users_user_photos_photo'){
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
            $isValid = true;
        }
        else{
            $user = $this->getUser();
            $isValid = false;
        }

        $isMain = !$user->isMainPhoto();//$request->request->get('mainPhotoAlreadyExists') == 1 ? false : true;

        $photo = new Photo();
        $photo->setUser($user);
        $fileUrl = $request->get('file_url', false);
        if(!empty($fileUrl)){
            $data = base64_decode($fileUrl);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/media/' . $uploadedPhoto->getClientOriginalName();
            $physicalfile = fopen( $filePath,"w");
            fwrite($physicalfile, $data);
            fclose($physicalfile);
            $file = new UploadedFile($filePath, $uploadedPhoto->getClientOriginalName(), $uploadedPhoto->getClientMimeType(),null,null,true);
            $photo->setFile($file);
        }else {
            $photo->setFile($uploadedPhoto);
        }
        $photo->setIsValid($isValid);
        $photo->setIsMain($isMain);

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo);
        $em->flush();

        $params = $photo->detectFace($request->getHost());

        //var_dump($params);die;
        if(null !== $params) {
            $face = $this->getFace($photo, $params);
            $this->savePhoto($face, $photo->getFaceAbsolutePath());

            $optimized = $this->applyFilterToPhoto('optimize_face', $photo->getFaceWebPath());
            $this->savePhoto($optimized, $photo->getFaceAbsolutePath());
        }

        $optimized = $this->applyFilterToPhoto('optimize_original', $photo->getWebPath());
        $this->savePhoto($optimized, $photo->getAbsolutePath());
        //var_dump($photo->getId());die;
        $response = new Response(json_encode($photo->getId()), Response::HTTP_OK, array('content-type' => 'application/json'));
        //$response->headers->set('Content-Type', 'application/json');

        return $response;
        //return new Response();
    }

    /**
     * Apply filter to photo using the LiipImagineBundle
     *
     * @param Photo $photo an Entity that represents an image in the database
     * @param string $filterName the Imagine filter to use
     */
    private function applyFilterToPhoto($filterName, $webPath)
    {
        $dataManager = $this->container->get('liip_imagine.data.manager');
        $image = $dataManager->find($filterName, $webPath);
        return $this->container->get('liip_imagine.filter.manager')->applyFilter($image, $filterName)->getContent();
    }

    public function savePhoto($photo, $path)
    {
        $f = fopen($path, 'w');
        fwrite($f, $photo);
        fclose($f);
    }

    private function getFace(Photo $photo, $params)
    {
        $dataManager = $this->container->get('liip_imagine.data.manager');
        $image = $dataManager->find('optimize_original', $photo->getWebPath());
        return $this->container->get('liip_imagine.filter.manager')
            ->applyFilter($image, 'face', array(
                'filters' => array(
                    'crop' => array(
                        'start' => array($params['x'], $params['y']),
                        'size' => array($params['w'], $params['h'])
                    )
                )
            ))->getContent();
    }

    /**
     * @Route("/user/photo/delete/{id}", name="user_photo_delete")
     */
    public function deletePhotoAction(Photo $photo)
    {

        $user = $this->getUser();

        if($user->getId() != $photo->getUser()->getId()){
            throw $this->createAccessDeniedException();
        }

        $wasMainPhoto = $photo->getIsMain();
        $em = $this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();

        if($wasMainPhoto){
            $photos = $user->getPhotos();
            foreach ($photos as $userPhoto) {
                if ($userPhoto->getIsValid()) {
                    echo $userPhoto->getId();
                    //$userPhoto->setIsMain(true);
                    //$em->persist($userPhoto);
                    //$em->flush();
                    $wasMainPhoto = false;
                    break;
                }
            }
            if($wasMainPhoto and isset($photos[0])){
                echo $photos[0]->getId();
            }

        }

        return $this->render('frontend/security/empty.html.twig');
    }

    /**
     * @Route("/user/photo/main/{id}", name="user_set_main_photo")
     */
    public function setMainPhotoAction(Photo $photo, Request $request)
    {
        $user = $this->getUser();

        if(!$user || $user->getId() != $photo->getUser()->getId()){
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $photos = $user->getPhotos();

        foreach($photos as $userPhoto){
            if($userPhoto->getIsMain()){
                $userPhoto->setIsMain(false);
                $em->persist($userPhoto);
            }
        }

        $photo->setIsMain(true);
        $em->persist($photo);
        $em->flush();

        return $this->render('frontend/security/empty.html.twig');
    }

    /**
     * @Route("/user/base_links", name="user_mobile_base")
     * @Route("/user/interests", name="user_mobile_interests")
     * @Route("/user/my_profile", name="user_mobile_my_profile")
     * @Route("/user/search_", name="user_mobile_search")
     */
    public function userMobileLinksAction()
    {
        return $this->render('frontend/user/mobile_links.html.twig', array());
    }

    /**
     * @Route("/user/like/{id}", name="user_like", defaults={"id" = null})
     */
    public function usersLikeAction($id)
    {
        $firstUser = ((int)$id > 0) ? $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id) : false;
        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->getUsersForLike($this->getUser(), $firstUser);
        $users['url'] = $this->generateUrl('user_profile', array("tab" => 4));
        return new Response(
            json_encode($users)
        );
    }

    /**
     * @Route("/user/like/send/{id}", name="user_send_like")
     */
    public function userSendLikeAction($id)
    {
        $userRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $toUser = $userRepo->find($id);
        $result = $userRepo->sendUserLike($this->getUser(), $toUser);
        return new Response(
            json_encode($result)
        );
    }

    /**
     * @Route("/user/like/bingo/", name="user_like_bingo", defaults={"likeMeId" = 0})
     * @Route("/user/like/bingo/show/{likeMeId}", name="users_like_bingo_show")
     */
    public function userBingoAction($likeMeId)
    {
        if((int)$likeMeId > 0){
            $result = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->setSplashShowBingo($likeMeId, $this->getUser()->getId());
        }else {
            $result = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->getSplashBingo($this->getUser());
        }
        return new Response(
            json_encode($result)
        );
    }

    /**
     * @Route("/user/notifications", name="user_notifications", defaults={"id" = 0})
     * @Route("/user/notification/read/{id}", name="user_notification_read")
     */
    public function userNotificationAction($id)
    {
        if((int)$id == 0) {
            return $this->render('frontend/user/notifications.html.twig', array());
        }else{
            $em = $this->getDoctrine()->getManager();
            $notification = $em->getRepository('AppBundle:UserNotifications')->find($id);
            if($notification){
                $res = 1;
                $notification->setIsRead(true);
                $em->persist($notification);
                $em->flush();
            }else{
                $res = 0;
            }
            return new Response(
                json_encode($res)
            );
        }
    }

    /**
     * @Route("/user/users/online/{page}", defaults={"page" = 1}, name="user_users_online")
     */
    public function usersOnlineAction(Request $request, $page)
    {
        $data = array();
        $advancedSearch = $request->request->get('advanced_search');
        $data['filter'] = $advancedSearch['filter'];
        $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);
        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->getOnline(
            array(
                'current_user' => $this->getUser(),
                'data' => $data,
                'paginator' => $this->get('knp_paginator'),
                'page' => $page,
                'per_page' => $settings->getUsersPerPage(),
                'considered_as_online_minutes_number' => $settings->getUserConsideredAsOnlineAfterLastActivityMinutesNumber(),
            )
        );

        if($request->isXmlHttpRequest()){
            $template = $request->request->get('is_mobile') == 0 ? 'users_list' : 'users_list_mobile';
            return $this->render('frontend/user/' . $template . '.html.twig', array(
                'users' => $users,
            ));
        }

        $user = $this->getUser();
        $newUser = new User();
        $formQuick = ($user) ? $this->createForm(QuickSearchType::class, $newUser) : false;
        //var_dump(123);die;

        return $this->render('frontend/user/users.html.twig', array(
            'users' => $users,
            'data' => $data,
            'header' => 'Online',
            'form' => ($user) ? $formQuick->createView() : $formQuick,
        ));
    }

    /**
     * @Route("/user/search/advanced", name="user_search_advanced")
     */
    public function searchAction()
    {
        $form = $this->createForm(AdvancedSearchType::class, new User());
        return $this->render('frontend/user/advanced_search.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/quick_search_helper", name="quick_search_helper")
     * @Route("/user/quick_search_sidebar_helper", name="quick_search_sidebar_helper")
     * @Route("/user/advanced_search_helper", name="advanced_search_helper")
     */
    public function searchHelperAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            switch ($request->get('_route')){
                case 'quick_search_helper':
                    $searchType = QuickSearchType::class;
                    $options = array();
                    break;
                case 'quick_search_sidebar_helper':
                    $searchType = QuickSearchSidebarType::class;
                    $options = array();
                    break;
                case 'advanced_search_helper':
                    $searchType = AdvancedSearchType::class;
                    // By some reason these fields considered as ArrayCollection and
                    // it throws error when binding this field,
                    // so this field won't be created when ajax.
                    $options = array(
                        'do_not_create_ethnicity' => true,
                        'do_not_create_zodiac' => true,
                    );
                    break;
            }

            $form = $this->createForm($searchType, new User(), $options);
            $form->handleRequest($request);
            return $this->render('frontend/user/search_helper.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/user/search/results/{page}", defaults={"page" = 1}, name="user_search_results")
     * @Route("/public/search/results/{page}", defaults={"page" = 1}, name="public_search_results")
     * @Route("/members/{page}", defaults={"page" = 1}, name="users_public_list")
     */
    public function searchResultsAction(Request $request, $page)
    {
        $quickSearchHomePage = $request->request->get('quick_search_home_page', null);
        $quickSearchSidebar = $request->request->get('quick_search_sidebar', null);
        $quickSearch = $request->request->get('quick_search', null);
        $advancedSearch = $request->request->get('advanced_search', null);

        $data = null !== $quickSearch
            ? $quickSearch
            : $quickSearchSidebar
        ;

        if(null === $data){
            $data = $advancedSearch;
        }

        if(null === $data){
            $data = $quickSearchHomePage;
        }

        $data['current_route'] = $request->get('_route');        
        $usersRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $settings = $this->getDoctrine()->getManager()->getRepository('AppBundle:Settings')->find(1);
        
        $users = $usersRepo->search(array(
            'current_user' => $this->getUser(),
            'data' => $data,
            'paginator' => $this->get('knp_paginator'),
            'page' => $page,
            'per_page' => $settings->getUsersPerPage(),            
        ));
        //$users = $usersRepo->search($this->getUser(), $data, $this->get('knp_paginator'), $page, $perPage);

        if($request->isXmlHttpRequest()){
            $template = !$request->request->get('is_mobile', false) ? 'users_list' : 'users_list_mobile';
            if(!$this->getUser()){
                $template = str_replace('users', 'users_public', $template);
            }
            return $this->render('frontend/user/' . $template . '.html.twig', array(
                'users' => $users,
            ));
        }

        $user = $this->getUser();
        $newUser = new User();
        $formQuick = ($user) ? $this->createForm(QuickSearchType::class, $newUser) : false;
        //var_dump($usersRepo->getData());
        return $this->render('frontend/user/users.html.twig', array(
            'users' => $users,
            'data' => $usersRepo->getData(),
            'header' => 'Search Results',
            'form' => ($user) ? $formQuick->createView() : $formQuick,
        ));
    }

    /**
     * @Route("/user/users/{id}", name="view_user")
     */
    public function viewUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setViews($user->getViews() + 1);
        //var_dump($user->getHeight(true));die;
        $zodiac = $this->getDoctrine()->getManager()->getRepository('AppBundle:Zodiac')->getZodiacByDate($user->getBirthday());
        $user->setZodiac($zodiac);

        $em->persist($user);
        $em->flush();
        $this->createUpdateList('View', $this->getUser(), $user);

        //var_dump($this->getUser()->isAddLike($user));

        return $this->render('frontend/user/user.html.twig', array(
            'user' => $user,
        ));

    }

    /**
     * @Route("members/profile/{id}", name="users_public_list_user")
     */
    public function publicUserAction(User $user)
    {
        return $this->render('frontend/user/public_user.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/user/notes/{id}", name="user_notes")
     */
    public function saveNoteAction(Request $request, User $member)
    {
        $this->createUpdateList(
            'Note',
            $this->getUser(),
            $member,
            array('text' => $request->request->get('text'))
        );

        return new Response();
    }
    
    /**
     * @Route("/user/lists", name="mobile_user_lists")
     */
    public function listsAction()
    {
    	return $this->render('frontend/user/lists.html.twig');
    }
    
    /**
     * @Route("/user/account", name="mobile_user_account")
     */
    public function accountAction()
    {
    	return $this->render('frontend/user/account.html.twig');
    }

    /**
     * @Route("/user/users/favorite/{id}", name="user_users_favorite")
     */
    public function favoriteAction(Request $request, User $member)
    {
        $this->createUpdateList('Favorite', $this->getUser(), $member);
        return new Response();
    }

    /**
     * @Route("/user/users/black_list/{id}", name="user_users_black_list")
     */
    public function blackListAction(Request $request, User $member)
    {
        $this->createUpdateList('BlackList', $this->getUser(), $member);
        return new Response();
    }

    /**
     * @Route("/user/users/favorite/delete/{id}", name="user_users_favorite_delete")
     */
    public function deleteFavoriteAction(Request $request, User $member)
    {
        $this->deleteFromList('Favorite', $this->getUser(), $member);
        return new Response();
    }

    /**
     * @Route("/user/users/black_list/delete/{id}", name="user_users_black_list_delete")
     */
    public function deleteBlackListAction(Request $request, User $member)
    {
        $this->deleteFromList('BlackList', $this->getUser(), $member);
        return new Response();
    }

    /**
     * @Route("/user/list/favorited/{page}", defaults={"page" = 1}, name="user_list_favorited")
     * @Route("/user/manage/list/favorited/{page}", defaults={"page" = 1}, name="user_manage_list_favorited")
     */
    public function favoritedAction($page)
    {
        return $this->getList(array(
            'header' => 'Favorited',
            'inverse_list' => 'favoritedMe',
            'type' => 'owner',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/list/favorited_me/{page}", defaults={"page" = 1}, name="user_list_favorited_me")
     */
    public function favoritedMeAction($page)
    {
        return $this->getList(array(
            'header' => 'Favorited Me',
            'inverse_list' => 'favorited',
            'type' => 'member',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/list/viewed/{page}", defaults={"page" = 1}, name="user_list_viewed")
     */
    public function viewedAction($page)
    {
        return $this->getList(array(
            'header' => 'Viewed',
            'inverse_list' => 'viewedMe',
            'type' => 'owner',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/list/viewed_me/{page}", defaults={"page" = 1}, name="user_list_viewed_me")
     */
    public function viewedMeAction($page)
    {
        return $this->getList(array(
            'header' => 'Viewed Me',
            'inverse_list' => 'viewed',
            'type' => 'member',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/list/connected/{page}", defaults={"page" = 1}, name="user_list_connected")
     */
    public function connectedAction($page)
    {
        return $this->getList(array(
            'header' => 'Contacted',
            'inverse_list' => 'connectedMe',
            'type' => 'owner',
            'page' => $page,
        ));
    }


    /**
     * @Route("/user/list/connectedMe/{page}", defaults={"page" = 1}, name="user_list_connected_me")
     */
    public function connectedMeAction($page)
    {
        return $this->getList(array(
            'header' => 'Contacted Me',
            'inverse_list' => 'connected',
            'type' => 'member',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/list/black_listed/{page}", defaults={"page" = 1}, name="user_list_black_listed")
     * @Route("/user/manage/list/black_listed/{page}", defaults={"page" = 1}, name="user_manage_list_black_listed")
     */
    public function blackListedAction($page)
    {
        return $this->getList(array(
            'header' => 'Blocked',
            'inverse_list' => 'blackListedMe',
            'type' => 'owner',
            'page' => $page,
        ));
    }

    /**
     * @Route("/user/freeze_account", name="user_freeze_account")
     */
    public function freezeAccountAction(Request $request)
    {
        if($request->isMethod('POST')){
            $this->getUser()->setIsFrozen(true);
            $this->getUser()->setFreezeReason($request->request->get('freeze_account_reason', null));
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
            return $this->redirectToRoute('logout');
        }

        return $this->render('frontend/user/freeze.html.twig');
    }

    /**
     * @Route("/user/report/abuse/{id}", name="user_report_abuse")
     */
    public function reportAbuseAction(Request $request, User $member)
    {
        $subject = "NY Sugger daddy | Desktop | Report Abuse | Username: " . $member->getUsername();

        $text = '
			Username: ' . $member->getUsername() . '<br />
			ID: ' . $member->getId() . '<br>
			Text: ' . $request->request->get('text') . '<br /><br />
			From user: ' . $this->getUser()->getUsername() . '(#' . $this->getUser()->getId() . ')
		';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $this->getUser()->getUsername() . ' <' . $this->getUser()->getEmail() . '>' . "\r\n";

        $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);


        //mail($settings->getContactEmail(),$subject,$text,$headers);

        mail($settings->getReportEmail(),$subject,$text,$headers);
        return new Response();
    }

    /**
     * @Route("/user/password", name="user_change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $changed = false;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        if($request->isMethod('POST')){
            $post = $request->request->all();
            //var_dump($post);die;
            $originalEncodedPassword = $user->getPassword();
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);

            $validOldPassword = $encoder->isPasswordValid(
                $originalEncodedPassword, // the encoded password
                $post['change_password']['oldPassword'],  // the submitted password
                null
            );

            if($validOldPassword){
                $form->handleRequest($request);
                if($form->isValid() && $form->isSubmitted()){
                    $encodedPassword = $encoder->encodePassword($user->getPassword(), null);
                    $user->setPassword($encodedPassword);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $changed = true;
                }

            }
            else{
                $form->get('oldPassword')->addError(new FormError('Old password is invalid'));
            }
        }

        return $this->render('frontend/user/password.html.twig', array(
            'form' => $form->createView(),
            'changed' => $changed,
        ));
    }

    public function getList($settings)
    //public function getList($inverse_list, $header, $page, $perPage)
    {
        $settings['current_user'] = $this->getUser();
        $settings['paginator'] = $this->get('knp_paginator');
        $settings['per_page'] = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Settings')
            ->find(1)
            ->getUsersPerPage()
        ;

        $request = Request::createFromGlobals();
        $data = array();
        $advancedSearch = $request->request->get('advanced_search');
        $data['filter'] = $advancedSearch['filter'];
        $settings['request_data'] = $data;

        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->getList($settings);

        if($request->isXmlHttpRequest()){
            $template = $request->request->get('is_mobile') == 0 ? 'users_list' : 'users_list_mobile';
            return $this->render('frontend/user/' . $template . '.html.twig', array(
                'users' => $users,
            ));
        }


        $newUser = new User();
        $formQuick = ($settings['current_user']) ? $this->createForm(QuickSearchType::class, $newUser) : false;
        //var_dump(123);die;

        return $this->render('frontend/user/users.html.twig', array(
            'users' => $users,
            'header' => $settings['header'],
            'data' => $data,
            'form' => ($settings['current_user']) ? $formQuick->createView() : $formQuick,
        ));
    }

    public function getProfileType($tab)
    {

        switch($tab){
            case 1:
                return ProfileOneType::class;
                break;

            case 2:
                return ProfileTwoType::class;
                break;

            case 3:
                return ProfileThreeType::class;
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

    public function createUpdateList($entityName, $owner, $member, $fields = array())
    {
        if($owner->getId() == $member->getId()){
            return;
        }

        $repo = $this->getDoctrine()->getRepository('AppBundle:' . $entityName);
        $entity = $repo->findOneBy(array(
            'owner' => $owner,
            'member' => $member
        ));

        if(null === $entity){
            $className = 'AppBundle\Entity\\' . $entityName;
            $entity = new $className();
            $entity->setOwner($owner);
            $entity->setMember($member);
            if($entityName == 'Favorite' and $member->getIsSentEmail()){
                //send mail favorite
                $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);
                $subject = $owner->getUsername() . ' added you to their Favorites List on NYRichdate';
                $text = $owner->getUsername() . ' added you to their Favorite List!
                    <br /><br />
                    Want to see who? Log in to <a href="https://www.nyrichdate.com/">NYRichdate.com</a> and check your Favorited Me list.
                    <br /><br />
                    Good luck! 
                    <br /><br />
                    Team NYRichdate.com <br />
                    <a href="https://www.nyrichdate.com/">www.nyrichdate.com</a>';


                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ' . $settings->getContactEmail() . ' <' . $settings->getContactEmail() . '>' . "\r\n";

                mail($member->getEmail(),$subject,$text,$headers);
            }
        }
        elseif(count($fields) == 0){
            return;
        }


        foreach($fields as $key => $value){
            $method = 'set' . ucfirst($key);
            $entity->$method($value);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    public function deleteFromList($entityName, $owner, $member)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:' . $entityName);
        $entity = $repo->findOneBy(array(
            'owner' => $owner,
            'member' => $member
        ));

        if(null !== $entity){
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }
    }

    function setUserLoginFrom()
    {
        $loginFromRepo = $this->getDoctrine()->getRepository('AppBundle:LoginFrom');

        if($this->isMobile()){
            if($this->isIOS()){
                $this->getUser()->setLoginFrom($loginFromRepo->find(3));
            }
            elseif($this->isAndroid()){
                $this->getUser()->setLoginFrom($loginFromRepo->find(4));
            }
            else{
                $this->getUser()->setLoginFrom($loginFromRepo->find(2));
            }
        }
        else{
            $this->getUser()->setLoginFrom($loginFromRepo->find(1));
        }
    }

    public function isIOS(){
        return preg_match('/(iphone|ipad|ipaq|ipod)/i', $_SERVER['HTTP_USER_AGENT']);
    }

    public function isAndroid(){
        return preg_match('/(android)/i', $_SERVER['HTTP_USER_AGENT']);
    }

    public function isMobile(){
        return preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']);
    }

}
