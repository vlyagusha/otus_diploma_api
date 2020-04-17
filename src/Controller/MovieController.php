<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\MoviesInfoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MovieController extends AbstractController
{
    public function getListAction(
        Request $request,
        string $slug,
        MoviesInfoProvider $moviesInfoProvider
    ): Response {
        $page = $request->query->getInt('page', 1);

        return $this->json([
            'status' => true,
            'movies' => $moviesInfoProvider->getList($slug, $page),
        ]);
    }
}