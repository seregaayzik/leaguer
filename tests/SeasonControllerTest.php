<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\TeamRepository;

class SeasonControllerTest extends WebTestCase
{
    private static $client;
     
    public function testSiteAvability(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
    private function createNewSeason():string{
        self::$client = static::createClient();
        $teamRepository = static::getContainer()->get(TeamRepository::class);
        $queryBuilder = $teamRepository->createQueryBuilder('t');
        $queryBuilder->select('t.id')
            ->distinct()
            ->orderBy('RAND()')
            ->setMaxResults(4);

        $teams = $queryBuilder->getQuery()->getScalarResult();
        $teamsArray = array_map(function($team) {
            return $team['id'];
        }, $teams);
        $crawler = self::$client->request('POST', '/', [
            'simulation' => ['teams' => $teamsArray],
        ]);
        self::$client->followRedirect();
        $seasonUrl = self::$client->getRequest()->getUri();
        return  $seasonUrl;
    }
    public function testCreateSeason(): void{
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $select = $crawler->filter('select#simulation_teams');
        $this->assertCount(1, $select);
        $options = $select->filter('option');
        $this->assertGreaterThan(4, $options->count());
        $optionValues = [];
        foreach ($options as $option) {
            $optionValues[] = $option->getAttribute('value');
        }
        $randomKeys = array_rand($optionValues, 4);
        $randomOptions = array_map(function($key) use ($optionValues) {
            return $optionValues[$key];
        }, $randomKeys);
        $wrongDataToValidate = array_slice($randomOptions, 0, 3);
        $crawler = $client->request('POST', '/', [
            'simulation' => ['teams' => $wrongDataToValidate],
        ]);
        $this->assertResponseIsSuccessful();
        $errorMessage = $crawler->filter('.invalid-feedback')->text();
        $this->assertStringContainsString('This collection should contain 4 elements or more.', $errorMessage);
        $this->assertStringContainsString("It's only paired number allowed.", $errorMessage);
        $crawler = $client->request('POST', '/', [
            'simulation' => ['teams' => $randomOptions],
        ]);
        $client->followRedirect();
        $currentUri = $client->getRequest()->getUri();
        $this->assertStringContainsString('simulate/', $currentUri);
    }
    public function testGetSeasonData():void{
        $seasonPage = $this->createNewSeason();
        $crawler = self::$client->request('GET', '/getData/9999999');
        $this->assertResponseStatusCodeSame(404);
       
        $crawler = self::$client->request('GET', $seasonPage);
        $dataUrl = $crawler->filter('#result-table')->attr('data-url');
        $this->assertNotNull($dataUrl);
        $this->assertNotEmpty($dataUrl);
        for($i = 0; $i <= 5; $i ++){
            $nextWeekUrl = $dataUrl.'/'.$i;            
            $crawler = self::$client->request('GET', $nextWeekUrl);
            $weekNum = $i + 1;
            $this->assertSelectorTextContains('h5', $weekNum.'th Week match results:','Incorrect Header');
            $this->assertResponseIsSuccessful();
            $this->assertEquals(4, $crawler->filter('.score-table tbody tr')->count(),'Incorect team stat');
            $this->assertEquals(2, $crawler->filter('.match-result tbody tr')->count(),'Incorect weekly game data');
            if($i >= 4){
                $this->assertEquals(4, $crawler->filter('.prediction tbody tr')->count(),'Incorect prediction num');
            }else{
                $this->assertEquals(1, $crawler->filter('.prediction tbody tr')->count(),'Incorect prediction num');
            }
        }
        $crawler = self::$client->request('GET', $dataUrl.'/6');
        $this->assertResponseStatusCodeSame(404);
    }

     public function testFullSeasonData():void{
        $seasonPage = $this->createNewSeason();
        $crawler = self::$client->request('GET', $seasonPage);
        $this->assertResponseIsSuccessful();
        $dataUrl = $crawler->filter('#result-table')->attr('data-url');
        $crawler = self::$client->request('GET', $dataUrl);
        $this->assertResponseIsSuccessful();
        $link = $crawler->filter('a[href*="playAll"]')->link();
        $this->assertNotNull($link);
        $linkUrl = $link->getUri();
        $crawler = self::$client->request('GET', $linkUrl);
        $this->assertEquals(6, $crawler->filter('.card-body')->count());
        $this->assertResponseIsSuccessful();
     }
    public function testRecentSeasons(){
        $seasonPage = $this->createNewSeason();
        $crawler = self::$client->request('GET', '/');
        $this->assertSelectorTextContains('h2','Recent games');
        $countOfTables = $crawler->filter('.recent-seasons')->count() > 0;
        $isRecordsExists = $crawler->filter('.recent-seasons tbody tr')->count() > 0;
        $this->assertTrue($countOfTables);
        $this->assertTrue($isRecordsExists);
        $urls = $crawler->filter('.recent-seasons tbody tr td:first-child a')->extract(['href']);
        foreach($urls as $url){
            $crawler = self::$client->request('GET', $url);
            $this->assertResponseIsSuccessful();
            $dataUrl = $crawler->filter('#result-table')->attr('data-url');
            $this->assertNotNull($dataUrl);
            $this->assertNotEmpty($dataUrl);
        }      
    }
    
    public function testChangeScore(){
        $seasonPage = $this->createNewSeason();
        $crawler = self::$client->request('GET', $seasonPage);
        $this->assertResponseIsSuccessful();
        $dataUrl = $crawler->filter('#result-table')->attr('data-url');
        $changeUrl = $crawler->filter('#result-table')->attr('data-update-url');
        $this->assertNotNull($changeUrl);
        $crawler = self::$client->request('GET', $dataUrl);
        $editResultUrls = $crawler->filter('.match-result a.edit-result');
        $this->assertEquals($editResultUrls->count(),4);
        foreach($editResultUrls as $editUrl){
            $gameId = $editUrl->getAttribute('data-game-id');
            $scoreType = $editUrl->getAttribute('data-score-type');
            $score = rand(0, 9);
            self::$client->request('POST', $changeUrl, [
                'gameId' => $gameId,
                'scoreType' => $scoreType,
                'score' => $score
            ]);
            $this->assertResponseIsSuccessful();
        }

    }
}
