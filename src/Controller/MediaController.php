<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

// Only for this purpose. Media should be served by nginx directly
class MediaController extends AbstractController
{
    #[Route('/media/{pathToMediaFile}', name: 'process_media', requirements: ["pathToMediaFile" => "[\d\D]*"])]
    public function processMedia(string $pathToMediaFile): BinaryFileResponse
    {
        $pathToMediaFile = sprintf('%s/public/media/%s', $this->getParameter('kernel.project_dir'), $pathToMediaFile);
        $response = new BinaryFileResponse($pathToMediaFile);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }
}
