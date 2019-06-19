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

        $candidates = $this->election->getCandidates();
        shuffle($candidates);
        ob_start();
        require_once app_root."/templates/election_handlers/Runoff/vote_form.php";
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;

    }
    public function showConfirmation($votes): void{
        $user = Session::getUser();
        $form = $user->getConfirmationData($votes);
        $candidates = $this->decodeVotes($form["content"]);

        ob_start();
        require_once app_root."/templates/election_handlers/Runoff/confirm_form.php";
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
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

        ob_start();
        require_once app_root."/templates/election_handlers/Runoff/results.php";
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;

    }
}