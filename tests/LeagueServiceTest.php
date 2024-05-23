<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueServiceTest extends KernelTestCase
{
    private const TEAM_COUNT = 10;
    
    private function createNewSeason(){
        $teamRepository = static::getContainer()->get(\App\Repository\TeamRepository::class);
        $leagueService = static::getContainer()->get(\App\Service\LeagueService::class);
        $queryBuilder = $teamRepository->createQueryBuilder('t');
        $queryBuilder->select('t.id')
            ->distinct()
            ->orderBy('RAND()')
            ->setMaxResults(self::TEAM_COUNT);
        $teams = $queryBuilder->getQuery()->getScalarResult();
        $teamsArray = array_map(function($team) {
            return $team['id'];
        }, $teams);
        return $leagueService->createNewSeason($teamsArray);
    }
    public function testSeasonCreating(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $newSeason = $this->createNewSeason();
        $this->assertInstanceOf(\App\Entity\Season::class, $newSeason);
        $this->assertNotEmpty($newSeason);
        $this->assertNotEmpty($newSeason->getWeeks());
        $this->assertEquals(18, count($newSeason->getWeeks()));
        $this->assertEquals(10, count($newSeason->getTeams()));
        $this->assertEquals(90, $newSeason->getCountOfCames());
        foreach($newSeason->getWeeks() as $week){
            $teamsInWeek = [];
            foreach($week->getGames() as $game){
                $homeTeamPlayedThisWeek = in_array($game->getHomeTeam()->getId(),$teamsInWeek);
                $guestTeamPlayedThisWeek = in_array($game->getGuestTeam()->getId(),$teamsInWeek);
                $this->assertFalse($homeTeamPlayedThisWeek);
                $this->assertFalse($guestTeamPlayedThisWeek);
                $teamNotTheSame = $game->getGuestTeam()->getId() !== $game->getHomeTeam()->getId();
                $teamsInWeek[] = $game->getHomeTeam()->getId();
                $teamsInWeek[] = $game->getGuestTeam()->getId();
                $this->assertTrue($teamNotTheSame);
            }
        }
    }
    public function testResultsPerWeek(){
        $kernel = self::bootKernel();
        $leagueService = static::getContainer()->get(\App\Service\LeagueService::class);
        $newSeason = $this->createNewSeason(); 
        for($i = 0; $i <= self::TEAM_COUNT; $i ++){
            $statistic = $leagueService->playWeek($newSeason, $i);
            $this->assertInstanceOf(\App\Dto\WeekResultDto::class, $statistic);
            $isCorrectGamesPerWeek = (self::TEAM_COUNT/2 == count($statistic->games));
            $this->assertTrue($isCorrectGamesPerWeek);
            $this->assertNotEmpty($statistic);
        }
    }
}
