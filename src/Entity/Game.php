<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\GameController;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:game:item']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:game:collection']],
        ),
        new Post(
            uriTemplate: '/games',
            controller: GameController::class,
            name: 'game_post'
        )
    ]
)]
#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PlayerOnGame::class, orphanRemoval: true)]
    #[Groups(['read:game:item', 'read:game:collection'])]
    private Collection $players;

    #[ORM\Column]
    private ?bool $isFinish = null;


    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection<int, PlayerOnGame>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerOnGame $playerOnGame): self
    {
        if (!$this->players->contains($playerOnGame)) {
            $this->players->add($playerOnGame);
            $playerOnGame->setGame($this);
        }

        return $this;
    }

    public function removePlayer(PlayerOnGame $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }


    public function getIsFinish(): ?bool
    {
        return $this->isFinish;
    }

    public function setIsFinish(bool $isFinish): self
    {
        $this->isFinish = $isFinish;

        return $this;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'isFinish' => $this->getIsFinish(),
            'players' => $this->getPlayers()->map(fn(PlayerOnGame $playerOnGame) => $playerOnGame->toArray())->toArray()
        ];
    }
}
