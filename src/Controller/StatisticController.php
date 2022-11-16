<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    #[Route('/statistic', name: 'statistic')]
    public function statisctics(): Response
    {
        return $this->render('back/statistic/Statistics.html.twig', [
            
        ]);
    }
}
