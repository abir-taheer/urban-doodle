<?php
class Runoff implements ElectionHandler {
    private $election;

    public function __construct(Election $election){
        $this->election = $election;
    }

    public function makeSelectionForm(): void {
        // Get the user from the session
        // Since we can already assume that there is already a valid user if this function is being called
        $user = Session::getUser();
        Web::addScript("/static/js/runoff.js");
        Web::sendDependencies();

        $candidate_items = "";
        $candidates = $this->election->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ) {
            $escaped_candidate_name = htmlspecialchars($candidate->name);
            $candidate_items.= <<< HTML
                <li class="mdc-list-item">
                    <input type="hidden" name="votes[confirmed][]" value="{$candidate->id}">
                    <span class="mdc-list-item__text no-select">
                        <a class="candidate-name">{$escaped_candidate_name}</a>
                        <span class="right-icons">
                            <i class="material-icons candidate-lower">arrow_downward</i>
                            <i class="material-icons candidate-remove desktop-only">clear</i>
                            <i class="material-icons drag-icon">drag_indicator</i>
                        </span>
                    </span>
                </li>
HTML;
        }
        $escaped_election_name = htmlspecialchars($this->election->name);
        echo <<< HTML
            <div class="mdc-card mdc-layout-grid__cell--span-12 instant">
                <h3 class="txt-ctr">{$escaped_election_name}</h3>
                <p class="txt-ctr small-txt sub-container">Order the candidates based on your preference by holding down and dragging. <b><a class="desktop-only">Click on the X to remove a candidate from your ballot</a><a class="mobile-only">Swipe on a candidate to remove them from your ballot</a>.</b></p>
                <form class="vote-form">
                    <input type="hidden" name="election" value="{$this->election->db_code}">
                    <ul class="mdc-list sub-container candidate-select">
                    {$candidate_items}
                    </ul>
                </form>
                <div class="not-vote-txt fear sub-container">
                    <p class="txt-ctr">Removed from ballot:</p>
                    <p class="txt-ctr small-txt">Click on a candidate to add them back to your ballot.</p>
                </div>
                <div class="mdc-chip-set non-vote-container sub-container"></div>
                <br>
                <div class="sub-container">
                    <button class="mdc-button vote-submit mdc-button--unelevated" >Submit</button>
                </div>
                <br>
            </div>
HTML;
    }
    public function showConfirmation($votes): void{
        $user = Session::getUser();
        $form = $user->getConfirmationData($votes);
        $candidates = $this->decodeVotes($form["content"]);
        $candidate_items = "";
        $escaped_election_name = htmlspecialchars($this->election->name);
        foreach( $candidates as $id ) {
            $candidate = new Candidate($id);
            $escaped_candidate_name = htmlspecialchars($candidate->name);
            $candidate_items.= <<< HTML
                <li class="mdc-list-item">
                    <span class="mdc-list-item__text no-select">
                        <a class="candidate-name">{$escaped_candidate_name}</a>
                    </span>
                </li>
HTML;
        }
        echo <<< HTML
            <div class="mdc-card mdc-layout-grid__cell--span-12 instant">
                <h3 class="txt-ctr">Confirm Selection: {$escaped_election_name}</h3>
                <p class="txt-ctr small-txt sub-container red-txt">Please verify that the votes below are in the order that you previously selected.</p>
                <form class="confirm-form">
                    <input type="hidden" name="token" value="{$user->makeFormToken("submit_vote", $votes, Web::UTCDate("+1 hour"))}">
                    <ul class="mdc-list rank-candidates mdc-list--non-interactive sub-container">
                    {$candidate_items}
                    </ul>
                </form>
                <br>
                <div class="sub-container">
                    <button class="mdc-button confirm-votes mdc-button--unelevated" >Confirm</button>
                    &nbsp;&nbsp;
                    <button class="mdc-button cancel-confirm">Cancel</button>
                </div>
                <br>
            </div>
HTML;
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
            $vote_data["rounds"][$current_round]["eliminated"] = [];

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



        $vote_data["total_eligible_voters"] = $this->election->numPossibleVoters();
        $vote_data["eligible_voters_by_grade"] = $this->election->getEligibleVotersByGrade();
        $vote_data["total_votes"] = count($decoded_votes);
        $vote_data["votes_by_grade"] = $this->election->getVotesByGrade();
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


    public static function displayResults($result): void
    {
        $results_data = $result->getResultData();
        $candidates = $results_data["candidates"];
        $rounds = "";

        foreach($results_data["rounds"] as $round => $round_data){
            $votes_this_round = "";
            foreach( $round_data["votes"] as $candidate_id => $vote_count ){
                $vote_percentage =  strval((int) (($vote_count / $round_data["total_votes"]) * 10000));
                $vote_percentage = substr($vote_percentage, 0, strlen($vote_percentage) - 2).".".substr($vote_percentage, strlen($vote_percentage) - 2);
                $candidate_name = $candidates[$candidate_id];

                $votes_this_round.= <<<HTML
                            <li class="mdc-list-item">
                            <span class="mdc-list-item__text">

                                <span class="mdc-list-item__primary-text">{$candidate_name}</span>
                                <span class="mdc-list-item__secondary-text">{$vote_count} votes - ~ {$vote_percentage}%</span>

                            </span>
                            </li>
HTML;
            }

            $current_round = $round + 1;
            $rounds.= <<< HTML
                        <h3>Round {$current_round}</h3>
                        <p>{$round_data["total_votes"]} votes this round</p>
                        <ul class="mdc-list mdc-list--two-line mdc-list--non-interactive">
                            {$votes_this_round}
                        </ul>
                        <br>
HTML;
        }

        $election_name = htmlspecialchars($result->name);

        echo $rounds;
    }
}