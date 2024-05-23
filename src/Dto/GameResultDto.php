<?php
namespace App\Dto;

use App\Entity\Team;

class GameResultDto {
   public int $homeScore = 0;   
   public int $guestScore = 0;
   public Team $homeTeam;
   public Team $guestTeam;
   public function __construct(int $homeScore, int $guestScore, Team $homeTeam, Team $guestTeam) {
       $this->homeScore = $homeScore;
       $this->guestScore = $guestScore;
       $this->homeTeam = $homeTeam;
       $this->guestTeam = $guestTeam;
   }
}
