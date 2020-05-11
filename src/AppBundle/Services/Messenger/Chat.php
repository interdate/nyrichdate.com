<?php 

namespace AppBundle\Services\Messenger;

class Chat extends Messenger{
	
	public $user;	
	public $contact;
	private $message;
	private $isForbidden;
	
	public function __construct($options){
		parent::__construct();
		$this->user = (!empty($options['userId'])) ? new User($options['userId']) : null;
		$this->contact = (!empty($options['contactId'])) ? new User($options['contactId']) : null;
		$this->message = (isset($options['message']) and mb_strlen(trim($options['message']), "utf-8") > 0) ? new Message($options) : false;
		$this->isForbidden = $this->checkIfForbidden();
		//$this->isForbidden = false;
	}
	
	protected function checkIfForbidden(){
		$sql = "
			SELECT
				owner_id
			FROM
				black_list
			WHERE
				(owner_id = " . $this->contact->getId() . " AND member_id = " . $this->user->getId() . ")
			OR
				(owner_id = " . $this->user->getId() . " AND member_id = " . $this->contact->getId() . ")
		";
		
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		return (!empty($row['owner_id']) );
	}

	public function getUsersBlockStatus()
    {
        $res = 0;
        $sql = "
            SELECT
                id
            FROM
               user
            WHERE
               (id = " . $this->contact->getId() . " OR id = " . $this->user->getId() . ")
            AND
               is_active = 0
        ";

        $stmt = $this->db->query($sql);
        $row = $stmt->fetchAll();

        if (count($row) > 0) {

            if ($row[0]['id'] == $this->user->getId() or (count($row) == 2 and $row[1]['id'] == $this->user->getId())) {
                $res = 2;
            } elseif ($row[0]['id'] == $this->contact->getId() or (count($row) == 2 and $row[1]['id'] == $this->contact->getId())) {
                $res = 1;
            }
        }
        //var_dump($res);die;
        return $res;
    }
	
