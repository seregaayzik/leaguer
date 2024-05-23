<?php

namespace App\Service;

use App\Entity\Season;
use App\Entity\Week;
use App\Entity\Game;
use App\Repository\TeamRepository;
use App\Repository\SeasonRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\StatsService;
use App\Dto\WeekResultDto;

class LeagueService {

    private TeamRepository $_teamRepository;
    private SeasonRepository $_seasonRepository;
    private MatchSimulatorService $_matchSimultatorService;
    private EntityManagerInterface $_entityManager;
    private StatsService $_statService;
    private GameRepository $_gameRepository;
    
    public function __construct(
        TeamRepository $teamRepository,
        MatchSimulatorService $matchSimulatorService,
        EntityManagerInterface $entityManager,
        SeasonRepository $seasonRepository,
        StatsService $statService,
        GameRepository $gameRepository,
    ){
        $this->_statService = $statService;
        $this->_teamRepository = $teamRepository;
        $this->_matchSimultatorService = $matchSimulatorService;
        $this->_entityManager = $entityManager;
        $this->_seasonRepository = $seasonRepository;
        $this->_gameRepository = $gameRepository;
       
    }

    public function getGameById(int $id): Game{
        return $this->_gameRepository->findById($id);
    }
    
    public function playAll(Season $season, $refresh = false):array{
        $statisticsForAllWeeks = [];
        $countOfWeeks = count($season->getWeeks());
        for($cw = 0; $cw <= $countOfWeeks - 1; $cw ++){
            $statisticsForAllWeeks[] = $this->playWeekOfSeason($season,$cw,$refresh);
        }
        return $statisticsForAllWeeks;
    }
    
    public function getHistory(){
        return $this->_seasonRepository->getRecentSeasons();
    }
    
    public function getSeasonById(int $id): ?Season{
        return $this->_seasonRepository->find($id);
    }
    
    public function isSeasonExists(int $id): bool{
        return (bool)$this->_seasonRepository->countById($id);
    }
    
    public function playWeek(Season $season, int $week, bool $refresh = false): WeekResultDto{
        return $this->playWeekOfSeason($season,$week,$refresh);
    }
    
    public function changeGameResult(int $gameId, string $scoreType,int $score):void{
        $game = $this->_gameRepository->findOneById($gameId);     
        if(!$game){
            throw new \InvalidArgumentException('Invalid Game ID');
        }
        if($scoreType == 'homeScore'){
            $game->setHomeScore($score);
        }else if($scoreType == 'guestScore'){
            $game->setGuestScore($score);
        }else{
            throw new \InvalidArgumentException('Invalid Score Type');
        }
        $this->_entityManager->persist($game);  
        $this->_entityManager->flush($game);  
    }
    private function playWeekOfSeason(Season $season, int $weekId, bool $refresh = false):WeekResultDto{
        if(!$season || !$week = $season->getWeek($weekId) ){
            throw new \InvalidArgumentException('Invalid Week ID');
        }
        foreach($week->getGames() as $game){
            if(!$game->isFinished() || $refresh){
                $matchResult = $this->_matchSimultatorService->simulateMatchResult($game->getHomeTeam(), $game->getGuestTeam());
                $game->setHomeScore($matchResult->homeScore);
                $game->setGuestScore($matchResult->guestScore);
                if(!$game->isFinished()){
                    $season->increaseFinishedGames();
                    $game->setFinished(true);
                }
                $this->_entityManager->persist($game);  
            }
            $week->setFinished(true);
            $this->_entityManager->persist($week);
        }
        $this->_entityManager->persist($season); 
        $this->_entityManager->flush();
        return new WeekResultDto($this->_statService->getLeagueStat($season,$weekId), $week->getGames());
    }
    
    public function createNewSeason(array $teamsIds): Season{
        $teamsEntities = $this->_teamRepository->getTeamsByIds($teamsIds);
        $teams = $teamsEntities;
        $newSeasonEntity = new Season();
        $newSeasonEntity->setName("Season");
        $numTeams = count($teamsEntities);
        if ($numTeams % 2 !== 0) {
            throw new \InvalidArgumentException('Incorrect team quantity');
        }
        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;
        for ($round = 0; $round < $numRounds; $round++) {
            $week = new Week();
            $week->setName("Week #" . ($round + 1));
            $newSeasonEntity->addWeek($week);
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $homeIndex = ($round + $match) % ($numTeams - 1);
                $awayIndex = ($numTeams - 1 - $match + $round) % ($numTeams - 1);
                if ($match == 0) {
                    $awayIndex = $numTeams - 1;
                }
                $homeTeam = $teams[$homeIndex];
                $guestTeam = $teams[$awayIndex];
                $game = new Game();
                $game->setHomeTeam($homeTeam);
                $game->setGuestTeam($guestTeam);
                $week->addGames($game);
            }
            $firstTeam = array_shift($teams);
            array_push($teams, $firstTeam);
        }
        for ($round = 0; $round < $numRounds; $round++) {
            $week = new Week();
            $week->setName("Week #" . ($round + 1 + $numRounds));
            $newSeasonEntity->addWeek($week);
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $homeIndex = ($round + $match) % ($numTeams - 1);
                $awayIndex = ($numTeams - 1 - $match + $round) % ($numTeams - 1);
                if ($match == 0) {
                    $awayIndex = $numTeams - 1;
                }
                $homeTeam = $teams[$awayIndex];
                $guestTeam = $teams[$homeIndex];

                $game = new Game();
                $game->setHomeTeam($homeTeam);
                $game->setGuestTeam($guestTeam);
                $week->addGames($game);
            }
            $firstTeam = array_shift($teams);
            array_push($teams, $firstTeam);
        }
        $season = $this->_seasonRepository->save($newSeasonEntity);
        return $season;
    }
    
}
