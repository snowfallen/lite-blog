<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'page_')]
class HomeController extends AbstractController
{
    #[Route('/', name: "root")]
    public function redirecting(){
        return $this->redirectToRoute('page_home');
    }
    #[Route('/home', name: "home")]
    public function home()
    {
        return $this->render('home/index.html.twig', [
        ]);
    }
}
