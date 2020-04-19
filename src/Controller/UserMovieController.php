<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserMoviePreferenceRepository;
use App\Service\MoviesInfoProvider;
use App\Service\UserMoviePreferencesManager;
use App\Service\UserMovieRecommendationsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserMovieController extends AbstractController
{
    public function postUserMoviePreferenceAction(
        Request $request,
        UserMoviePreferencesManager $userMoviePreferencesManager
    ): Response {
        $requestData = json_decode($request->getContent(), true);
        if (($userId = $requestData['user_id']) === null) {
            throw new BadRequestHttpException();
        }
        if (($movies = $requestData['movies']) === null) {
            throw new BadRequestHttpException();
        }

        $userMoviePreference = $userMoviePreferencesManager->saveUserMoviePreferences($userId, $movies);

        return $this->json([
            'status' => true,
            'movies' => $userMoviePreference->getMovies()
        ]);
    }

    public function getUserMovieRecommendationsAction(
        Request $request,
        string $userId,
        UserMovieRecommendationsProvider $userMovieRecommendationsManager,
        UserMoviePreferenceRepository $userMoviePreferenceRepository,
        MoviesInfoProvider $moviesInfoProvider
    ): Response {
        $limit = $request->query->getInt('limit', 5);

        $userMoviePreference = $userMoviePreferenceRepository->find($userId);
        if ($userMoviePreference === null) {
            return $this->json([
                'status' => false,
                'message' => 'Нет информации о предпочтениях пользователя'
            ]);
        }

        $recommendations = $userMovieRecommendationsManager->getRecommendations($userMoviePreference, $limit);
        if ($recommendations === []) {
            $recommendations = $moviesInfoProvider->getRecommendations($userMoviePreference, $limit);
        }

        return $this->json([
            'status' => true,
            'recommendations' => $recommendations,
        ]);
    }
}
