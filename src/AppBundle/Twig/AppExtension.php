<?php

namespace AppBundle\Twig;

use AppBundle\Form\Type\AdminQuickSearchSidebarType;
use AppBundle\Form\Type\QuickSearchSidebarType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends \Twig_Extension
{
    protected $doctrine;
    protected $container;
    protected $requestStack;
    protected $messenger;

    public function __construct(RequestStack $requestStack, RegistryInterface $doctrine, $container, $messenger)
    {
        $this->requestStack = $requestStack;
        $this->doctrine = $doctrine;
        $this->container = $container;
        $this->messenger = $messenger;

    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecodeFilter')),
            //new \Twig_SimpleFilter('imageExist', array($this, 'imageExistFunction')),
        );
    }

    public function jsonDecodeFilter($data){
        return json_decode($data, true);
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSettings', array($this, 'getSettingsFunction')),
            new \Twig_SimpleFunction('getUsersOnline', array($this, 'getUsersOnlineFunction')),
            new \Twig_SimpleFunction('getNewUsers', array($this, 'getNewUsersFunction')),
            new \Twig_SimpleFunction('getStatistics', array($this, 'getStatisticsFunction')),
            new \Twig_SimpleFunction('getQuickSearchSidebarForm', array($this, 'getQuickSearchSidebarFormFunction')),
            new \Twig_SimpleFunction('getAdminQuickSearchSidebarForm', array($this, 'getAdminQuickSearchSidebarFormFunction')),
            new \Twig_SimpleFunction('getFooterBlocks', array($this, 'getFooterBlocksFunction')),
            new \Twig_SimpleFunction('getZodiac', array($this, 'getZodiacFunction')),
            new \Twig_SimpleFunction('getSeo', array($this, 'getSeoFunction')),
            new \Twig_SimpleFunction('getDistance', array($this, 'getDistanceFunction'))
        );
    }

    public function getSettingsFunction()
    {
        return $this->doctrine->getManager()->getRepository('AppBundle:Settings')->find(1);
    }

    public function getUsersOnlineFunction()
    {
        $settings = $this->doctrine->getManager()->getRepository('AppBundle:Settings')->find(1);

        return $this->doctrine->getManager()->getRepository('AppBundle:User')->getOnline(
            array(
                'current_user' => $this->getUser(),
                'data' => null,
                'paginator' => $this->container->get('knp_paginator'),
                'page' => 1,
                'per_page' => 40,//$settings->getUsersPerPage(),
                'considered_as_online_minutes_number' => $settings->getUserConsideredAsOnlineAfterLastActivityMinutesNumber(),
            )
        );
    }

    public function getNewUsersFunction()
    {
        $settings = $this->doctrine->getManager()->getRepository('AppBundle:Settings')->find(1);
        $per_page = (is_object($this->getUser())) ? 40 : $settings->getUsersPerPage();
        return $this->doctrine->getManager()->getRepository('AppBundle:User')->getNew(
            array(
                'considered_as_new_days_number' => $settings->getUserConsideredAsNewAfterDaysNumber(),
                'per_page' => $per_page,
                'current_user' => $this->getUser(),
            )
        );
    }

    public function getStatisticsFunction()
    {
        $settings = $this->doctrine->getManager()->getRepository('AppBundle:Settings')->find(1);
        $delay = new \DateTime();
        $delay->setTimestamp(strtotime(
            $settings->getUserConsideredAsOnlineAfterLastActivityMinutesNumber() . ' minutes ago')
        );
        $conn = $this->doctrine->getManager()->getConnection();
        return $conn->query("CALL get_statistics ('"
            . $delay->format('Y-m-d H:i:s.000000') . "', '"
            . $this->getUser()->getId() . "', '"
            . $this->getUser()->getGender()->getId() . "')")
            ->fetch();
    }

    public function getQuickSearchSidebarFormFunction(){
        return $this->getSideBarForm(QuickSearchSidebarType::class);
    }

    public function getAdminQuickSearchSidebarFormFunction(){
        return $this->getSideBarForm(AdminQuickSearchSidebarType::class);
    }

    public function getSideBarForm($type){
        return $this->container->get('form.factory')
            ->create($type)
            ->handleRequest($this->requestStack->getCurrentRequest())
            ->createView();
    }

    public function getFooterBlocksFunction()
    {
        return $this->doctrine
            ->getManager()
            ->getRepository('AppBundle:FooterHeader')
            ->findAll()
        ;
    }

    public function getZodiacFunction($date = ""){
        $zodiac[356] = "גדי";
        $zodiac[326] = "קשת";
        $zodiac[296] = "עקרב";
        $zodiac[266] = "מאזניים";
        $zodiac[235] = "בתולה";
        $zodiac[203] = "אריה";
        $zodiac[172] = "סרטן";
        $zodiac[140] = "תאומים";
        $zodiac[111] = "שור";
        $zodiac[78]  = "טלה";
        $zodiac[51]  = "דגים";
        $zodiac[20]  = "דלי";
        $zodiac[0]   = "גדי";
        if (!$date) $date = time();
        $dayOfTheYear = date("z",$date);
        $isLeapYear = date("L",$date);
        if ($isLeapYear && ($dayOfTheYear > 59)) $dayOfTheYear = $dayOfTheYear - 1;
        foreach($zodiac as $day => $sign) if ($dayOfTheYear > $day) break;
        return $sign;
    }

    public function getSeoFunction(){
        return $this->doctrine->getRepository('AppBundle:Seo')->findOneByPage('homepage');
    }

    public function imageExistFunction($url){
        return file_exists($url);
    }

    public function getUser()
    {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    public function getName()
    {
        return 'app_extension';
    }

    public function getDistanceFunction($user1,$user2){
        $distance = 0;
        if($user1->getLatitude() != null and $user1->getLongitude() != null and $user2->getLatitude() != null and $user2->getLongitude() != null and $user1->getId() != $user2->getId()) {
            $conn = $this->doctrine->getEntityManager()->getConnection();
            $sql = "SELECT get_distance(" . $user1->getLatitude() . "," . $user1->getLongitude() . "," . $user2->getLatitude() . "," . $user2->getLongitude() . ") as distance";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $distance = $stmt->fetchAll();
            $distance = number_format($distance[0]['distance'], 2, '.', '');
        }
        return $distance;
    }
}

?>