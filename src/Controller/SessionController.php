<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Form\ProgramTypeForm;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findAll();
        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
            'sessions' => $sessions
            ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();
        return $this->render('session/show.html.twig', [
        'controller_name' => 'SessionController',
        'session' => $session,
        'students' => $students
        ]);
    }


}

