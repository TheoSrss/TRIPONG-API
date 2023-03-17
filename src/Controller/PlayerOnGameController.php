<?php

namespace App\Controller;

use App\Entity\PlayerOnGame;
use App\Repository\GameRepository;
use App\Repository\PlayerOnGameRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PlayerOnGameController extends AbstractController
{
    #[Route(
        path: '/playersOnGame',
        name: 'playersOnGame_post',
        defaults: [
            '_api_resource_class' => PlayerOnGame::class
        ],
        methods: ['PUT'],
    )]
    public function put(
        Request                $request,
        EntityManagerInterface $entityManager,
        PlayerRepository       $playerRepository,
        GameRepository         $gameRepository,
        PlayerOnGameRepository $playerOnGameRepository
    ): JsonResponse
    {
        try {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $data = $serializer->decode($request->getContent(), 'json');

            $game = $gameRepository->find($data['game']);

            if (!$game->getIsFinish()) {

                $players = $data['players'];
                foreach ($players as $index => $player) {
                    $playerOnGame = $playerOnGameRepository->findOneBy([
                        'player' => $player,
                        'game' => $game
                    ]);
                    $playerOnGame->setRanking($index + 1);
                    $entityManager->persist($playerOnGame);
                }
                $game->setIsFinish(true);
                $entityManager->flush();
                return new JsonResponse(Response::HTTP_CREATED);
            }
            return new JsonResponse(Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
