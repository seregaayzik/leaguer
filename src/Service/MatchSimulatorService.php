<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\StatRepository;
use MathPHP\Probability\Distribution\Continuous\Normal;
use App\Dto\GameResultDto;
class MatchSimulatorService {
    
    private StatRepository $_statRepository;
           
    private const L = 1;
    
    public function __construct(
        StatRepository $statRepository
    ) {
        $this->_statRepository = $statRepository;
    }

    public function simulateMatchResult(Team $homeTeam, Team $guestTeam): GameResultDto{
        if(!$homeTeam->getStat() || !$guestTeam->getStat()){
            throw new \InvalidArgumentException('Leak of statistic data');
        }
        $summaryAvgGoalsPerGame = $this->_statRepository->getAvgGoals();
        $homeTeamAvgGoals = $homeTeam->getStat()->getGoal()/$homeTeam->getStat()->getGamesQuantity();
        $homeTeamAvgGoalsc = $homeTeam->getStat()->getGoalc()/$homeTeam->getStat()->getGamesQuantity();
        $guestTeamAvgGoals = $guestTeam->getStat()->getGoal()/$guestTeam->getStat()->getGamesQuantity();
        $guestTeamAvgGoalsc = $guestTeam->getStat()->getGoalc()/$guestTeam->getStat()->getGamesQuantity();
        $homeTeamAvgGoalsStrenght = $homeTeamAvgGoals/$summaryAvgGoalsPerGame;
        $homeTeamAvgGoalsDefenceWeakness = $homeTeamAvgGoalsc/$summaryAvgGoalsPerGame;
        $guestTeamAvgGoalsStrenght = $guestTeamAvgGoals/$summaryAvgGoalsPerGame;
        $guestTeamAvgGoalsDefenceWeakness = $guestTeamAvgGoalsc/$summaryAvgGoalsPerGame;        
        $homeTeamEstimatedScore = $homeTeamAvgGoalsStrenght * $homeTeamAvgGoalsDefenceWeakness * $summaryAvgGoalsPerGame;
        $guestTeamEstimatedScore = $guestTeamAvgGoalsStrenght * $guestTeamAvgGoalsDefenceWeakness * $summaryAvgGoalsPerGame;
        $normalHomeScoreDistribution = new Normal($homeTeamEstimatedScore, self::L);
        $normalGuestScoreDistribution = new Normal($guestTeamEstimatedScore, self::L);
        $homeFinalScore = $normalHomeScoreDistribution->rand();
        $guestFinalScore = $normalGuestScoreDistribution->rand();
        $homeFinalScore = $homeFinalScore >= 0?round($homeFinalScore):0;
        $guestFinalScore = $guestFinalScore >= 0?round($guestFinalScore):0;
        return new GameResultDto($homeFinalScore,$guestFinalScore,$homeTeam,$guestTeam);
    }
}
