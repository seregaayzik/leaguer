<?php
namespace App\Service;


use App\Entity\Season;
use App\Entity\TeamStatistic;
use App\Entity\Team;

class StatsService {
    private array $statistic = [];
    
    private const WEEKS_TO_PREDICTION = 3;
    
    private function getTeamStatistic(Team $team):TeamStatistic {
        if(!isset($this->statistic[$team->getId()])){
            $this->statistic[$team->getId()] = new TeamStatistic($team);
        }
        return $this->statistic[$team->getId()];
    }
    private function initStatistic(Season $season){
        $allTeams = $season->getTeams();
        foreach($allTeams as $team){
            $this->statistic[$team->getId()] = new TeamStatistic($team);
        }
    }
   
    public function getLeagueStat(Season $season, int $currentWeek):array{
        $this->statistic = [];
        if(!$season || !$season->getWeek($currentWeek)){
            throw new \InvalidArgumentException('Invalid Week ID');
        }
        $this->initStatistic($season);
        
        for($wi = 0;$wi <= $currentWeek; $wi++){
            $week = $season->getWeek($wi);
            foreach($week->getGames() as $game){
                //Home Team
                if ($game->getHomeScore() > $game->getGuestScore()) {
                    $this->getTeamStatistic($game->getHomeTeam())->addPoints(3);
                    $this->getTeamStatistic($game->getHomeTeam())->addWin(1);
                    $this->getTeamStatistic($game->getGuestTeam())->addLoss(1);
                } 
                $homeTeamGoalDifference = $game->getHomeScore() - $game->getGuestScore();
                $this->getTeamStatistic($game->getHomeTeam())->addDiff($homeTeamGoalDifference);
                //Guest Team
                
                if ($game->getGuestScore() > $game->getHomeScore()) {
                    $this->getTeamStatistic($game->getGuestTeam())->addPoints(3);
                    $this->getTeamStatistic($game->getGuestTeam())->addWin(1);
                    $this->getTeamStatistic($game->getHomeTeam())->addLoss(1);
                } 
                if ($game->getHomeScore() === $game->getGuestScore()) {
                    $this->getTeamStatistic($game->getGuestTeam())->addPoints(1);
                    $this->getTeamStatistic($game->getGuestTeam())->addDraw(1);
                    $this->getTeamStatistic($game->getHomeTeam())->addPoints(1);
                    $this->getTeamStatistic($game->getHomeTeam())->addDraw(1);
                }
                $guestTeamGoalDifference = $game->getGuestScore() - $game->getHomeScore();
                $this->getTeamStatistic($game->getGuestTeam())->addDiff($guestTeamGoalDifference);
                
                $this->getTeamStatistic($game->getHomeTeam())->addGames(1);
                $this->getTeamStatistic($game->getGuestTeam())->addGames(1);
                
            }
        }
        if($currentWeek > self::WEEKS_TO_PREDICTION){
            foreach($this->statistic as $statistic){
                $statistic->calculatePrediction();
            }
        }
        usort($this->statistic, function ($a, $b) {
            $res = $b->getPoints() <=> $a->getPoints();
            if ($res === 0) {
                $res = $b->getDiff() <=> $a->getDiff();
            }
            return $res;
        });
        return $this->statistic;
    }
}
