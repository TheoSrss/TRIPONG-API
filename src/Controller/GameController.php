<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\PlayerOnGame;
use App\Repository\GameRepository;
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

class GameController extends AbstractController
{

    #[Route(
        path: '/games',
        name: 'game_post',
        defaults: [
            '_api_resource_class' => Game::class
        ],
        methods: ['POST'],
    )]
    public function post(
        Request                $request,
        EntityManagerInterface $entityManager,
        PlayerRepository       $playerRepository,
        GameRepository         $gameRepository
    ): JsonResponse
    {
        try {

            $game = new Game();
            $game->setIsFinish(false);
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $playersIds = $serializer->decode($request->getContent(), 'json')['players'];

            foreach ($playersIds as $player) {
                $player = $playerRepository->find($player);
                $playerOnGame = new PlayerOnGame();
                $playerOnGame->setPlayer($player);
                $playerOnGame->setGame($game);
                $entityManager->persist($playerOnGame);
            }
            $entityManager->persist($game);
            $entityManager->persist($game);
            $entityManager->flush();
            return new JsonResponse($game->getId(), Response::HTTP_CREATED);
        }catch (\Exception $e){
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
