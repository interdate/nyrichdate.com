<?php 

namespace AppBundle\Services\Messenger;

use Symfony\Component\HttpFoundation\JsonResponse;

class Messenger{ 
	
	public $db;
	public $config;
	public $isNewMessage = false;
	
	public function __construct(){
		$this->config = Config::getInstance();
		$this->config = $this->arrayToObject($this->config);
		$this->db = Database::getInstance($this->config->database);
		//$this->db = $db;
		date_default_timezone_set('Asia/Jerusalem');
	}
	
	public function response($array) {
		return new JsonResponse($array);
	}
	
	public function arrayToObject($array){
		$json = json_encode($array);
		return json_decode($json);
	}
	
	public function isNewMessage(){
		return $this->isNewMessage;		
	}
	
	public function openChat($options){
		$userAttributes = new UserAttributes();
		
		$chatSession = $userAttributes->get($this->config->messengerSession, array($options['userId'], $options['contactId']));
		if(count($chatSession) == 0){
			$userAttributes->post($this->config->messengerSession, array($options['userId'], $options['contactId']));
			return true;
		}
		 
		return false;
	}
	
	public function closeChat($options){		
		$sql = "DELETE FROM " . $this->config->messengerSession->table . " WHERE userId = ? AND contactId = ?";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $options['userId'], \PDO::PARAM_INT);
		$stmt->bindParam(2, $options['contactId'], \PDO::PARAM_INT);
		$success = ($stmt->execute()) ? true : false;
		return $this->response(array('success' => $success));		
	}
	
	public function getActiveChats($options){   
		$userAttributes = new UserAttributes();
		$activeChats = array();
		
		$sql = "
			SELECT
				s.userId, s.contactId, u.username FROM " . $this->config->messengerSession->table . " s
			JOIN
				" . $this->config->users->table . " u
			ON
				(s.contactId = u.id)
			WHERE
				s.userId = ?";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $options['userId'], \PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll();
		
		foreach ($result as $row){
			
			$chat = new Chat(array(
				'userId' => $row['userId'],
				'contactId' => $row['contactId']
			));
			
			if(!$chat->isForbidden()){
				$activeChats[] = array(
					'id' => $row['contactId'],
					'name' => $row['username']
				);
			}
		}
		
		return $activeChats;
	}
		
	public function checkActiveChatsNewMessages($options){
		$result = array();
		$dateTime = array();
		$userAttributes = new UserAttributes();		
		
		$allChats = $userAttributes->get($this->config->messengerSession, array($options['userId']));		
		
		if(count($allChats)){
		
			$startTime = time();
			while(time() - $startTime < 10) {
				
				foreach ($allChats as $chatOptions){
					$chat = new Chat($chatOptions);
					$newMessages = $chat->getNewMessages();
					
					if(count($newMessages) > 0){
						$allowedToReadMessage = ($chat->user()->isPaying() || $chat->contact()->isPaying()) ? true : false;
						foreach ($newMessages as $message){
							$this->isNewMessage = true;						
							$messageDateObject = new \DateTime($message['date']);
							$timestamp = $messageDateObject->getTimestamp();
							$date = date("m/d/Y", $timestamp);
							$time = date("H:i", $timestamp);
							
							$text = ($message['fromUser'] != $chat->user()->getId() && !$allowedToReadMessage)
								? ''
								: nl2br(urldecode($message['message']))
							;
							
							$result[] = array(
								"id" => $message['messageId'],
								"from" => $chat->contact()->getId(),						
								"text" => $text,  								
								"dateTime" => $date . ' ' . $time, 
								"userImage" => $chat->contact()->getImage(),
								"userName" => $chat->contact()->getNickName(),
								"allowedToRead" => $allowedToReadMessage
							);						
						}
					}	
				}
				
				if($this->isNewMessage()){
					$timestamp = time();
					$time = date("i:s", $timestamp);
					$dateTime[] = $time;
					
					foreach($result as $message){
						$chat->setMessageAsDelivered($message['id']);
					}
									
					//return $this->response(array("newMessages" => $result, "MinSec" => $dateTime));
					//exit(0);
					return array(
						"newMessages" => $result,
						"currentUserHasPoints" => $chat->user()->hasPoints(),
						"MinSec" => $dateTime,
					);
									
				}
			
				usleep(500);  
			}

		}
		   
		$timestamp = time();
		$time = date("i:s", $timestamp);
		$dateTime[] = $time;
		//return $this->response(array("newMessages" => $result, "MinSec" => $dateTime));
		return array("newMessages" => $result, "MinSec" => $dateTime);
	}

	public function checkMessagesIfRead($messages){
		/*
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);
		*/
		
		$readMessages = array();	
		$startTime = time();
		while(time() - $startTime < 10) {			
			if(mb_strlen(trim($messages), "utf-8") > 0){
				$sql = "SELECT messageId FROM " . $this->config->messenger->table . " WHERE messageId IN (" . trim($messages) . ") AND isRead = 1";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				foreach ($stmt->fetchAll() as $row){
					$readMessages[] = $row['messageId'];
				}
				
				if(count($readMessages)){
					return $readMessages;
				}
			}
			
			usleep(500);
		}
		
		return $readMessages;
	}
	
	public function checkMessagesIfReadApi($messages){
		/*
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);
			*/
		
		$readMessages = array();
		//	if(mb_strlen(trim($messages), "utf-8") > 0){
		
				$sql = "SELECT messageId FROM " . $this->config->messenger->table . " WHERE messageId IN (" . $messages . ") AND isRead = 1";
				$stmt = $this->db->prepare($sql);				
				$stmt->execute();
				$messRead = $stmt->fetchAll();
				if(count($messRead) > 0){
					foreach ($messRead as $row){
						$readMessages[] = $row['messageId'];
					}
				}
		//}
	
		return $readMessages;
	}
	
	
	public function checkNewMessages($options){
		
		$users = array();
        $messagesIds = array();
		
		$sql = "
			SELECT 
				u.id, m.messageId, m.fromUser, m.message, m.isRead, m.isDelivered, u.username, u.gender_id, CONCAT ( p.id, '.', p.ext )  as photoName 
			FROM " . $this->config->messenger->table . " m
			JOIN 					 
				" . $this->config->users->table . " u 
			ON
				( m.fromUser = u.id)

			LEFT JOIN
			    file p
			ON
			    ( p.user_id = u.id AND p.is_main = 1 AND p.is_valid = 1 AND p.type = 1)
			WHERE 
				m.toUser = ? AND m.isRead = 0 AND m.isNotified = 0 AND m.date > ?";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $options['userId'], \PDO::PARAM_INT);		
		$stmt->bindParam(2, $options['lastLoginAt']);
		$stmt->execute();
		$result = $stmt->fetchAll();
		
		foreach ($result as $row){
            $message = strip_tags(urldecode($row['message']));
            if(strlen($row['message']) > 75){
                $message = substr($message, 0, 74) . '...';
            }
            $photo = '/media/photos/' . $row['id'] . '/' . $row['photoName'];
            if(!is_file($_SERVER['DOCUMENT_ROOT'] . $photo)){
                $photo = '/images/no_photo_' . $row['gender_id'] . '.png';
            }
			/*$photo = (!empty ($row['photoName']))
                ? cloudinary_url($row['photoName'], array(
                    "width" => 150, "height" => 150, "crop" => "thumb", "gravity" => "face"//, "radius" => 20
                ))
                : '/images/no_photo_thumb_' . $row['gender_id'] . '.jpg'
            ;*/

			
			$user = array(
				"id" => $row['fromUser'],
				"name" => $row['username'],
				"isDelivered" => $row['isDelivered'],
				"isRead" => $row['isRead'],
                "messageId" => $row['messageId'],
				"message" => $message,
                "photo" => $photo,
			);
			
			if(!in_array($user, $users)){
				$users[] = $user;
			}

            $messagesIds[] = $row['messageId'];
		}



        if(count($messagesIds)){
            $sql = "UPDATE messenger SET isNotified = 1 WHERE messageId IN (" . implode(',', $messagesIds) . ")";
            $this->db->query($sql);
        }





		return $this->response(array("fromUsers" => $users));
	}

	public function checkNewMessagesMobile($options){



		$sql = "
			SELECT
				COUNT(m.messageId) as newMessagesNumber FROM " . $this->config->messenger->table . " m
			JOIN
				" . $this->config->users->table . " u
			ON
				m.fromUser = u.id AND m.fromUser <> ? AND u.is_active = 1 AND u.is_non_locked = 1 AND u.is_frozen = 0
			WHERE
				m.toUser = ? AND m.isRead = 0";

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $options['contactId'], \PDO::PARAM_INT);
		$stmt->bindParam(2, $options['userId'], \PDO::PARAM_INT);
		$stmt->execute();
		return $this->response($stmt->fetch());
	}

	public function checkDialogNewMessages($options){
		$result = array();
		$dateTime = array();
		$startTime = time();
		
		while(time() - $startTime < 10) {
			
			$dialog = new Dialog($options);
			$newMessages = $dialog->getNewMessages();
			
			//return $this->response(array("newMessages" => $newMessages, "MinSec" => $dateTime));
			//die();
		
			if(count($newMessages) > 0){
				$allowedToReadMessage = ($dialog->user()->isPaying() || $dialog->contact()->isPaying()) ? true : false;
				
				foreach ($newMessages as $message){
					$this->isNewMessage = true;
					$messageDateObject = new \DateTime($message['date']);
					$timestamp = $messageDateObject->getTimestamp();
					$date = date("m/d/Y", $timestamp);
					$time = date("H:i", $timestamp);
					$text = ($message['fromUser'] != $dialog->user()->getId() && !$allowedToReadMessage)
						? ''
						: nl2br(urldecode($message['message']))
					;
		
					$result[] = array(
						"id" => $message['messageId'],
						"from" => $dialog->contact()->getId(),
						"text" => $text,
						"dateTime" => $date . ' ' . $time,
						"userImage" => $dialog->contact()->getImage(),
						"userName" => $dialog->contact()->getNickName(),
						"allowedToRead" => $allowedToReadMessage,
					);
				}
			}			
			$readMessages = (isset($options['messages']) and !empty($options['messages'])) ? $this->checkMessagesIfReadApi($options['messages']) : array();
			if($this->isNewMessage()){
				$timestamp = time();
				$time = date("i:s", $timestamp);
				$dateTime[] = $time;
		
				foreach($result as $message){
					$dialog->setMessageAsDelivered($message['id']);
				}
				
				//return $this->response(array("newMessages" => $result, "MinSec" => $dateTime));
				//exit(0);
				
				return array(
					"newMessages" => $result, 
					"currentUserHasPoints" => $dialog->user()->hasPoints(), 
					"MinSec" => $dateTime,
					"readMessages" => $readMessages
				);
			}else{
				return array(
				    "newMessages" => '',
					"readMessages" => $readMessages
				);
			}
		
			usleep(500);
		}
		 
		$timestamp = time();
		$time = date("i:s", $timestamp);
		$dateTime[] = $time;
		//return $this->response(array("newMessages" => $result, "MinSec" => $dateTime));
		return array("newMessages" => $result, "MinSec" => $dateTime);
	}
	
	public function getNewMessagesNumber($options = false){
		
		$sql = "
			SELECT
				m.messageId FROM " . $this->config->messenger->table . " m	
			JOIN
				users u
			ON
				u.id = m.fromUser 
				AND u.userBlocked = 0 
				AND u.userFrozen = 0 
				AND u.userNotActivated = 0
			WHERE
				m.toUser = ? AND m.isRead = 0";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $options['userId'], \PDO::PARAM_INT);
		$stmt->execute();
		return count($stmt->fetchAll());
	}
	
	
	public function getUsersMessages($page, $perPage, $userId, $conn)
    {
        $offset = ($page - 1) * $perPage;
        $userCond = $userId === null ? '' : 'WHERE m.fromUser = ' . $userId . ' OR m.toUser = ' . $userId;


        $sql = "
            SELECT
              m.messageId,
              m.fromUser,
              m.toUser,
              m.message,
              m.date,
              fromUser.username as fromUsername,
              toUser.username as toUsername
            FROM
              messenger m
            LEFT JOIN
              user fromUser
            ON fromUser.id = m.fromUser

            LEFT JOIN
              user toUser
            ON toUser.id = m.toUser
            " . $userCond . "
            ORDER BY m.messageId DESC
            LIMIT " . $offset . "," . $perPage
        ;

        $stmt = $conn->prepare($sql);
		$stmt->execute();
        $messages = $stmt->fetchAll();

		foreach ($messages as $key => $item){
            $messages[$key]['message'] = nl2br(urldecode($item['message']));
		}
		
		return $messages;
		
	}

    public function getUsersMessagesNumber($userId)
    {
        $userCond = $userId === null ? '' : 'WHERE m.fromUser = ' . $userId . ' OR m.toUser = ' . $userId;

        $sql = "
            SELECT
              COUNT(m.messageId) as number
            FROM
              messenger m
            LEFT JOIN
              user fromUser
            ON fromUser.id = m.fromUser

            LEFT JOIN
              user toUser
            ON toUser.id = m.toUser
            " . $userCond
        ;

        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
		return $result['number'];

	}
	
	public function useFreePointToReadMessage($messageId, $userId, $json = true ){
		$user = new User($userId);		
		if($user->hasPoints()){			
			$sql = "SELECT fromUser, message FROM " . $this->config->messenger->table . " WHERE messageId = ?";			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(1, $messageId, \PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			
			$result = array(
				'success' => true,
				'message' => array(
					'from' => $row['fromUser'],
					'text' => nl2br(urldecode($row['message'])),
				)				
			);			
						
			$sql = "UPDATE " . $this->config->messenger->table . " SET isRead = 1, isDelivered = 1 WHERE messageId = '" . $messageId . "'";				
			$stmt = $this->db->query($sql);
			
			$sql = "UPDATE user SET points = points - 1 WHERE id = '" . $userId . "'";
			$stmt = $this->db->query($sql);
			
			return ($json) ? $this->response($result) : $result;
		}
				
		return ($json) ? $this->response(array('success' => false)) : array('success' => false);
	}


    public function pushNotification($message, $userId, $id = 0) {
        //var_dump(234);die;
        $sql = "SELECT * FROM users_device WHERE user_id = ? and hash is null";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $androidDevices = false;


        foreach ($rows as $row) {

            if($row['ios'] != null) {
                /*
                // Put your device token here (without spaces):
                $deviceToken = trim($row['ios']);

                // Put your private key's passphrase here:
                $passphrase = 'interdate';

                //var_dump()

                $ctx = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'] . '/../src/AppBundle/Services/Messenger/nyrd.pem');
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
                stream_context_set_option($ctx, 'ssl', 'allow_self_signed', true);

                // Open a connection to the APNS server
                $fp = stream_socket_client(
                    'ssl://gateway.push.apple.com:2195', $err,
                    $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

                if (!$fp) {
                    exit("Failed to connect: $err $errstr" . PHP_EOL);
                }

                //echo 'Connected to APNS' . PHP_EOL;

                // Create the payload body
                $body['aps'] = array(
                    'alert' => $message,
                    'sound' => 'default',
                    'count' => 1,
                );

                // Encode the payload as JSON
                $payload = json_encode($body);

                // Build the binary notification
                $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                // Send it to the server
                $result = fwrite($fp, $msg, strlen($msg));
                //print_r($this->id) . '<br>';

                fclose($fp);
                */
            }

            if($row['android'] != null) {
                $androidDevices[] = $row['android'];
            }
            if($row['browser'] != null){
                //$browserDevices[] = $row['browser'];
                $this->sendBrowserPush($row['browser'],$message, array('user' => $id, 'contact' => $userId));
            }
        }


        if($androidDevices != false){
            // API access key from Google API's Console
            if($userId == 455){
                //var_dump($androidDevices);die;
            }
            if(!defined('API_ACCESS_KEY')){
                define( 'API_ACCESS_KEY', 'AAAACzlAATY:APA91bGtB5UKvqm6dmdZm4TJEIqdpihpOMbyDGbk0AbkiM8144hg2AvnAddpwkxEEj7wTjiPC65QJTEVoOTxMPIAPpFR2WG2Gn0ecsQYITHOCenIJ_YgS21WofPxrlhwX7zVzBUN3ivxvnPU591lzx75JuewJ3v_7w' );
            }

            // prep the bundle
            $msg = array
            (
                'title'		=> 'NY Sugar Daddy',
                'message' 	=> $message,
                //'subtitle'	=> 'This is a subtitle. subtitle',
                //'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
                'vibrate'	=> 1,
                'sound'		=> 1,
                'notId' 	=> time(),
                //'style' 	=> 'inbox',
                //'largeIcon'	=> 'large_icon',
                //'smallIcon'	=> 'small_icon'
            );
            $fields = array
            (
                'registration_ids' 	=> $androidDevices,
                'data'			=> $msg
            );

            $headers = array
            (
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result = curl_exec($ch );
            curl_close( $ch );

        }
    }

    public function sendBrowserPush($token, $message, $data){
        // API access key from Google API's Console
        $apiKey = "AAAACzlAATY:APA91bGtB5UKvqm6dmdZm4TJEIqdpihpOMbyDGbk0AbkiM8144hg2AvnAddpwkxEEj7wTjiPC65QJTEVoOTxMPIAPpFR2WG2Gn0ecsQYITHOCenIJ_YgS21WofPxrlhwX7zVzBUN3ivxvnPU591lzx75JuewJ3v_7w";


        $clickUrl = ($data['user'] > 0) ? "https://www.nyrichdate.com/user/messenger/dialog/open/userId:" . $data['contact'] . "/contactId:". $data['user'] : "https://www.nyrichdate.com/user/messenger";
        $fields = array
        (
            'to' 	=> $token,
            "notification" => array(
                "body" => $message,
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
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        $res = json_decode(trim($result), true);
        //var_dump(json_decode(trim($result), true));die;
        if($res["failure"] == 1 or $res["results"][0]["error"] == 'NotRegistered'){
            $this->removebrewserToken($token,$data['contact']);
        }
        var_dump($res);

    }


    public function removebrewserToken($token,$id){
        $sql = "DELETE FROM users_device WHERE browser = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $token, \PDO::PARAM_STR);
        $stmt->bindParam(2, $id, \PDO::PARAM_INT);
        $stmt->execute();
	}



	public function setUserDevice($mob_os, $token, $userId){

        $sql = "SELECT * FROM users_device WHERE $mob_os = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $token, \PDO::PARAM_STR);
        $stmt->bindParam(2, $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        if(count($row) == 0) {
            $sql = "INSERT INTO users_device ($mob_os, user_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $token, \PDO::PARAM_STR);
            $stmt->bindParam(2, $userId, \PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    public function getUserByHash($token){
        $sql = "SELECT * FROM users_device WHERE hash = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $token, \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return (count($row) > 0) ? $row[0]['user_id'] : false;
    }


    public function setMessageAsNotified($messageId){
        //$sql = "UPDATE " . $this->config->messenger->table . " SET isRead = 1 WHERE messageId = ? AND toUser = ? AND fromUser = ? AND isRead = 0";
        $sql = "UPDATE " . $this->config->messenger->table . " SET isNotified = 1 WHERE messageId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $messageId, \PDO::PARAM_INT);
        if($stmt->execute())
            return true;

        return false;
    }

	/*
	public function checkMessagesIfRead($messages){
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);
		$readMessages = array();
		if(mb_strlen(trim($messages), "utf-8") > 0){			
			$sql = "SELECT messageId FROM " . $this->config->messenger->table . " WHERE messageId IN (" . trim($messages) . ") AND isRead = 1";			
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			foreach ($stmt->fetchAll() as $row){
				$readMessages[] = $row['messageId'];
			}
		}
		return $readMessages;
	}
	*/
	
	public function removeMessages($messagesIdsString){

		$sql1 = "DELETE FROM " . $this->config->messenger->table . " WHERE messageId IN (" . $messagesIdsString . ")";
		$this->db->query($sql1);

        $sql = "SELECT * FROM " . $this->config->lastMessages->table . " WHERE messageId IN (" . $messagesIdsString . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if(count($rows) > 0){
            foreach ($rows as $row) {
                $id = $row['id'];
                $fromUser = $row['user1'];
                $toUser = $row['user2'];

                $sql = "SELECT * FROM " . $this->config->messenger->table . " WHERE (toUser = ? and fromUser = ? ) OR (toUser = ? and fromUser = ?) AND messageId NOT IN (" . $messagesIdsString . ") ORDER BY date DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(1, $fromUser, \PDO::PARAM_INT);
                $stmt->bindParam(2, $toUser, \PDO::PARAM_INT);
                $stmt->bindParam(3, $toUser, \PDO::PARAM_INT);
                $stmt->bindParam(4, $fromUser, \PDO::PARAM_INT);
                $stmt->execute();
                $newMess = $stmt->fetch();
                //var_dump($newMess);
                $messageId = $newMess ? $newMess['messageId'] : '';
                $message = $newMess ? $newMess['message'] : '';
                $date = $newMess ? $newMess['date'] : '';

                $sql = "UPDATE " . $this->config->lastMessages->table . " SET messageId = ?, message = ?, date = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                //var_dump($sql);
                $stmt->bindParam(1, $messageId, \PDO::PARAM_INT);
                $stmt->bindParam(2, $message, \PDO::PARAM_STR);
                $stmt->bindParam(3, $date, \PDO::PARAM_STR);
                $stmt->bindParam(4, $id, \PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        echo $sql1;

	}
	
}
