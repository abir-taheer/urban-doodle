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
        Web::addScript("/static/js/runoff.js");
        Web::sendDependencies();

        // TODO move this to a template file
        $response = "
            <div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12 instant\">
                <h3 class=\"txt-ctr\">".$this->election->name."</h3>
                <p class=\"txt-ctr small-txt sub-container\">Order the candidates based on your preference by holding down and dragging. <a class=\"desktop-only\">Click on the X to remove a candidate from your ballot</a><a class=\"mobile-only\">Swipe on a candidate to remove them from your ballot</a>.</p>
                <form class=\"vote-form\">
                    <input type=\"hidden\" name=\"election\" value=\"".$this->election->db_code."\">
                    <ul class=\"mdc-list sub-container candidate-select\" data-mdc-auto-init=\"MDCList\">
        ";
        $candidates = $this->election->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ) {
            $response.= "
                <li class=\"mdc-list-item\">
                    <input type=\"hidden\" name=\"votes[confirmed][]\" value=\"".$candidate->id."\">
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
    public function showConfirmation($votes): void{
        $user = Session::getUser();
        $form = $user->getConfirmationData($votes);
        $response = "
            <div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12 instant\">
                <h3 class=\"txt-ctr\">Confirm Selection: ".$this->election->name."</h3>
                <p class=\"txt-ctr small-txt sub-container red-txt\">Please verify that the votes below are in the order that you previously selected.</p>
                <form class=\"confirm-form\">
                    <input type=\"hidden\" name=\"token\" value=\"".$user->makeFormToken("submit_vote", $votes, Web::UTCDate("+1 hour"))."\">
                    <ul class=\"mdc-list rank-candidates sub-container\" data-mdc-auto-init=\"MDCList\">
        ";
        $candidates = $this->decodeVotes($form["content"]);
        foreach( $candidates as $id ) {
            $candidate = new Candidate($id);
            $response.= "
                <li class=\"mdc-list-item\">
                    <span class=\"mdc-list-item__text no-select\">
                        <a class=\"candidate-name\">".$candidate->name."</a>
                    </span>
                </li>
            ";
        }
        $response.= "
                    </ul>
                </form>
                <br>
                <div class=\"sub-container\">
                    <button class=\"mdc-button confirm-votes mdc-button--unelevated\" data-mdc-auto-init=\"MDCRipple\">Confirm</button>
                    &nbsp;&nbsp;
                    <button class=\"mdc-button cancel-confirm\" data-mdc-auto-init=\"MDCRipple\">Cancel</button>
                </div>
                <br>
            </div>
        ";
        echo $response;
    }

    public function verifyVote($vote): bool {
        $confirmed = $vote["confirmed"] ?? [];
        $removed = $vote["removed"] ?? [];
        $all_votes = array_merge($confirmed, $removed);
        $accounted_for = 0;
        foreach( $this->election->getCandidates() as $candidate ){
            if( in_array($candidate->id, $all_votes) ){
                $accounted_for++;
            }
        }
        return count($all_votes) === count($this->election->getCandidates()) &&  count($all_votes) === $accounted_for && count($confirmed) != 0;
    }

    public function countVotes(): array {
        $decoded_votes = [];
        $eliminated = [];
        foreach( $this->election->getAllVotes() as $raw_vote ){
            $decoded_votes[] = $this->decodeVotes($raw_vote["content"]);
        }
        $vote_data = [];
        $current_round = 0;
        $winner = null;

        // Setup the first round of the vote counts as 0
        foreach( $this->election->getCandidates() as $candidate ){
            $vote_data["rounds"][$current_round]["votes"][$candidate->id] = 0;
            $vote_data["candidates"][$candidate->id] = $candidate->name;
        }

        while( $winner === null ){
            $votes_this_round = 0;
            foreach( $decoded_votes as $choices ){
                foreach( $choices as $vote ){
                    if( ! in_array($vote, $eliminated) ){
                        $vote_data["rounds"][$current_round]["votes"][$vote]++;
                        $votes_this_round++;
                        break;
                    }
                }
            }
            $vote_data["rounds"][$current_round]["total_votes"] = $votes_this_round;

            $lowest_votes = min($vote_data["rounds"][$current_round]["votes"]);
            $highest_votes = max($vote_data["rounds"][$current_round]["votes"]);

            if( $highest_votes === $lowest_votes ){
                $winner = "Tie / No Winner";
                break;
            } else {
                if( $highest_votes / $votes_this_round >= 0.5){
                    $winner = array_search($highest_votes, $vote_data["rounds"][$current_round]["votes"]);
                    break;
                }
            }

            foreach($vote_data["rounds"][$current_round]["votes"] as $id => $vote_count){
                if( $vote_count === $lowest_votes ){
                    $eliminated[] = $id;
                    $vote_data["rounds"][$current_round]["eliminated"][] = $id;
                }
            }

            $current_round++;
        }
        $vote_data["total_votes"] = count($decoded_votes);
        $vote_data["winner"] = $winner;
        return $vote_data;
    }
    public function hasAnalysisMethod(): bool {
        return false;
    }

    public function encodeVotes($votes): string {
        return implode(",", $votes["confirmed"]);
    }

    public function decodeVotes($str): array
    {
        return explode(",", $str);
    }
}