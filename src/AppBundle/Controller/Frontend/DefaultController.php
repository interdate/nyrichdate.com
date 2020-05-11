<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\FaqCategory;
use AppBundle\Entity\PaymentHistory;
use AppBundle\Entity\User;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\QuickSearchHomePageType;
use AppBundle\Form\Type\ArticleType;
use AppBundle\Form\Type\TranslatableFaqCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            if($this->getUser()->getIsFrozen()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($this->getUser()->setIsFrozen(false)->setFreezeReason(null));
                $em->flush();
            }

            return $this->getUser()->isAdmin()
                ? $this->redirect($this->generateUrl('admin_users'))
                : $this->redirect($this->generateUrl('user_homepage'));

        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(QuickSearchHomePageType::class, new User());

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->onHomepage();

        return $this->render('frontend/index.html.twig', array(
            'last_username'     => $lastUsername,
            'error'             => $error,
            'articles'          => $this->getDoctrine()->getRepository('AppBundle:Article')->getOnHomepage(),
            'homePageBlocks'    => $this->getDoctrine()->getRepository('AppBundle:HomePage')->findAll(),
            'seo'               => $this->getDoctrine()->getRepository('AppBundle:Seo')->findOneByPage('homepage'),
            'slides'            => $this->getDoctrine()->getRepository('AppBundle:Slide')->findByIsActive(true),
            'form'              => $form->createView(),
            'users'             => $users,
        ));
    }

    /**
     * @Route("/form", name="form")
     */
    public function formAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->find(24);
        $article->setTranslatableLocale($request->getLocale());
        $em->refresh($article);

        $form = $this->createForm(ArticleType::class, $article);

        return $this->render('frontend/form.html.twig', array(
            'form'              => $form->createView(),            
        ));
    }

    /**
     * @Route("/trans_form", name="trans_form")
     */
    public function transFormAction(Request $request)
    {
        $faqCategory = new FaqCategory();

        $form = $this->createForm(TranslatableFaqCategoryType::class, $faqCategory);

        return $this->render('frontend/form.html.twig', array(
            'form'              => $form->createView(),
        ));
    }


    /**
     * @Route("/paypal/test", name="test_paypal")
     */
    public function paypalActionsAction(Request $request){

        $contact = $this->getDoctrine()->getRepository('AppBundle:User')->find(455);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find(462);
        $messenger = $this->get('messenger');
        $messenger->pushNotification('You got a new message from ' . $user->getUsername(), $contact->getId(),462);
            die;
        /*$apiKey = "AAAACzlAATY:APA91bGtB5UKvqm6dmdZm4TJEIqdpihpOMbyDGbk0AbkiM8144hg2AvnAddpwkxEEj7wTjiPC65QJTEVoOTxMPIAPpFR2WG2Gn0ecsQYITHOCenIJ_YgS21WofPxrlhwX7zVzBUN3ivxvnPU591lzx75JuewJ3v_7w";
        $token = "c5UcIyVzNUI:APA91bHeUxKqOG9p4jdl0DuUfzy3yspCxEtCZpcMkikClpTsuMKrISiTAyBbBLpPrc5ZOyzQgaUGyTuttHr-hKkaeIZwZAz0_LOY2c83_8vyGfBQwYqKoRqyPjXZAxKNbl1g9xHW1cnO";

        $clickUrl = "https://www.nyrichdate.com/user/messenger/dialog/open/userId:455/contactId:462";
        $fields = array
        (
            'to' 	=> $token,
            "notification" => array(
                "body" => 'You got a new message from ' . $user->getUsername(),
                "title" => 'NY RichDate',//$this->config->gcm->title,
                "icon" => "https://www.nyrichdate.com/images/icon.png",
                'click_action' => htmlspecialchars($clickUrl,ENT_COMPAT),
            ),
            'priority' => 'high',
        );

        $headers = array
        (
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );


        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        $res = json_decode(trim($result), true);
        //var_dump(json_decode(trim($result), true));die;

        var_dump($res);die;*/
        /*$token = $this->getPaypalAccessToken();
        $sale_id = '5EJ71529EU302201L';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/payments/sale/" . $sale_id );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer " . $token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        var_dump($result);die;
        */
//        $subscribeId = 'I-DTVHWGCD1H7M';
//        $action = 'cancel'; //re-activate  suspend  cancel
//        $note = 'Reactivating the profile.';
//        $ch = curl_init();
//        $data = json_encode(array(
//            'note' => $note
//        ));
//        curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/payments/billing-agreements/" . $subscribeId . "/" . $action);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_POST, 1);
//
//        $headers = array();
//        $headers[] = "Content-Type: application/json";
//        $headers[] = "Authorization: Bearer " . $token;
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//        $result = curl_exec($ch);
//        if (curl_errno($ch)) {
//            echo 'Error:' . curl_error($ch);
//        }
//        curl_close ($ch);
//        var_dump($result);die;
        $appLink = 'nyrd://';

        return $this->render('frontend/message.html.twig', array(
            'title' => 'PayPal Test Route',
            'message' => 'test mess',
            'link' => $appLink
        ));
    }



    public function getPaypalAccessToken(){
        $ch = curl_init();
        $client_id = 'ASddX9sYCzG65UxGB8Fi_pYxGxI4o6qMk4GyKhI2wtY365cJwDVJCVxW-nxdP0fU8jW4l0yLI1m4_Yen';
        $secret = 'EOwCO6P1XAPuENhVgRhOnaF48ujw5C8JYVukUpQGo_Gpdtqq3kgieJCzIJ-lp6P4K24TWro3L9tkV4Kj';
        curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $secret);

        $headers = array();
        $headers[] = "Accept: application/json";
        $headers[] = "Accept-Language: en_US";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $data = (array)json_decode($result);
        return $data['access_token'];
        //var_dump($data); die;

        /*
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/billing-agreements/I-1TJ3GAGG82Y9/suspend");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"note\": \"Suspending the profile.\"\n}");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer Access-Token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/billing-agreements/I-1TJ3GAGG82Y9/re-activate");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"note\": \"Reactivating the profile.\"\n}");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer Access-Token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
curl_close ($ch);
         */


    }

    public function paymentGetInfoPaypalAction($req){
        //$request->overrideGlobals();
        $request = $req;
        $data = array();
        $tx = $request->get('tx');
        //var_dump(isset($tx));die;
        if(isset($tx)){

            $data = array(
                'cmd' => '_notify-synch',
                'tx' => $tx,
                'at' => 'EWUNKxy4_-OCGeCd-vHwld_33avFbbH0-eFEiDEymSFvuJw7BZqA_6BN-Zq'
            );
        }else{
            $data = array('cmd' => '_notify-validate');
            foreach ($_REQUEST as $key => $val){
                $data[$key] = $val;
            }
        }
        //var_dump($data);die;
        $url = "https://www.paypal.com/cgi-bin/webscr";
        $ch = curl_init($url);
        # Form data string
        $postString = http_build_query($data, '', '&');
        # Setting our options
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));


        # Get the response
        $res = curl_exec($ch);
        curl_close($ch);

        $tokens = explode("\r\n\r\n", trim($res));

        $res = trim(end($tokens));
        if(strpos($res,'SUCCESS') !== false){
            $sumb = substr($res, 7, 1);
        }elseif(strpos($res,'FAIL') !== false){
            //FAIL
            $sumb = substr($res, 4, 1);
        }elseif(strpos($res,'VERIFIED') !== false){
            //VERIFIED
            $sumb = ' ';//substr($res, 8, 1);
        }elseif (strpos($res,'INVALID') !== false){
            $sumb = ' ';//substr($res, 7, 1);
        }
        //explode($sumb, $res);
        $str = str_replace($sumb,'&',$res);

        if(strpos($res,'VERIFIED') !== false or strpos($res,'INVALID') !== false){
            $request->request->set($res, true);
            return $request;
        }
        //mail('pavel@interdate-ltd.co.il','New Payment',$str);

        //if(strpos($res,'&') !== false){
        parse_str($str, $arr);
        //}
        //var_dump(!is_array($arr) or count($arr) == 0);die;
        if(!is_array($arr) or count($arr) == 0){
            $arr = array($str);
        }

        foreach ($arr as $key => $value){
            $request->request->set($key, $value);
        }
        return $request;
    }


    /**
     * @Route("/payment/app-subscription/{id}", defaults={"id" = 0}, name="payment_app_subscription")
     */
    public function appSubscriptionAction(Request $request, $id){
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find((int)$id);
        $period = (int)$request->get('period',0);
        if($user and $period > 0 and $period < 6){
            $price = '1560.00';
            $per = '1';
            $per2 = 'Y';
            if($period == 2){
                $price = '900.00';
                $per = '6';
                $per2 = 'M';
            }elseif($period == 3){
                $price = '499.00';
                $per = '3';
                $per2 = 'M';
            }elseif($period == 4){
                $price = '99.00';
                $per = '1';
                $per2 = 'M';
            }elseif($period == 5){
                $price = '69.00';
                $per = '2';
                $per2 = 'W';
            }
        }else{
            return $this->redirectToRoute('user_homepage');
        }
        return $this->render('frontend/app-pp.html.twig', array(
            'payUser' => $user,
            'period' => $period,
            'price' => $price,
            'per' => $per,
            'per2' => $per2
        ));
    }


    /**
     * @Route("/payment/subscription", defaults={"id" = 0}, name="payment_subscription")
     * @Route("/payment/subscription/{id}", defaults={"id" = 0}, name="payment_subscription_return")
     * @Route("/payment/subscription-app/{id}", defaults={"id" = 0}, name="payment_subscription_app")
     */
    public function ppSubscriptionAction(Request $request, $id){
        $app = ($request->get('_route') == 'payment_subscription_app');
        $appLink = ($app) ? 'nyrd://' : '';
        $message = 'Your payment was successful.';
        $params = $request->request->all();
        if(count($params) == 0){
            $params = $request->query->all();
        }

        $id = (int)$id;

        //var_dump($params);die;
        $strArr = array();
        foreach($params as $key => $value){
            $strArr[] = $key . '=' . $value;
        }
        $str = implode('&',$strArr);
        //$str = http_build_query($params);
        //mail('pavel@interdate-ltd.co.il','NYRichDate Payment',$str);

        if(!empty($request->get('tx'))){
            $request = $this->paymentGetInfoPaypalAction($request);
            $request->attributes->set('item_number', $strArr['item_number']);
            $request->attributes->set('custom', $strArr['cm']);
        }
        $params = $request->request->all();
        if(count($params) == 0){
            $params = $request->query->all();
        }
        //var_dump($params);die;

        //$strArr = array();
        foreach($params as $key => $value){
            $strArr[] = $key . '=' . $value;
        }
        $str = implode('&',$strArr);
        //$str = http_build_query($params);

        if($id == 461){
            //var_dump($appLink);die;
        }
        mail('pavel@interdate-ltd.co.il','NYRichDate Payment',$str);
        $txn_id = $request->get('txn_id');
        $historyRepo = $this->getDoctrine()->getRepository('AppBundle:PaymentHistory');
        ///payment/subscription?transaction_subject=NYRichDate.com Subscription&payment_date=06:00:13 Dec 26, 2018 PST&txn_type=subscr_payment&subscr_id=I-A58XLRHF54Y4&last_name=Brunicki&residence_country=IL&item_name=NYRichDate.com Subscription&payment_gross=1.00&mc_currency=USD&business=contact@interdate-ltd.co.il&payment_type=instant&protection_eligibility=Eligible&verify_sign=AvjpzUw5bu0ys5XGbviEYfpGE0h6AK2uwTXqZn9ruS9DvuVCg.RkIPZd&payer_status=unverified&payer_email=daniel@interdate-ltd.co.il&txn_id=8MR765553H165305J&receiver_email=contact@interdate-ltd.co.il&first_name=Daniel&payer_id=R6M2U7Q7UV968&receiver_id=NS3QEJSVR755Q&contact_phone=+972 525567959&item_number=455&payment_status=Completed&payment_fee=0.33&mc_fee=0.33&mc_gross=1.00&custom=455_6&charset=windows-1252&notify_version=3.9&ipn_track_id=40374a253928c
        //var_dump($txn_id != null and $historyRepo->findOneBy(array('transactionId' => $txn_id)) == null and ($request->get("payment_status") == "Completed" or $request->get("payment_status") == "Pending"));die;
        if($txn_id != null and $historyRepo->findOneBy(array('transactionId' => $txn_id)) == null and ($request->get("payment_status") == "Completed" or $request->get("payment_status") == "Pending")) {

            $userId = (int)$request->get('item_number');
            $custom = $request->get('custom');
            $arr = explode('_', $custom);
            $subscribeId = (int)$arr[1];
            if($userId == 0){
                $userId = (int)$arr[0];
            }
            if(isset($arr[2]) && $arr[2] == '1'){
                $appLink = 'nyrd://';
            }

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);
            if ($user) {
                /*
                 * $subscribeId:
                 * 1 - one year
                 * 2 - six months
                 * 3 - three months
                 * 4 - one months
                 * 5 - two weeks
                 * 6 - one day
                 */
                //$subscribeId = (int)$request->get('custom');
                //var_dump($subscribeId);die;
                $em = $this->getDoctrine()->getManager();
                if ($subscribeId > 0 and $subscribeId < 7) {
                    $startDate = new \DateTime(date('Y-m-d H:i:s'));
                    //var_dump($subscribeId,$startDate->format('Y-m-d H:i:s'));
                    $user->setStartSubscription($startDate);
                    $interval_spec = 'P1D';
                    switch ($subscribeId) {
                        case 1:
                            $interval_spec = 'P1Y';
                            break;
                        case 2:
                            $interval_spec = 'P6M';
                            break;
                        case 3:
                            $interval_spec = 'P3M';
                            break;
                        case 4:
                            $interval_spec = 'P1M';
                            break;
                        case 5:
                            $interval_spec = 'P14D';
                            break;
                        case 6:
                            $interval_spec = 'P1D';
                            break;
                    }
                    $interval = new \DateInterval($interval_spec);
                    $endDate = new \DateTime(date('Y-m-d H:i:s'));
                    $endDate->add($interval);
                    /*var_dump($subscribeId,$startDate->format('Y-m-d H:i:s'));
                    var_dump($endDate->format('Y-m-d H:i:s'));
                    die;*/
                    $user->setEndSubscription($endDate);
                    if($historyRepo->findOneBy(array('transactionId' => $txn_id)) == null) {
                        $history = new PaymentHistory();
                        $history->addUser($user);
                        $note = '';
                        $history->setNote($note);
                        $history->setFullData($params);
                        $history->setPaymentDate($startDate);
                        $amount = $request->get('mc_gross');
                        $history->setAmount($amount);

                        $history->setTransactionId($txn_id);
                        $subscr_id = $request->get('subscr_id');
                        $history->setRecurringId($subscr_id);
                        $parent = $this->getDoctrine()->getRepository('AppBundle:PaymentHistory')->findOneBy(array('recurringId' => $subscr_id, 'parent' => null));
                        if ($parent) {
                            $history->setParent($parent);
                        }
                        $em->persist($history);
                        $em->flush();
                        $user->addPaymentHistory($history);
                    }

                }

                $em->persist($user);
                $em->flush();
                $message = 'Your payment was successful.';
            } elseif($id > 0) {
                $userId = $id;
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);
                if ($user) {
                    $startDate = new \DateTime(date('Y-m-d H:i:s'));
                    $user->setStartSubscription($startDate);
                    $endDate = new \DateTime(date('Y-m-d H:i:s'));
                    $endDate->add(new \DateInterval('PT10M'));
                    $user->setEndSubscription($endDate);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $message = 'Your payment was successful.';
                }
            }
        }else{
            //return $this->redirectToRoute('user_homepage');
        }
        return $this->render('frontend/message.html.twig', array(
            'title' => 'Subscription',
            'message' => $message,
            'link' => $appLink
        ));
    }


}
