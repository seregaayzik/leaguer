<?php

namespace App\Entity;

use App\Repository\StatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatRepository::class)]
class Stat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'stat', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\Column]
    private ?int $goal = null;

    #[ORM\Column]
    private ?int $goalc = null;

    #[ORM\Column]
    private ?int $gamesQuantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(int $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    public function getGoalc(): ?int
    {
        return $this->goalc;
    }

    public function setGoalc(int $goalc): static
    {
        $this->goalc = $goalc;

        return $this;
    }

    public function getGamesQuantity(): ?int
    {
        return $this->gamesQuantity;
    }

    public function setGamesQuantity(int $gamesQuantity): static
    {
        $this->gamesQuantity = $gamesQuantity;

        return $this;
    }
}
