<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserMoviePreferenceRepository;
use App\Service\MoviesInfoProvider;
use App\Service\Security\RequestSignChecker;
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

    public function getUserMovieRecommendationsAction(
        Request $request,
        string $userId,
        UserMovieRecommendationsProvider $userMovieRecommendationsManager,
        UserMoviePreferenceRepository $userMoviePreferenceRepository,
        MoviesInfoProvider $moviesInfoProvider,
        RequestSignChecker $requestSignChecker
    ): Response {
        $requestSignChecker->checkSign($request);

        $limit = $request->query->getInt('limit', 5);

        $userMoviePreference = $userMoviePreferenceRepository->find($userId);
        if ($userMoviePreference === null) {
            return $this->json([
                'status' => false,
                'message' => 'Нет информации о предпочтениях пользователя'
            ], Response::HTTP_NOT_FOUND);
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
