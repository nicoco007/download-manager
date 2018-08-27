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
            'home' => 'Home',
            'login' => 'Sign in'
        ];
    }

    function setupAdminPages()
    {
        $this->pages = [
            'admin_home' => 'Home',
            'files' => 'Files',
            'logout' => 'Sign out'
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