<?php

namespace App\Service;

use App\Entity\Season;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MatchNavService {   
    public UrlGeneratorInterface $_urlGeneratorInterface;
    
    public function __construct(
        UrlGeneratorInterface $urlGeneratorInterface
    ){
        $this->_urlGeneratorInterface = $urlGeneratorInterface;
    }
    
    public function buildNavMenu(Season $season, int $weekIndex):array{
        $buttons = [];
        $lastWeekIndex = count($season->getWeeks()) - 1;
        if($weekIndex - 1 >= 0){
            $buttons[] = [
                'label' => 'Show prew week',
                'url' => $this->_urlGeneratorInterface->generate('get_week_data', ['id' => $season->getId(), 'week' => $weekIndex - 1])
            ];
        }
        if($lastWeekIndex > $weekIndex){
            $nextWeekButton = [
                'url' => $this->_urlGeneratorInterface->generate('get_week_data', ['id' => $season->getId(), 'week' => $weekIndex + 1])
            ];
            $nextWeek = $season->getWeek($weekIndex + 1);
            if($nextWeek->isFinished()){
                $nextWeekButton['label'] = 'Show next week results';
            }else{
                $nextWeekButton['label'] = 'Play next week';
            } 
            $buttons[] = $nextWeekButton;
        }
        $playAllUrl = [
            'url' => $this->_urlGeneratorInterface->generate('simulate_season', ['id' => $season->getId()])
        ];

        if($season->isSeasonFinished()){
            $playAllUrl['label'] = 'Show All Results';
        }else{
            $playAllUrl['label'] = 'Play All';
        }
        $buttons[] = $playAllUrl;
        return $buttons;
    }
}
