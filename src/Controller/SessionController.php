<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Program;
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
    public function show(Session $session, SessionRepository $sessionRepository): Response
    {
    
        $nonInscrits = $sessionRepository->findStudentNoRegister($session->getId());
        $nonModules = $sessionRepository->findModuleNotInSession($session->getId());

        return $this->render('session/show.html.twig', [
            'controller_name' => 'SessionController',
            'session' => $session,
            'nonInscrits' => $nonInscrits,
            'nonModules' => $nonModules
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


    #[Route('/student/{studentId}/session/{sessionId}/remove', name: 'remove_student_session')]
    public function removeStudentFromSession(int $studentId, int $sessionId, EntityManagerInterface $entityManager): Response 
    {

        $student = $entityManager->getRepository(Student::class)->find($studentId);
        $session = $entityManager->getRepository(\App\Entity\Session::class)->find($sessionId);

        $session->removeStudent($student);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }

    #[Route('/module/{moduleId}/session/{sessionId}/remove', name: 'remove_student_session')]
    public function removeModuleFromSession(int $moduleId, int $sessionId, EntityManagerInterface $entityManager): Response 
    {

        $module = $entityManager->getRepository(Module::class)->find($moduleId);
        $session = $entityManager->getRepository(\App\Entity\Session::class)->find($sessionId);

        $session->removeModule($module);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }


    #[Route('/student/{studentId}/session/{sessionId}/add', name: 'add_student_session')]
    public function addStudentInSession(int $studentId, int $sessionId, EntityManagerInterface $entityManager): Response 
    {

        $student = $entityManager->getRepository(Student::class)->find($studentId);
        $session = $entityManager->getRepository(\App\Entity\Session::class)->find($sessionId);

        $session->addStudent($student);
        $student->addSession($session);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }


    #[Route('/module/{moduleId}/session/{sessionId}/add', name: 'add_module_session')]
    public function addModuleInSession(int $moduleId, int $sessionId, EntityManagerInterface $entityManager, Request $request): Response 
    {
        $module = $entityManager->getRepository(Module::class)->find($moduleId);
        $session = $entityManager->getRepository(Session::class)->find($sessionId);
        $nbDay = $request->request->get('nbDay');

        // Créer un nouveau Program qui relie le module à la session
        $program = new Program();
        $program->setModule($module);
        $program->setSession($session);
        $program->setNbDay($nbDay);

        $entityManager->persist($program);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }
}