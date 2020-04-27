<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\Security\RequestSignChecker;
use App\Service\UserMoviePreferencesManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserMovieController extends AbstractController
{
    public function postUserMoviePreferenceAction(
        Request $request,
        UserMoviePreferencesManager $userMoviePreferencesManager,
        RequestSignChecker $requestSignChecker
    ): Response {
        $requestSignChecker->checkSign($request);

        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['user_id'])) {
            throw new BadRequestHttpException();
        }
        $userId = $requestData['user_id'];

        if (!isset($requestData['movies'])) {
            throw new BadRequestHttpException();
        }
        $movies = $requestData['movies'];

        $userMoviePreference = $userMoviePreferencesManager->saveUserMoviePreferences($userId, $movies);

        return $this->json([
            'status' => true,
            'movies' => $userMoviePreference->getMovies()
        ]);
    }
}