	public function getNewMessages(){
		$sql = "SELECT 
					messageId,message,date,fromUser 
				FROM 
					" . $this->config->messenger->table . " 
				WHERE 
					toUser = ? AND fromUser = ? AND isDelivered = 0 AND isRead = 0";
				
		$userId = $this->user->getId();
		$contactId = $this->contact->getId();
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $userId, \PDO::PARAM_INT);
		$stmt->bindParam(2, $contactId, \PDO::PARAM_INT);
		$stmt->execute();		
		$result = $stmt->fetchAll();
		return $result;		
	}
	
	public function setMessageAsDelivered($messageId){
		$userAttributes = new UserAttributes();
		$userSession = $userAttributes->get($this->config->messengerSession, array($this->user->getId(), $this->contact->getId()));
		if(count($userSession) == 0){
			return false;
		}
				
		$sql = "UPDATE " . $this->config->messenger->table . " SET isDelivered = 1, modified = ? WHERE messageId = ?";
		$stmt = $this->db->prepare($sql);		
		$modified = date("Y-m-d H:i:s");		
		$stmt->bindParam(1, $modified, \PDO::PARAM_INT);
		$stmt->bindParam(2, $messageId, \PDO::PARAM_INT);
		$stmt->execute();		
	}
	
	public function setMessageAsRead($messageId){
		//$sql = "UPDATE " . $this->config->messenger->table . " SET isRead = 1 WHERE messageId = ? AND toUser = ? AND fromUser = ? AND isRead = 0";
		$sql = "UPDATE " . $this->config->messenger->table . " SET isRead = 1 WHERE messageId = ?";
		$stmt = $this->db->prepare($sql);
		$userId = $this->user->getId();
		$contactId = $this->contact->getId();
		$stmt->bindParam(1, $messageId, \PDO::PARAM_INT);
		//$stmt->bindParam(2, $userId, \PDO::PARAM_INT);
		//$stmt->bindParam(3, $contactId, \PDO::PARAM_INT);		
		if($stmt->execute())
			return true;
		
		return false;
	}
	
	public function contact(){
		return $this->contact;		
	}
	
	public function user(){
		return $this->user;		
	}
	
	public function sendMessage(){

		if($this->message){
		
			$userAttributes = new UserAttributes();
			$userAttributes->post($this->config->messenger,
				array(					
					$this->message->from, 
					$this->message->to,					 
					$this->message->text,
					$this->message->date,
					$this->message->isRead,
					$this->message->isDelivered,
				)
			);
			
			$lastMessageId = $userAttributes->getLastId();
			
			$messageDateObject = new \DateTime($this->message->date);
			$timestamp = $messageDateObject->getTimestamp();
			$date = date("m/d/Y", $timestamp);
			$time = date("H:i", $timestamp);
			
			
			$sql = "				
				SELECT owner_id FROM communication WHERE
					(owner_id = ? AND member_id = ?)
						OR
					(owner_id = ? AND member_id = ?)
			";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(1, $this->message->from, \PDO::PARAM_INT);
			$stmt->bindParam(2, $this->message->to, \PDO::PARAM_INT);
			$stmt->bindParam(3, $this->message->to, \PDO::PARAM_INT);
			$stmt->bindParam(4, $this->message->from, \PDO::PARAM_INT);
			$stmt->execute();
			
			$contacted = $stmt->fetchAll();
			
			if(!count($contacted)){				
				$userAttributes->post($this->config->contacted,
					array(
						$this->message->from,
						$this->message->to,
					)
				);			
			}
			
			$sql = "SELECT id FROM " . $this->config->lastMessages->table . " WHERE 
				( user1 = ? AND user2 = ? ) 
					OR 
				( user1 = ? AND user2 = ?)
			";
							
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(1, $this->message->from, \PDO::PARAM_INT);
			$stmt->bindParam(2, $this->message->to, \PDO::PARAM_INT);
			$stmt->bindParam(3, $this->message->to, \PDO::PARAM_INT);
			$stmt->bindParam(4, $this->message->from, \PDO::PARAM_INT);			
			$stmt->execute();
				
			$lastMessage = $stmt->fetch();
				
			if(empty($lastMessage['id'])){
				$userAttributes->post($this->config->lastMessages,
					array(
						$this->message->from,
						$this->message->to,
						$lastMessageId,
						$this->message->text,
						$this->message->date,
					)
				);
			}
			else{
				$sql = "
					UPDATE " . $this->config->lastMessages->table . " SET 
						messageId = ?, 
						message = ?,
						date = ?
					WHERE id = ?
				";
				
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(1, $lastMessageId, \PDO::PARAM_INT);				
				$stmt->bindParam(2, $this->message->text);
				$stmt->bindParam(3, $this->message->date);
				$stmt->bindParam(4, $lastMessage['id'], \PDO::PARAM_INT);
				$stmt->execute();				
			}
			
			return array(
				"id" => $lastMessageId,	
				"from" => $this->message->from,
				"to" => $this->message->to,
				"text" => $this->message->text,
				"dateTime" => $date . " " . $time,
				"senderImage" => $this->user->getImage(),
				"contactIsOnline" => $this->contact->isOnline(),
				"isSaved" => true,
			);
		
		}
		else return false; 
		
		
	}
	
	public function getHistory($messagesNumber = 30){
		
		$userId = $this->user->getId();
		$contactId = $this->contact->getId();
		$userImage = $this->user->getImage();
		$contactImage = $this->contact->getImage();
		$result = array();
		
		$sql = "SELECT TOP " . $messagesNumber . " 
					fromUser,toUser,message,date,isRead,messageId,isDelivered
				FROM
					" . $this->config->messenger->table . "
				WHERE
					(toUser = ? AND fromUser = ?)
				OR
					(toUser = ? AND fromUser = ?)
				ORDER BY 
					messageId 
				DESC";
		
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $userId, \PDO::PARAM_INT);
		$stmt->bindParam(2, $contactId, \PDO::PARAM_INT);
		$stmt->bindParam(3, $contactId, \PDO::PARAM_INT);
		$stmt->bindParam(4, $userId, \PDO::PARAM_INT);
		$stmt->execute();
		$messages = $stmt->fetchAll();	
		$messages = array_reverse($messages);
		$allowedToReadMessage = ($this->user->isPaying() || $this->contact->isPaying()) ? true : false;
		
		foreach ($messages as $message){		
			$messageDateObject = new \DateTime($message['date']);
			$timestamp = $messageDateObject->getTimestamp();
			$date = date("d/m/Y", $timestamp);
			$time = date("H:i", $timestamp);			
			$isRead = ($message['isRead'] == 0) ? false : true;			
			$image = ($userId == $message['fromUser']) ? $userImage : $contactImage;
			
			$text = ($message['fromUser'] != $this->user->getId() && !$allowedToReadMessage && $message['isRead'] == 0)
				? ''
				: nl2br(urldecode($message['message']))
			; 

			$result[] = array(
				"id" => $message['messageId'],
				"from" => $message['fromUser'],					
				"text" => $text,
				"dateTime" => $date . ' ' . $time,
				"userImage" => $image,
				"isSaved" => true,
				"isRead" => $isRead,
				"allowedToRead" => $allowedToReadMessage
			);
			
			if($message['fromUser'] == $contactId and $message['isDelivered'] == 0){		
				$this->setMessageAsDelivered($message['messageId']);   
			}			
			
		}
		
		return $result;
	}

	public function isNotSentToday(){
		$date = new \DateTime('now');
		$date1 = $date->format("Y-m-d 00:00:00");
		$date2 = $date->format("Y-m-d 23:59:59");
		$userId = $this->user->getId();
		$contactId = $this->contact->getId();

		$sql = "SELECT messageId FROM " . $this->config->messenger->table . " WHERE fromUser = ? AND toUser = ? AND date BETWEEN ? AND ?";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $contactId);
		$stmt->bindParam(3, $date1);
		$stmt->bindParam(4, $date2);
		$stmt->execute();

		return ( count($stmt->fetchAll()) == 1 );
	}
	
	public function isLimit($limit){
        $date = new \DateTime('now');
		$date1 = $date->format("Y-m-d 00:00:00");
		$date2 = $date->format("Y-m-d 23:59:59");
		$userId = $this->user->getId();
        $contactId = $this->contact->getId();

        $sql = "SELECT toUser FROM " . $this->config->messenger->table . " WHERE fromUser = ? AND toUser = ? AND date BETWEEN ? AND ? GROUP BY toUser";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $contactId);
		$stmt->bindParam(3, $date1);
		$stmt->bindParam(4, $date2);
		$stmt->execute();

        if(count($stmt->fetchAll()) > 0){
            return false;
        }

        $sql = "SELECT toUser FROM " . $this->config->messenger->table . " WHERE fromUser = ? AND date BETWEEN ? AND ? GROUP BY toUser";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $date1);
		$stmt->bindParam(3, $date2);
		$stmt->execute();

		return ( count($stmt->fetchAll()) >= $limit );
	}

	public function isForbidden(){
		return $this->isForbidden;		
	}
	
}

?>