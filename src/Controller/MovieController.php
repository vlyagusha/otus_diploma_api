<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\MoviesInfoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    public function getListAction(
        Request $request,
        string $slug,
        MoviesInfoProvider $moviesInfoProvider
    ): Response {
        return $this->json([
            'status' => true,
            'movies' => $moviesInfoProvider->getList($slug),
        ]);
    }
}
