<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:player:item']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:player:collection']],
        ),
    ]
)]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:player:item','read:player:collection','read:game:item','read:game:collection'])]
    private ?string $name = null;


    #[ORM\OneToMany(mappedBy: 'player', targetEntity: PlayerOnGame::class, orphanRemoval: true)]
    #[Groups(['read:player:item','read:player:collection'])]
    private Collection $games;

    public function __construct()
    {
        $this->gamesPlayed = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

//    /**
//     * @return Collection<int, Game>
//     */
//    public function getGamesPlayed(): Collection
//    {
//        return $this->gamesPlayed;
//    }
//
//    public function addGamesPlayed(Game $gamesPlayed): self
//    {
//        if (!$this->gamesPlayed->contains($gamesPlayed)) {
//            $this->gamesPlayed->add($gamesPlayed);
//            $gamesPlayed->addPlayer($this);
//        }
//
//        return $this;
//    }
//
//    public function removeGamesPlayed(Game $gamesPlayed): self
//    {
//        if ($this->gamesPlayed->removeElement($gamesPlayed)) {
//            $gamesPlayed->removePlayer($this);
//        }
//
//        return $this;
//    }
//
    /**
     * @return Collection<int, PlayerOnGame>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(PlayerOnGame $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setPlayer($this);
        }

        return $this;
    }

    public function removeGame(PlayerOnGame $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getPlayer() === $this) {
                $game->setPlayer(null);
            }
        }

        return $this;
    }

}
