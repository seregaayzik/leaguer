<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Stat for 2023 - 2024 Season
        $stats = [
            [
                'teamName' => 'Liverpool FC',
                'goal' => 84,
                'goalc' => 41,
                'games' => 38,
            ],
            [
                'teamName' => 'AFC Bournemouth',
                'goal' => 53,
                'goalc' => 65,
                'games' => 38,
            ],
            [
                'teamName' => 'Arsenal FC',
                'goal' => 89,
                'goalc' => 28,
                'games' => 38,
            ],
            [
                'teamName' => 'Aston Villa FC',
                'goal' => 76,
                'goalc' => 56,
                'games' => 38,
            ],
            [
                'teamName' => 'Brentford FC',
                'goal' => 54,
                'goalc' => 61,
                'games' => 38,
            ],
            [
                'teamName' => 'Brighton & Hove Albion FC',
                'goal' => 55,
                'goalc' => 60,
                'games' => 38,
            ],
            [
                'teamName' => 'Burnley FC',
                'goal' => 40,
                'goalc' => 76,
                'games' => 38,
            ],
            [
                'teamName' => 'Chelsea FC',
                'goal' => 75,
                'goalc' => 62,
                'games' => 38,
            ],
            [
                'teamName' => 'Crystal Palace FC',
                'goal' => 52,
                'goalc' => 58,
                'games' => 38,
            ],
                        [
                'teamName' => 'Everton FC',
                'goal' => 39,
                'goalc' => 49,
                'games' => 38,
            ],
            [
                'teamName' => 'Fulham FC',
                'goal' => 51,
                'goalc' => 59,
                'games' => 38,
            ],
            [
                'teamName' => 'Luton Town FC',
                'goal' => 50,
                'goalc' => 81,
                'games' => 38,
            ],
                        [
                'teamName' => 'Manchester City FC',
                'goal' => 93,
                'goalc' => 33,
                'games' => 38,            
            ],
            [
                'teamName' => 'Manchester United FC',
                'goal' => 55,
                'goalc' => 58,
                'games' => 38,
            ],
            [
                'teamName' => 'Newcastle United FC',
                'goal' => 81,
                'goalc' => 60,
                'games' => 38,
            ],
            [
                'teamName' => 'Nottingham Forest FC',
                'goal' => 47,
                'goalc' => 66,
                'games' => 38,
            ],
            [
                'teamName' => 'Sheffield United FC',
                'goal' => 35,
                'goalc' => 101,
                'games' => 38,
            ],
            [
                'teamName' => 'Tottenham Hotspur FC',
                'goal' => 71,
                'goalc' => 61,
                'games' => 38,
            ],
            [
                'teamName' => 'West Ham United FC',
                'goal' => 59,
                'goalc' => 71,
                'games' => 38,
            ],
            [
                'teamName' => 'Wolverhampton Wanderers FC',
                'goal' => 50,
                'goalc' => 63,
                'games' => 38,
            ],
        ];
        foreach($stats as $stat){
            $teamEntity = $manager->getRepository(\App\Entity\Team::class)->findOneByTeamName($stat['teamName']);
            if($teamEntity){
                $statisticEntity = new \App\Entity\Stat();
                $statisticEntity->setTeam($teamEntity);
                $statisticEntity->setGoal($stat['goal']);
                $statisticEntity->setGoalc($stat['goalc']);
                $statisticEntity->setGamesQuantity($stat['games']);
                $manager->persist($statisticEntity);
            }
        }
        $manager->flush();
    }
}
