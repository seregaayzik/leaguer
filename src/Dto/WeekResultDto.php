<?php

namespace App\Dto;

class WeekResultDto {
    public array $teamStat;
    public iterable $games;
    
    public function __construct(array $teamStat, iterable $games) {
        $this->teamStat = $teamStat;
        $this->games = $games;
    }
}
