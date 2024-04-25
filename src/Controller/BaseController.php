<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    protected $user;
    protected $request;

    public function __construct(protected Security $security, protected RequestStack $requestStack)
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

	protected function validAuth(): ?RedirectResponse
    {
        $route = $this->request->attributes->get('_route');
        if (!$this->security->isGranted('ROLE_USER') && $route !== '/' && $route !== 'login' && $route !== 'register') {
            return $this->redirectToRoute('app_home');
        }
        return null;
    }
}
