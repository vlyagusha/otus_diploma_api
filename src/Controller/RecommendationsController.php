<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserMoviePreferenceRepository;
use App\Service\Security\RequestSignChecker;
use App\Service\UserMovieRecommendationsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RecommendationsController extends AbstractController
{
    public function getUserRecommendationsAction(
        Request $request,
        string $userId,
        UserMovieRecommendationsProvider $userMovieRecommendationsManager,
        UserMoviePreferenceRepository $userMoviePreferenceRepository,
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

        return $this->json([
            'status' => true,
            'recommendations' => $recommendations,
        ]);
    }
}
