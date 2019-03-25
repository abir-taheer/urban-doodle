<?php
class Runoff implements ElectionHandler {
    private $election;

    public function __construct(Election $election){
        $this->election = $election;
    }

    public function makeSelectionForm(): void{
        // Get the user from the session
        // Since we can already assume that there is already a valid user if this function is being called
        $user = Session::getUser();
        Web::addScript("https://cdnjs.cloudflare.com/ajax/libs/slipjs/2.1.1/slip.min.js");
        Web::addScript("/static/js/runoff.js");
        $response = "
            <div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12 instant\">
                <h3 class=\"txt-ctr\">".$this->election->name."</h3>
                <p class=\"txt-ctr small-txt sub-container\">Order the candidates based on your preference by holding down and dragging. <a class=\"desktop-only\">Click on the X to remove a candidate from your ballot</a><a class=\"mobile-only\">Swipe on a candidate to remove them from your ballot</a>.</p>
                <form class=\"vote-form\">
                    <input type=\"hidden\" name=\"token\" value=\"".$user->makeFormToken("confirm", $this->election->db_code, Web::UTCDate("+30 min"))."\">
                    <ul class=\"mdc-list sub-container candidate-select\" data-mdc-auto-init=\"MDCList\">
        ";
        $candidates = $this->election->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ) {
            $response.= "
                <li class=\"mdc-list-item\">
                    <input type=\"hidden\" name=\"vote[]\" value=\"".$candidate->id."\">
                    <span class=\"mdc-list-item__text no-select\">
                        <a class=\"candidate-name\">".$candidate->name."</a>
                        <span class=\"right-icons\">
                            <i class=\"material-icons candidate-lower\">arrow_downward</i>
                            <i class=\"material-icons candidate-remove desktop-only\">clear</i>
                            <i class=\"material-icons drag-icon\">drag_indicator</i>
                        </span>
                    </span>
                </li>
            ";
        }
        $response.= "
                    </ul>
                </form>
                <div class=\"not-vote-txt fear sub-container\">
                    <p class=\"txt-ctr\">Removed from ballot:</p>
                    <p class=\"txt-ctr small-txt\">Click on a candidate to add them back to your ballot.</p>
                </div>
                <div class=\"mdc-chip-set non-vote-container sub-container\" data-mdc-auto-init=\"MDCChipSet\"></div>
                <br>
                <div class=\"sub-container\">
                    <button class=\"mdc-button vote-submit mdc-button--unelevated\" data-mdc-auto-init=\"MDCRipple\">Submit</button>
                </div>
                <br>
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