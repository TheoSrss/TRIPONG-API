<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $gamesPlayed;

    public function __construct()
    {
        $this->gamesPlayed = new ArrayCollection();
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

    /**
     * @return Collection<int, Game>
     */
    public function getGamesPlayed(): Collection
    {
        return $this->gamesPlayed;
    }

    public function addGamesPlayed(Game $gamesPlayed): self
    {
        if (!$this->gamesPlayed->contains($gamesPlayed)) {
            $this->gamesPlayed->add($gamesPlayed);
            $gamesPlayed->addPlayer($this);
        }

        return $this;
    }

    public function removeGamesPlayed(Game $gamesPlayed): self
    {
        if ($this->gamesPlayed->removeElement($gamesPlayed)) {
            $gamesPlayed->removePlayer($this);
        }

        return $this;
    }
}
