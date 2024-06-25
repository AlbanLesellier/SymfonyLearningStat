<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ActorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class ActorController extends AbstractController
{
    #[Route('/actor', name: 'app_actor')]
    public function index(  
        ActorRepository $actorRepository,
        Request $request,
        ManagerRegistry $doctrine,
        LoggerInterface $logger
    ): Response
    {
        
        $actors = $actorRepository->findAll();
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
            'actors' => $actors
        ]);
    }
}
