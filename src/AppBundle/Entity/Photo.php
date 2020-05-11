<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Photo extends File
{


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="photos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain;

    /**
     * Set isMain
     *
     * @param boolean $isMain
     * @return Photo
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return boolean 
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    public function getUploadDir()
    {
        return '/media/photos/' . $this->user->getId();
    }

    public function getFaceAbsolutePath(){
        //$this->setIdIfDeleted();
        return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir() . '/' . $this->id . '-face.' . $this->ext;
    }

    public function getFaceWebPath()
    {
        $faceWebPath = $this->getUploadDir() . '/' . $this->id . '-face.' . $this->ext;
        return is_file($_SERVER['DOCUMENT_ROOT'] . $faceWebPath) ? $faceWebPath : $this->getWebPath();
    }

    public function getNoFile()
    {
        return $this->user->getNoPhoto();
    }

    /**
     * Called before entity removal
     *
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        parent::removeUpload();

        $file = $this->getFaceAbsolutePath();
        if (null !== $file && is_file($file)) {
            unlink($file);
        }
    }

    public function detectFace($hostName)
    {
        $api_key = 'AIzaSyDzvtLRGb0ZjgIHZ8GFTZrhDe3fvRTsai8';
        $cvurl = 'https://vision.googleapis.com/v1/images:annotate?key=' . $api_key;
        $type = 'FACE_DETECTION';
        $photoUrl = 'http://' . $hostName . $this->getWebPath();
        $data = file_get_contents($photoUrl);
        $base64 = base64_encode($data);
        $request_json = '
            {
                "requests": [
                    {
                        "image": {
                            "content":"' . $base64 . '"
                        },
                        "features": [
                            {
                                "type": "' . $type . '",
                                "maxResults": 200
                            }
                        ]
                    }
                ]
            }';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $cvurl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);
        $json_response = curl_exec($curl);
        curl_close($curl);
        $result = (json_decode($json_response, true));

        if(!isset($result['responses'][0]['faceAnnotations'])){
            return null;
        }

        $vertices = $result['responses'][0]['faceAnnotations'][0]['boundingPoly']['vertices'];
        $topLeft = $vertices[0];
        $topRight = $vertices[1];

        if(!isset($topRight['x'])){
            $topRight['x'] = 0;
        }

        if(!isset($topLeft['x'])){
            $topLeft['x'] = 0;
        }

        if($topRight['x'] == 0 && $topLeft['x'] > 0){
            $size = getimagesize($photoUrl);
            $squareSideLength = $size[0] - $topLeft['x'];
        }
        else{
            $squareSideLength = $topRight['x'] - $topLeft['x'];
        }

        return array(
            'x' =>  $topLeft['x'],
            'y' =>  $topLeft['y'],
            'w' => $squareSideLength,
            'h' => $squareSideLength,
        );

    }

    public function rotate($rotate = 90){
        //$this->contener
//        $f = fopen($path, 'w');
//        fwrite($f, $photo);
//        fclose($f);

        //$degrees = 180;

// Content type
        chmod($this->getAbsolutePath(),0777);
        if ($this->ext == 'jpg' || $this->ext == 'jpeg') {
            chmod($this->getAbsolutePath(),0777);
            header('Content-type: image/jpeg');
            $source = imagecreatefromjpeg($this->getAbsolutePath());
            $rotateSorce = imagerotate($source, $rotate, 0);
            $res = imagejpeg($rotateSorce, $this->getAbsolutePath(), 100);
            imagedestroy($source);
            imagedestroy($rotateSorce);
            //var_dump($res);
        } elseif ($this->ext == 'png') {
            header('Content-type: image/png');
            $source = imagecreatefrompng($this->getAbsolutePath());
            $bgColor = imageColorAllocateAlpha($source, 0, 0, 0, 0);
            $rotateSorce = imagerotate($source, $rotate, $bgColor);
            imagealphablending($rotateSorce, false);
            imagesavealpha($rotateSorce, true);
            imagepng($rotateSorce, $this->getAbsolutePath());
            imagedestroy($source);
            imagedestroy($rotateSorce);
        }
        return $this;
    }
}
