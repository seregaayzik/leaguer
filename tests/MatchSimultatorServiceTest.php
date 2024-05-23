<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchSimultatorServiceTest extends KernelTestCase
{
    public function testGameSImulator(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        $teamRepository = static::getContainer()->get(\App\Repository\TeamRepository::class);
        $matchSimulatorService = static::getContainer()->get(\App\Service\MatchSimulatorService::class);
        $queryBuilder = $teamRepository->createQueryBuilder('t');
        $queryBuilder->select('t')
            ->distinct()
            ->orderBy('RAND()')
            ->setMaxResults(2);
        $teams = $queryBuilder->getQuery()->getResult();
        $this->assertNotEmpty($teams);
        $gameResult = $matchSimulatorService->simulateMatchResult($teams[0],$teams[1]);
        $this->assertInstanceOf(\App\Dto\GameResultDto::class, $gameResult);
        $this->assertNotEmpty($matchSimulatorService);
        $this->assertLessThan(20, $gameResult->homeScore );
        $this->assertGreaterThan(-1, $gameResult->homeScore );
        $this->assertLessThan(20, $gameResult->guestScore );
        $this->assertGreaterThan(-1, $gameResult->guestScore );
    }
}
