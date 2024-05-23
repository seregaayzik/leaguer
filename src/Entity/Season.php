<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Week>
     */
    #[ORM\OneToMany(targetEntity: Week::class, mappedBy: 'season', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $weeks;

    #[ORM\Column(length: 16)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;


    #[ORM\Column]
    private int $finishedGames = 0;

    public function __construct()
    {
        $this->weeks = new ArrayCollection();
        $this->datetime = new \DateTime();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeek(int $weekId): ?Week
    {
        return $this->weeks[$weekId] ?? null;
    }
    
    /**
     * @return Collection<int, Week>
     */
    public function getWeeks(): Collection
    {
        return $this->weeks;
    }

    public function addWeek(Week $week): static
    {
        if (!$this->weeks->contains($week)) {
            $this->weeks->add($week);
            $week->setSeason($this);
        }

        return $this;
    }

    public function removeWeek(Week $week): static
    {
        if ($this->weeks->removeElement($week)) {
            // set the owning side to null (unless already changed)
            if ($week->getSeason() === $this) {
                $week->setSeason(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
    
    public function getTeams(): array{
        $teams = [];
        foreach($this->weeks as $week){
            foreach($week->getGames() as $game){
                if(!isset($teams[$game->getHomeTeam()->getId()])){
                    $teams[$game->getHomeTeam()->getId()] = $game->getHomeTeam();
                }
            }
        }
        return $teams;
    }

    public function getCountOfCames(): int{
        $games = 0;
        foreach($this->weeks as $week){
            $games += count($week->getGames());
        }
        return $games;
    }
    
    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getFinishedGames(): ?int
    {
        return $this->finishedGames;
    }

    public function increaseFinishedGames(): static
    {
        $this->finishedGames += 1;

        return $this;
    }
    
    public function isSeasonFinished():bool{
        return $this->finishedGames === $this->getCountOfCames();
    }
}
