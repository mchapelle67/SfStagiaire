<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleTypeForm;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository,): Response
    {
        // créer liste des modules
        $modules = $moduleRepository->findAll();

        return $this->render('module/index.html.twig', [
            'controller_name' => 'ModuleController',
            'modules' => $modules
        ]);
    }

}

// gère module et category