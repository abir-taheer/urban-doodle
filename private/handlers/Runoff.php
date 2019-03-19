<?php
class Runoff implements ElectionHandler {
    private $election;

    public function __construct(Election $election){
        $this->election = $election;
    }

    public function makeSelectionForm(): void{
        Web::addScript("https://cdnjs.cloudflare.com/ajax/libs/slipjs/2.1.1/slip.min.js");
        Web::addScript("/static/js/runoff.js");
        $response = "
            <div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12 instant\">
                <h3 class=\"txt-ctr\">".$this->election->name."</h3>
                <ul class=\"mdc-list sub-container candidate-select\" data-mdc-auto-init=\"MDCList\">
        ";
        $candidates = $this->election->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ) {
            $response.= "
                <li class=\"mdc-list-item\">
                    <input type=\"hidden\" name=\"vote[]\" value=\"".$candidate->id."\">
                    <span class=\"mdc-list-item__text no-select\"><a class=\"candidate-name\">".$candidate->name."</a><i class=\"material-icons drag-icon\">drag_indicator</i></span>
                </li>
            ";
        }
        $response.= "
                </ul>
                <div class=\"mdc-chip-set non-vote-container sub-container\" data-mdc-auto-init=\"MDCChipSet\"></div>
            </div>
        ";
        echo $response;
    }
    public function showConfirmation(): void{

    }
    public function getVotesArray(): array{
        // TODO: Implement getVotesArray() method.
        return array();
    }

    public function countVotes(): array{
        return array();
    }
    public function hasAnalysisMethod(): bool {
        return false;
    }
}