<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentTypeForm;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(StudentRepository $studentRepository, Request $request, EntityManagerInterface $entityManager): Response
    {        
        // créer liste des étudiants
        $students = $studentRepository->findAll();

        // création formulaire d'ajout
        $student = new Student();
        $form = $this->createForm(StudentTypeForm::class, $student);

        // gestion de la requête
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données du formulaire
            $student = $form->getData();
            // on les envois dans la base de données (equivalent de prepare et execute)
            $entityManager->persist($student);
            $entityManager->flush();
            
            // puis on redirige vers la liste des étudiants
            return $this->redirectToRoute('app_student');
        }
        
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
            'students' => $students,
            'formAddStudent' => $form
        ]);
    }
    
    // voir le detail d'un étudiant
    #[Route('/student/{id}', name: 'show_student')]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
        'controller_name' => 'StudentController',
        'student' => $student   
        ]);
    }

    #[Route('/student/{id}/delete', name: 'delete_student')]
    public function delete(Student $student, EntityManagerInterface $entityManager): Response
    {
        // on supprime l'étudiant
        $entityManager->remove($student);
        $entityManager->flush();

        // puis on redirige vers la liste des étudiants
        return $this->redirectToRoute('app_student');
    }


    #[Route('/student/{id}/edit', name: 'edit_student')]
    public function update(EntityManagerInterface $entityManager, Student $student, Request $request): Response
    {
        // on récupère l'étudiant à modifier
        $id = $student->getId();
        $student = $entityManager->getRepository(Student::class)->find($id);

        $form = $this->createForm(StudentTypeForm::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données du formulaire
            $student = $form->getData();
            // on les envois dans la base de données (equivalent de prepare et execute)
            $entityManager->persist($student);
            $entityManager->flush();
            
            // puis on redirige vers la liste des étudiants
            return $this->redirectToRoute('app_student');
        }

        return $this->render('student/update.html.twig', [
            'controller_name' => 'StudentController',
            'student' => $student,
            'formEditStudent' => $form
        ]);
    }
}
