<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/api/v1/login_check")
     * @Route("/api/v1/login")
     */
    public function loginCheck()
    {
        throw $this->createNotFoundException();
    }
}
