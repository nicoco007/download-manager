<?php


namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Navigation
{
    /** @var array */
    private $pages = [];

    public function __construct(AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage)
    {
        if ($tokenStorage->getToken() !== null && $authChecker->isGranted('ROLE_ADMIN'))
            $this->setupAdminPages();
        else
            $this->setupStandardPages();
    }

    function setupStandardPages()
    {
        $this->pages = [
            'left' => [
                'home' => 'Home'
            ],
            'right' => [
                'login' => 'Sign In'
            ]
        ];
    }

    function setupAdminPages()
    {
        $this->pages = [
            'left' => [
                'admin_home' => 'Home',
                'projects' => 'Projects'
            ],
            'right' => [
                'logout' => 'Sign Out'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }
}