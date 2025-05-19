<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    
    #[Route('/student', name: 'show_student')]
    public function showStudent(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findOneBy();
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
            'students' => $students
        ]);
    }
    
}
