<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Student;
use App\Form\ProgramTypeForm;
use App\Form\SessionFormType;
use Doctrine\ORM\EntityManager;
use App\Form\StudentSessionTypeForm;
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

     #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        // on supprime la session
        $entityManager->remove($session);
        $entityManager->flush();

        // puis on redirige vers la liste des étudiants
        return $this->redirectToRoute('app_session');
    }

    #[Route('/student/{studentId}/session/{sessionId}/add', name: 'add_student_session')]
    public function add(int $studentId, int $sessionId, EntityManagerInterface $entityManager): Response
    {
        $students = $entityManager->getRepository(Student::class)->findAll();
        $session = $entityManager->getRepository(Session::class)->find($sessionId);

        // Session a une méthode addStudent() et Student a addSession()
        $session->addStudent($students);
        $entityManager->persist($session);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }
    

    #[Route('/session/add', name: 'new_session')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {        
    
        // création formulaire d'ajout
        $session = new Session();
        $form = $this->createForm(SessionFormType::class, $session);

        // gestion de la requête
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données du formulaire
            $session = $form->getData();
            // on les envois dans la base de données (equivalent de prepare et execute)
            $entityManager->persist($session);
            $entityManager->flush();
            
            // puis on redirige vers la liste des étudiants
            return $this->redirectToRoute('app_session/add');
        }
        
        return $this->render('session/add/index.html.twig', [
            'controller_name' => 'session/addController',
            'formAddsession' => $form
        ]);
    }
}