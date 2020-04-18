<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserMoviePreference;
use App\Repository\UserMoviePreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserMovieController extends AbstractController
{
    public function postUserMoviePreferenceAction(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $requestData = json_decode($request->getContent(), true);
        if (($userId = $requestData['user_id']) === null) {
            throw new BadRequestHttpException();
        }
        if (($movieId = $requestData['movie_id']) === null) {
            throw new BadRequestHttpException();
        }

        $userMoviePreference = $entityManager->getRepository(UserMoviePreference::class)->find($userId);
        if ($userMoviePreference === null) {
            $userMoviePreference = new UserMoviePreference();
            $userMoviePreference->setUserId($userId);

            $entityManager->persist($userMoviePreference);
        }
        $userMoviePreference->addMovie($movieId);

        $entityManager->flush();

        return $this->json([
            'status' => true,
            'movies' => $userMoviePreference->getMovies()
        ]);
    }

    public function getUserMovieRecommendationsAction(
        Request $request,
        string $userId,
        UserMoviePreferenceRepository $userMoviePreferenceRepository
    ): Response {
        $limit = $request->query->getInt('limit', 5);

        $userMoviePreference = $userMoviePreferenceRepository->find($userId);
        if ($userMoviePreference === null) {
            return $this->json([
                'status' => false,
                'likes' => null,
            ]);
        }

        $likes = $userMoviePreferenceRepository->getLikes($userMoviePreference);

        return $this->json([
            'status' => true,
            'likes' => array_slice($likes, 0, $limit),
        ]);
    }
}
