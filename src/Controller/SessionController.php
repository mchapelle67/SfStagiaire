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

    #[Route('/session/{id}', name: 'show_session', requirements: ['id' => '\d+'])]
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
        // On récupère tous les programs associés à la session
        $programs = $entityManager->getRepository(Program::class)->findBy(['session' => $session]);

        // On supprime chaque program associé
        foreach ($programs as $program) {
            $entityManager->remove($program);
        }

        // On supprime la session
        $entityManager->remove($session);
        $entityManager->flush();

        // Redirection vers la liste des sessions
        return $this->redirectToRoute('app_session');
    }

    #[Route('/session/new', name: 'new_session')]
    public function newSession (Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Session();

        // Création du formulaire
        $form = $this->createForm(SessionFormType::class, $session);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session); // persist fonctionne aussi en édition
            $entityManager->flush();

            return $this->redirectToRoute('new_session'); 
        }

        return $this->render('session/new.html.twig', [
            'formAddsession' => $form->createView()
        ]);

        // Redirection vers la liste des sessions
        return $this->redirectToRoute('app_session');
    }

    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function editSession (Session $session, Request $request, EntityManagerInterface $entityManager): Response
    {
        // On récupère l'ID de la session 
        $id = $session->getId();
        $session = $entityManager->getRepository(Session::class)->find($id);

        // Création du formulaire
        $form = $this->createForm(SessionFormType::class, $session);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session); // persist fonctionne aussi en édition
            $entityManager->flush();

            return $this->redirectToRoute('show_session', ['id' => $id]); 
        }

        return $this->render('session/new.html.twig', [
            'formAddsession' => $form->createView()
        ]);

        // Redirection vers la liste des sessions
        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
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

    #[Route('/module/{moduleId}/session/{sessionId}/remove', name: 'remove_module_session')]
    public function removeModuleFromSession(int $moduleId, int $sessionId, EntityManagerInterface $entityManager): Response 
    {
        $program = $entityManager->getRepository(Program::class)->findOneBy(['module' => $moduleId, 'session' => $sessionId]);

        $module = $entityManager->getRepository(Module::class)->find($moduleId);
        $session = $entityManager->getRepository(\App\Entity\Session::class)->find($sessionId);

        $module->removeProgram($program);
        $session->removeProgram($program);
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