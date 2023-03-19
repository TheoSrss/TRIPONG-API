<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PlayerOnGameController;
use App\Repository\PlayerOnGameRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/playerOnGame',
            controller: playerOnGameController::class,
            name: 'playerOnGame_post'
        )
    ]
)]
#[ORM\Entity(repositoryClass: PlayerOnGameRepository::class)]
class PlayerOnGame implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:game:item','read:game:collection'])]
    private ?Player $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:player:item','read:player:collection',])]
    private ?Game $game = null;


    #[ORM\Column(nullable: true)]
    #[Groups(['read:player:item','read:player:collection','read:game:item','read:game:collection'])]
    private ?int $ranking = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): self
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'player' => $this->getPlayer()->toArray(),
            'game' => $this->getGame()->toArray(),
            'ranking' => $this->getRanking(),
        ];
    }

}
