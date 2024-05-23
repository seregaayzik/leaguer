<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    public ?Team $phomeTeam = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    public ?Team $guestTeam = null;

    #[ORM\ManyToOne(inversedBy: 'games', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Week $week = null;

    #[ORM\Column]
    public int $homeScore = 0;

    #[ORM\Column]
    public int $guestScore = 0;

    #[ORM\Column]
    private ?bool $finished = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->phomeTeam;
    }

    public function setHomeTeam(Team $phomeTeam): static
    {
        $this->phomeTeam = $phomeTeam;

        return $this;
    }

    public function getGuestTeam(): ?Team
    {
        return $this->guestTeam;
    }

    public function setGuestTeam(Team $guestTeam): static
    {
        $this->guestTeam = $guestTeam;

        return $this;
    }

    public function getWeek(): ?Week
    {
        return $this->week;
    }

    public function setWeek(?Week $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(int $homeScore): static
    {
        if($homeScore < 0 || $homeScore > 100){
            throw new \InvalidArgumentException("Incorrect Score Value. $homeScore given");
        }
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getGuestScore(): ?int
    {
        return $this->guestScore;
    }

    public function setGuestScore(int $guestScore): static
    {
        if($guestScore < 0 || $guestScore > 100){
            throw new \InvalidArgumentException("Incorrect Score Value. $guestScore given");
        }
        $this->guestScore = $guestScore;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): static
    {
        $this->finished = $finished;

        return $this;
    }
    public function isLastGameOfWeek(): bool
    {
        $games = $this->getWeek()->getGames();
        $lastgame = end($games);
        return $this->getId() === $lastgame->getId();
    }
}
