<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
            'students' => $students
        ]);
    }
    
    #[Route('/student/{id}', name: 'show_student')]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
        'controller_name' => 'StudentController',
        'student' => $student   
        ]);
    }
    
}
