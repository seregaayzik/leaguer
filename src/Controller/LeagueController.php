<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LeagueService;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SimulationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\MatchNavService;

class LeagueController extends AbstractController
{
    private LeagueService $_leagueService;
    private MatchNavService $_matchNavService;
    
    public function __construct(
        LeagueService $_leagueService ,
        MatchNavService $matchNavService
    ) {
        $this->_leagueService = $_leagueService;
        $this->_matchNavService = $matchNavService;
    }
    
    #[Route('/league/playAll/{id}', name: 'simulate_season', requirements: ['id' => '\d+', 'week' => '\d+'])]
    public function simulateAll(int $id,Request $request): Response
    {
        $season = $this->_leagueService->getSeasonById($id);
        if(!$season){
            throw $this->createNotFoundException('Page not found.');
        }
        $refresh = $request->get('refresh',false);
        $weekResults = $this->_leagueService->playAll($season,$refresh);
        return $this->render('league/simulate_all.html.twig', [
            'weekResults' => $weekResults,
            'seasonId' => $id
        ]);            
    }
    
    #[Route('/getData/{id}/{week}', name: 'get_week_data', requirements: ['id' => '\d+', 'week' => '\d+'])]
    public function getData(int $id,Request $request,int $week = 0): Response
    {
        $season = $this->_leagueService->getSeasonById($id);
        if(!$season){
            throw $this->createNotFoundException('Page not found.');
        } 
        try{
            $refresh = $request->get('refresh',false);
            $weekResult = $this->_leagueService->playWeek($season, $week,$refresh);
        }catch(\InvalidArgumentException $invalidArgument){
            throw $this->createNotFoundException('Page not found.');
        }
        return $this->render('league/_score.html.twig', [
            'weekNum' => $week,
            'nav' => $this->_matchNavService->buildNavMenu($season, $week),
            'weekResult' => $weekResult,
            'seasonId' => $id,
        ]);
    }
    
    #[Route('/editGame', name: 'edit_game', methods: ['POST'])]
    public function editGame(Request $request): JsonResponse
    {
        $status = true;
        $message = '';
        $code = 200;
        if(!$request->get('gameId') || !$request->get('scoreType')){
            throw $this->createNotFoundException('Page not found.');
        }
        try{
            $this->_leagueService->changeGameResult($request->get('gameId'),$request->get('scoreType'),$request->get('score'));
        }catch(\InvalidArgumentException $invalidAttributeException){
            $status = false;
            $message = $invalidAttributeException->getMessage();
            $code = 400;
        }catch(\Exception $exeption){
            $status = false;
            $message = $exeption->getMessage();
            $code = 500;
        }
        return $this->json([
            'status' => $status,
            'message' => $message,
        ],$code);
    }
    
    #[Route('/simulate/{id}', name: 'simulate_week', requirements: ['id' => '\d+'])]
    public function simulate(int $id): Response
    {
        $season = $this->_leagueService->isSeasonExists($id);
        if(!$season){
            throw $this->createNotFoundException('Page not found.');
        } 
        return $this->render('league/simulate.html.twig', [
            'seasonId' => $id
        ]);
    }
    
    #[Route('/', name: 'index_league')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SimulationType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $season = $this->_leagueService->createNewSeason($data['teams']);
            return $this->redirectToRoute('simulate_week', ['id' => $season->getId()]);
        }
        return $this->render('league/index.html.twig', [
            'form' => $form->createView(),
            'history' => $this->_leagueService->getHistory()
        ]);
    }
}
