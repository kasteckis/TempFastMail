<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdsTxtController extends AbstractController
{
    #[Route('/ads.txt', name: 'app_ads_txt', methods: ['GET'])]
    public function adsTxt(): Response
    {
        return new Response($_ENV['GOOGLE_ADS_ID']);
    }
}
