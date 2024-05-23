<?php

namespace App\Entity;

use App\Entity\Team;

class TeamStatistic {
    public Team $team;
    public int $points = 0;
    public int $win = 0;
    public int $draw = 0;
    public int $loss = 0;
    public int $diff = 0;
    public int $games = 0;
    public int $prediction = 0;
    public Game $game;
    public function __construct(Team $team) {
        $this->team = $team;
    }
    public function addPoints($value):void{
        $this->points += $value;
    }
    public function addWin($value):void{
        $this->win += $value;
    }
    public function addDraw($value):void{
        $this->draw += $value;
    }
    public function addLoss($value):void{
        $this->loss += $value;
    }
    public function addDiff($value):void{
        $this->diff += $value;
    }
    public function addGames($value):void{
        $this->games += $value;
    }
    public function getTeam(): Team {
        return $this->team;
    }

    public function getPoints(): int {
        return $this->points;
    }

    public function getWin(): int {
        return $this->win;
    }

    public function getDraw(): int {
        return $this->draw;
    }

    public function getLoss(): int {
        return $this->loss;
    }

    public function getDiff(): int {
        return $this->diff;
    }

    public function getGames(): int {
        return $this->games;
    }
    
    public function calculatePrediction():void{
        $totalGames = $this->win + $this->loss;
        if ($totalGames == 0) {
            $winLossRatio = 0; 
        } else {
            $winLossRatio = $this->win / $totalGames;
        }
        if ($this->games == 0) {
            $pointsRatio = 0;
        } else {
            $pointsRatio = $this->points / ($this->games * 3);
        }
        $winProbability = ($winLossRatio + $pointsRatio) / 2;
        $this->prediction = round($winProbability * 100);
    }
}
