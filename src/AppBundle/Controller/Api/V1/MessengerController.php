<?php

namespace AppBundle\Controller\Api\V1;

use AppBundle\Services\Messenger\Chat;
use AppBundle\Services\Messenger\Dialog;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;

session_write_close();

class MessengerController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Set user device token",
     *   parameters = {
     *     {"name"="phone_id", "dataType"="string", "required"=true, "description"="Device Token Id. "}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postPhonesAction(Request $request){

        $mobileDetector = $this->get('mobile_detect.mobile_detector');
        $mob_os = '';
        if($mobileDetector->isAndroidOS()){
            $mob_os = 'android';
        }
        if($mobileDetector->isIOS()){
            $mob_os = 'ios';
        }
        $token = $request->get('phone_id',false);

        $result = array('success' => false);
        if(($mob_os == 'ios' or $mob_os == 'android') and !empty($token)) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
            $messenger = $this->get('messenger');
            $result = array('success' => $messenger->setUserDevice($mob_os, $token, $userId));
        }
        return $this->view($result, Response::HTTP_OK);

    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get User Inbox",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getInboxAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->view( array(
            'title' => $this->get('translator')->trans('Messages'),
            'dialogs' => $this->getDoctrine()->getRepository('AppBundle:User')->getDialogs($user),
            'texts' => array(
                'no_results' => $this->get('translator')->trans('Inbox is empty'),
                'all' => $this->get('translator')->trans('All Dialogs'),
                'read' => $this->get('translator')->trans('Read Dialogs')
            ),
        ), Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get dialog with contact",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getDialogAction(User $contact)
    {
        $mobileDetector = $this->get('mobile_detect.mobile_detector');
        if($mobileDetector->isIOS()){
            $responseData = array('status' => 403, 'msg' => 'User Not Authenticated');
            $response = new JsonResponse();
            $response->setData($responseData);
            $response->setStatusCode($responseData['status']);
            return $response;
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $contactId = $contact->getId();

        $dialog = new Dialog(array(
            'userId' => $userId,
            'contactId' => $contactId,
        ));

        //$dialog['contact']['image']

        return $this->view( array(
            'dialog' => $dialog,
            'history' => $dialog->getHistory(),
            'texts' => array(
                'title' => $this->get('translator')->trans('Messages'),
                'a_conversation_with' => $this->get('translator')->trans('A conversation with'),
            ),
            //'contact' => $this->getDoctrine()->getRepository('AppBundle:User')->find($contactId),
        ), Response::HTTP_OK);


    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get new messages in active chat",
     *	 parameters = {
     *     {"name"="messages", "dataType"="string", "required"= false, "description"="Array of Messages Id's."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getChatsNewMessagesAction(User $contact, Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $contactId = $contact->getId();
        $options['userId'] = $userId;
        $messenger = $this->get('messenger');
        //$result['newMessages'] = array();
        $messages = $request->get('messages', false);
        
        $options['messages'] = ($messages) ? implode(',',$messages) : '';

        //return $messenger->response($options);
        //if(!count($result['newMessages'])){
            $options['contactId'] = $contactId;
            $result = $messenger->checkDialogNewMessages($options);
        //}

        return $messenger->response($result);

    }
    
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Check messages",
     *   parameters = {
     *     {"name"="messages", "dataType"="string", "required"=true, "description"="Array of Messages Id's."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    
    public function postCheckMessagesAction(Request $request){
    	$messages = $request->get('messages');
    	$messages = implode(',',$messages);
    	$messenger = $this->get('messenger');
    	//return $messenger->response(array($messages));
    	$result['readMessages'] = $messenger->checkMessagesIfReadApi($messages);
    	
    	return $this->view( $result, Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get count of new messages, count of new notifications and new Messages not notified",
     *
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function getNewMessagesAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $res = $conn->query("CALL get_new_messages ('"
            . $user->getId() . "', '"
            . $user->getGender()->getId() . "')")
            ->fetch();
        if($res['newMessagesNumber'] > 0) {
            $res['messages'] = $conn->query("CALL get_new_messages_not_notified ('"
                . $user->getId() . "')")
                ->fetchAll();
            $res['newMessagesText'] = $this->get('translator')->trans('You have a new message!');
        }else{
            $res['messages'] = array();
        }

        return $this->view($res, Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Send message",
     *   parameters = {
     *     {"name"="message", "dataType"="string", "required"=true, "description"="Message text."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postSendMessageAction(Request $request, User $contact)
    {
        $mobileDetector = $this->get('mobile_detect.mobile_detector');
        if($mobileDetector->isIOS()){
            $responseData = array('status' => 403, 'msg' => 'User Not Authenticated');
            $response = new JsonResponse();
            $response->setData($responseData);
            $response->setStatusCode($responseData['status']);
            return $response;
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $contactId = $contact->getId();
        $message = $request->get('message');
        $options['message'] = strip_tags($message);
        $options['message'] = urlencode($message);
        $options['userId'] = $userId;
        $options['contactId'] = $contactId;

        $chat = new Chat($options);
            //
        $chatIsNotActive = $chat->getUsersBlockStatus();
        if($chatIsNotActive != '0'){
            $message =
                ($chatIsNotActive == '1')
                ?
                $this->get('translator')->trans('This message was not sent because this user has been blocked by the administrator.')
                :
                $this->get('translator')->trans('This message was not sent because your account has been blocked by the administrator.')
            ;

            return $this->view(array('success' => false, 'chatIsNotActive' => $chatIsNotActive, 'errorMessage' => $message), Response::HTTP_OK);
        }

        if($chat->isForbidden()){
            //$this->get('translator')->trans('Message was not sent. You\'re in this user blocked list.')
            $errMess = $this->get('translator')->trans('This message was not sent because this user is in your blocked list or you are in theirs. If you wish to remove this person from your blocked list- go to your Blocked list and remove this user from the list by clicking on the Unblock button.');
            return $this->view(array('success' => false, 'chatIsForbidden' => true, 'errorMessage' => $errMess), Response::HTTP_OK);
        }

        if($chat->contact()->isFrozen()){
            return $this->view(array('success' => false, 'contactIsFrozen' => true, 'errorMessage' => $this->get('translator')->trans('This message was not sent because it user froze the account.')), Response::HTTP_OK);
        }

        $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);

        if($chat->isLimit($settings->getSendMessageUsersNumber())){
            return $this->view(array('success' => false, 'isLimit' => true, 'errorMessage' => $this->get('translator')->trans('You have reached the maximum amount of messaging.')), Response::HTTP_OK);
        }

        $messageObj = $chat->sendMessage();

        if($messageObj){
        	$mobileDetector = $this->get('mobile_detect.mobile_detector');
        	
           /* if(($mobileDetector->isAndroidOS() and $chat->isNotSentToday())){

                $contact = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->contact()->getId());
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->user()->getId());

                if($contact->getIsSentEmail()) {

                    $subject = $request->getHost() . ' | ' . $this->get('translator')->trans('New message')
                        . ' | ' . date("m/d/Y H:i");

                    $image = ($user->getMainPhoto() === null) ? $user->getNoPhoto() : $user->getMainPhoto()->getFaceWebPath();

                    $body = '<div dir="ltr">';
                    $body .= $this->get('translator')->trans('A new message is waiting for you') . '<br>';
                    $body .= $this->get('translator')->trans('From:') .' ' . '<strong>' . $user->getUsername() . '</strong><br>';
                    $body .= $this->get('translator')->trans('Age:') .' ' . $user->age() . '<br>';
                    $body .= $this->get('translator')->trans('Area:') .' ' . $user->getZipCode()->getArea()->getRegion()->getName() . '<br>';
                    $body .= '<img src="' . $request->getSchemeAndHttpHost() . $image . '" style="max-width: 300px"/>';
                    $body .= '</div>';

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    //$headers .= 'From: ' . $settings->getContactEmail() . ' <' . $settings->getContactEmail() .'>' . "\r\n";
                    $headers .= 'From: ' . $settings->getContactFromEmail() . ' <' . $settings->getContactFromEmail() . '>' . "\r\n";
                    mail($contact->getEmail(), $subject, $body, $headers);
                }
                //UNCOMMENT
                if($contact->getIsSentPush()){ 
                    $messenger = $this->get('messenger');
                    $messenger->pushNotification('You got a new message from ' . $user->getUsername(), $contact->getId());
                }
            }
           */

            //$contact = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->contact()->getId());
            //$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->user()->getId());
            //$messenger = $this->get('messenger');
            //$messenger->pushNotification('You got a new message from ' . $user->getUsername(), $contact->getId());

            return $this->view(array('success' => true, 'message' => $messageObj), Response::HTTP_OK);
        }

        return $this->view(array('success' => false), Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Send push",
     *   
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */
    public  function postSendPushAction(Request $request, User $contact ) {
        $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->find(1);
    	$user = $this->get('security.token_storage')->getToken()->getUser();
    	$userId = $user->getId();
    	$contactId = $contact->getId();
    	
    	$options['userId'] = $userId;
    	$options['contactId'] = $contactId;
    	
    	$chat = new Chat($options);

    	if($chat->isNotSentToday() or $contact->getId() == 1249){
    	
    		$contact = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->contact()->getId());
    		$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->user()->getId());
    	
    		if($contact->getIsSentEmail()) {
    	
    			$subject = $request->getHost() . ' | ' . $this->get('translator')->trans('New message')
    			. ' | ' . date("m/d/Y H:i");
    	
    			$image = ($user->getMainPhoto() === null) ? $user->getNoPhoto() : $user->getMainPhoto()->getFaceWebPath();
    	
    			$body = '<div dir="ltr">';
    			$body .= $this->get('translator')->trans('A new message is waiting for you') . '<br>';
    			$body .= $this->get('translator')->trans('From:') .' ' . '<strong>' . $user->getUsername() . '</strong><br>';
    			$body .= $this->get('translator')->trans('Age:') .' ' . $user->age() . '<br>';
    			$body .= $this->get('translator')->trans('Area:') .' ' . $user->getZipCode()->getArea()->getRegion()->getName() . '<br>';
    			$body .= '<img src="' . $request->getSchemeAndHttpHost() . $image . '" style="max-width: 300px"/>';
    			$body .= '</div>';
    	
    			$headers = "MIME-Version: 1.0" . "\r\n";
    			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    			//$headers .= 'From: ' . $settings->getContactEmail() . ' <' . $settings->getContactEmail() .'>' . "\r\n";
    			$headers .= 'From: '.$settings->getContactFromEmail() .' <' . $settings->getContactFromEmail() . '>' . "\r\n";
    			mail($contact->getEmail(), $subject, $body, $headers);
    		}
    		if($contact->getIsSentPush()){
    			$messenger = $this->get('messenger');
    			$messenger->pushNotification('You got a new message from ' . $user->getUsername(), $contact->getId(), $user->getId());
    		}
    	}
//        $contact = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->contact()->getId());
//        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($chat->user()->getId());
//        $messenger = $this->get('messenger');
//        $messenger->pushNotification('You got a new message from ' . $user->getUsername(), $contact->getId());
    	 
    }
    

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Set message as read",
     *   parameters = {
     *     {"name"="message_id", "dataType"="integer", "required"=true, "description"="Message id."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function postReadMessageAction(Request $request, User $contact)
    {
        $messageId = $request->get('message_id',false);
        $result = false;
        if($messageId) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
            $contactId = $contact->getId();

            $options['userId'] = $userId;
            $options['contactId'] = $contactId;

            $chat = new Chat($options);
            $result = $chat->setMessageAsRead($messageId);
        }
        return $this->view(array('success' => $result), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Set message as notified",
     *   parameters = {
     *     {"name"="message_id", "dataType"="integer", "required"=true, "description"="Message id."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function notifyMessageAction(Request $request)
    {
        $messageId = $request->get('message_id',false);
        $result = false;
        if($messageId) {
            //$user = $this->get('security.token_storage')->getToken()->getUser();
            //$userId = $user->getId();
            $messenger = $this->get('messenger');
            $result = $messenger->setMessageAsNotified($messageId);
        }
        return $this->view(array('success' => $result), Response::HTTP_OK);
    }


    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Use free point for read message",
     *   parameters = {
     *     {"name"="message_id", "dataType"="integer", "required"=true, "description"="Message id."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when bad credentials were sent"
     *   }
     * )
     */

    public function useFreePointToReadMessageAction(Request $request)
    {
        $messageId = $request->get('message_id',false);
        $result = array('success' => false);
        if($messageId) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
            $messenger = $this->get('messenger');
            $result = $messenger->useFreePointToReadMessage($messageId, $userId, false);
        }
        return $this->view($result, Response::HTTP_OK);
    }


}
