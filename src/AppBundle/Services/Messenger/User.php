<?php 

namespace AppBundle\Services\Messenger;

//use AppBundle\Entity\Photo;

class User extends Messenger{
	
	protected $id;
	protected $image;
	protected $nickName;
	protected $gender;
	protected $isFrozen;
	protected $isOnline;
	protected $isPaying;
	protected $hasPoints;
		
	public function __construct($id){
		parent::__construct();
		$this->id = $id;		
		$this->setProperties();
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getImage(){
		return $this->image;
	}
	
	public function getNickName(){
		return $this->nickName;
	}
	
	public function isFrozen(){
		return $this->isFrozen;
	}
	
	public function isOnline(){
		return $this->isOnline;
	}
	
	public function isPaying(){
		return $this->isPaying;
	}
	
	public function hasPoints(){
		return $this->hasPoints;
	}

	public function setProperties(){
		$sql = "
			SELECT 
				u.username,
				u.gender_id,
				u.is_frozen,
				u.points,
				CONCAT ( p.id, '.', p.ext ) as name,
				p.id as photo_id,
				1 as isOnline,
				1 as isPaying
			FROM 
				user u
			LEFT JOIN
				file p
			ON
				p.user_id = u.id AND p.is_main = 1 AND p.is_valid = 1 AND p.type = 1
			WHERE 
				u.id=:id";
		
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam("id",$this->id, \PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
		$this->nickName = (!empty ( $row['username'] ) ) ? $row['username'] : false;
		$this->gender = $row['gender_id'];
		$this->isFrozen = ($row['is_frozen'] == 1) ? true : false;
		$this->isOnline = ($row['isOnline'] == 1) ? true : false;
		$this->isPaying = ($row['isPaying'] == 1) ? true : false;
		$this->hasPoints =  ($row['points'] > 0) ? true : false;

        /*
		$sql = "SELECT id, ext FROM file WHERE id=:id AND is_main = 1 AND is_valid = 1" ;
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam("id",$this->id);
		$stmt->execute();
		$row = $stmt->fetch();
        */

		/*
		if( !empty ( $row['id'] ) && is_file( $_SERVER['DOCUMENT_ROOT'] . '/uploads/userpics/' . $row['id'] . '.' . $row['ext']) ){
			$this->image = $this->config->users->storage->images . '/' . $row['id'] . '.' . $row['ext'];
		}
		else{
			$sql = "SELECT id, name FROM photo WHERE id=:id AND is_valid = 1" ;
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam("id",$this->id);
			$stmt->execute();
			$row = $stmt->fetch();

			if( !empty ( $row['id'] ) && is_file( $_SERVER['DOCUMENT_ROOT'] . '/uploads/userpics/' . $row['id'] . '.' . $row['ext']) ){
				$this->image = $this->config->users->storage->images . '/' . $row['id'];
			}
			else{
				$this->image = ($this->gender == 0) ?  $this->config->users->noImage->male : $this->config->users->noImage->female;
			}
		}
		*/


        //'/media/photos/' . $this->user->getId();
		$this->image = (empty($row['photo_id'])) ? '/images/no_photo_' . $this->gender . '.png' : '/media/photos/' . $this->id . '/' . $row['name'];
        //$this->addPhoto( $row['photo_id'] );

	}
	
	

}

?>