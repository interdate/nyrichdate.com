<?php

namespace AppBundle\Controller\Backend;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use AppBundle\Services\Messenger\Chat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessengerController extends Controller
{
    /**
     * @Route("/admin/messenger/list/{page}", defaults={"page" = 1, "userId" = null}, name="admin_messenger")
     * @Route("/admin/messenger/user/{userId}/{page}", defaults={"page" = 1, "userId" = null}, name="admin_messenger_user")
     */
    public function indexAction(Request $request, $page, $userId)
    {

        $perPage = $userId === null ? 500 : 50;
        $messages = $this->get('messenger')->getUsersMessages($page, $perPage, $userId, $this->getDoctrine()->getConnection());
        $messagesNumber = $this->get('messenger')->getUsersMessagesNumber($userId);

        $route = $request->get('_route');
        if($route == 'admin_messenger'){
            $title = "All Messages";
            $icon = 'list';
        }
        else{
            $title = "Messages of "
                . $this->getDoctrine()->getRepository('AppBundle:User')->find($userId)->getUsername();
            ;
            $icon = 'wechat';
        }

        return $this->render('backend/messenger/index.html.twig', array(
            'messages' => $messages,
            'title' => $title,
            'icon' => $icon,
            'userId' => $userId,
            'pagination' => array(
                'page' => $page,
                'route' => $route,
                'pages_count' => ceil($messagesNumber / $perPage),
            )
        ));
    }

    /**
     * @Route("/admin/messenger/messages/delete", name="admin_messenger_messages_delete")
     */
    public  function removeMessagesAction(Request $request){
        $messagesIds = $request->request->get('messagesIds');
        $this->get('messenger')->removeMessages($messagesIds);
        return new Response();
    }

    /**
     * @Route("/admin/messenger/send", name="admin_messenger_send")
     */
    public function sendMessagesAction(Request $request)
    {
        $reportsRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Report');
        $reports = $reportsRepo->findAll();
        $message = $request->request->get('message', '');
        $toUsers = $request->request->get('reportId', false);
        $title = "Sent Message to Users";
        $icon = 'send';
        //var_dump($message, $toUsers);die();
        $send = false;
        //var_dump($this->getUser()->getId());
        if($toUsers != false and !empty($message)){
            $usersRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
            $report = $reportsRepo->find($request->request->get('reportId'));
            $data = json_decode($report->getParams(), true);
            $data['filter'] = '';

            $users = $usersRepo->setAdminMode()->search(
                array(
                    'current_user' => $this->getUser(),
                    'data' => $data,
                    'allResults' => true
                )
            );
            //var_dump(count($users));

            $options['userId'] = $this->getUser()->getId();
            $options['message'] = $message;


            foreach((array)$users as $user){
                $contactId = $user->getId();
                $options['contactId'] = $contactId;
                $chat = new Chat($options);
                $messageObj = $chat->sendMessage();

                if($messageObj){
                    $messenger = $this->get('messenger');
                    $messenger->pushNotification('You got a new message from ' . $this->getUser()->getUsername(), $contactId, $this->getUser()->getId());
                }
            }

            $send = true;
        }
        //var_dump($send);die();
        return $this->render('backend/messenger/send.html.twig', array(
            'messages' => $message,
            'title' => $title,
            'icon' => $icon,
            'reports' => $reports,
            'send' => $send
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
