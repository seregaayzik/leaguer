<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $teams = [
            "Sheffield United FC",
            "Luton Town FC",
            "Burnley FC",
            "West Ham United FC",
            "Nottingham Forest FC",
            "AFC Bournemouth",
            "Wolverhampton Wanderers FC",
            "Chelsea FC",
            "Tottenham Hotspur FC",
            "Brentford FC",
            "Newcastle United FC",
            "Brighton & Hove Albion FC",
            "Fulham FC",
            "Crystal Palace FC",
            "Manchester United FC",
            "Aston Villa FC",
            "Everton FC",
            "Liverpool FC",
            "Manchester City FC",
            "Arsenal FC"
            ];
        foreach($teams as $teamName){
            $teamEntity = new \App\Entity\Team();
            $teamEntity->setName($teamName);
            $manager->persist($teamEntity);
        }
        $manager->flush();
    }
}
