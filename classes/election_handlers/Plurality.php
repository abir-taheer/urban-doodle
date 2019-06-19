<?php
class Plurality implements ElectionHandler {
    private $election;
    public $num_choices, $allows_duplicates;
    /**
     * ElectionHandler constructor.
     * @param Election $election an instance of the election
     */
    public function __construct(Election $election)
    {
        $this->election = $election;
        $extra_info = json_decode($election->extra, true);
        $this->num_choices = is_int($extra_info["choices"]) ? $extra_info["choices"] : 1;

        // The reason for a comparison is to ensure that this variable is always a boolean even if the data is incorrectly stored
        $this->allows_duplicates = $extra_info["count_duplicates"] === true;
    }

    /**
     * Echos the completed html elements, and any necessary resources, to be inserted into the page for the voting selection
     * Choices for candidates must be under the name votes[] -- All votes must be accessible using $_POST["votes"]
     * DBCode of election must be accessible using -- $_POST["election"]
     */
    public function makeSelectionForm(): void
    {
        Web::addScript("/static/js/plurality.js");
        $escaped_election_name = htmlspecialchars($this->election->name);
        $extra_text = "";
        if( $this->num_choices > 1 ){
            $extra_text .= "Each selection has the same weight. If you vote for the same candidate more than once, the duplicate votes ";
            $extra_text .= ($this->allows_duplicates) ? " <b class='green-txt'>will</b> be counted." : "<b class='red-txt'>will not</b> be counted.";
        }
        $s_word = ($this->num_choices > 1) ? "selections" : "selection";

        $selection = "";
        // Making the form
        foreach( range(1, $this->num_choices) as $selection_number ){
            $candidate_radios = "";
            $cands = $this->election->getCandidates();
            shuffle($cands);
            foreach( $cands as $candidate ){
                $safe_name = htmlspecialchars($candidate->name);
                $candidate_radios .= <<<HTML
                <div class="mdc-form-field">
                  <div class="mdc-radio">
                    <input class="mdc-radio__native-control" type="radio" name="votes[{$selection_number}]" value="{$candidate->id}">
                    <div class="mdc-radio__background">
                      <div class="mdc-radio__outer-circle"></div>
                      <div class="mdc-radio__inner-circle"></div>
                    </div>
                  </div>
                  <label class="muli">{$safe_name}</label>
                </div><br>
HTML;
            }
            $candidate_radios .= <<<HTML
                <div class="mdc-form-field">
                  <div class="mdc-radio">
                    <input class="mdc-radio__native-control" type="radio" name="votes[{$selection_number}]" value="na" required>
                    <div class="mdc-radio__background">
                      <div class="mdc-radio__outer-circle"></div>
                      <div class="mdc-radio__inner-circle"></div>
                    </div>
                  </div>
                  <label class="muli red-txt">No Selection</label>
                </div><br>
HTML;
            $selection .= <<<HTML
            <h3>Selection {$selection_number}</h3>
            {$candidate_radios}
HTML;
        }

        echo <<< HTML
            <div class="mdc-card mdc-layout-grid__cell--span-12 instant">
                <h3 class="txt-ctr">{$escaped_election_name}</h3>
                <p class="txt-ctr small-txt sub-container">This election allows for <b>{$this->num_choices}</b> {$s_word}. {$extra_text}</p>
                <form class="vote-form" data-num="{$this->num_choices}">
                    <input type="hidden" name="election" value="{$this->election->db_code}">
                    <ul class="mdc-list sub-container">
                        {$selection}
                    </ul>
                </form>
                <br>
                <div class="sub-container">
                    <button class="mdc-button vote-submit mdc-button--unelevated" >Submit</button>
                </div>
                <br>
            </div>
HTML;
    }

    /**
     * Checks if raw vote data sent from the client and makes sure that it matches the necessary format
     * @param array $vote Raw post vote data from client -- $_POST["votes"]
     * @return bool
     */
    public function verifyVote($vote): bool
    {
        if( count($vote) !== $this->num_choices ){
            return False;
        }
        $candidate_ids = [];
        foreach($this->election->getCandidates() as $candidate){
            $candidate_ids[] = $candidate->id;
        }

        $all_none = true;

        foreach($vote as $choice){
            if( !in_array($choice, $candidate_ids) && $choice !== 'na'){
                return false;
            }
            if( $choice !== 'na' ){
                $all_none = false;
            }
        }
        if( $all_none ){
            return false;
        }

        return true;
    }

    /**
     * Encodes the array of votes into a string that can be later decoded to form an array of the votes
     * Depending on the election, the order of items in this array is very important!
     * @param array $vote -- The equivalent of $_POST["votes"]
     * @return string
     */
    public function encodeVotes($vote): string
    {
        // TODO: Implement encodeVotes() method.
        return implode(",", $vote);
    }

    /**
     * Convert encoded votes back into an array of votes that can be used by the countVotes method
     * @param string $str a string of votes encoded using the encodeVotes() method
     * @return array
     */
    public function decodeVotes($str): array
    {
        // TODO: Implement decodeVotes() method.
        return explode(",", $str);
    }

    public function showConfirmation($confirmation_id): void
    {
        $user = Session::getUser();
        $form = $user->getConfirmationData($confirmation_id);
        $candidates = $this->decodeVotes($form["content"]);
        $candidate_items = "";
        $escaped_election_name = htmlspecialchars($this->election->name);
        foreach( $candidates as $id ) {
            if( $id !== 'na' ){
                $candidate = new Candidate($id);
                $escaped_candidate_name = htmlspecialchars($candidate->name);
            } else {
                $escaped_candidate_name = "<a class='red-txt'>No Selection</a>";
            }
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
                <p class="txt-ctr small-txt sub-container red-txt">Please verify that the selections below are in fact the ones you made.</p>
                <form class="confirm-form">
                    <input type="hidden" name="token" value="{$user->makeFormToken("submit_vote", $confirmation_id, Web::UTCDate("+1 hour"))}">
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

    /**
     * Tallies up the votes for all of the candidates and returns an array containing the results
     * This may vary based on the type of election, but it MUST:
     *      Initialize the counting by giving all of the candidates 0 votes
     *      Must not assign any votes to candidates who have been disqualified
     *      Return a winner/ Contain a case for a tie
     *      Contain enough information to be displayed on the results page
     * @return array
     */
    public function countVotes(): array
    {
        $vote_count = [];
        foreach( $this->election->getCandidates() as $candidate ){
            $vote_count[$candidate->id] = 0;
        }
        $votes = $this->election->getDecodedVotes();
        foreach( $votes as $vote ){
            if( ! $this->allows_duplicates ){
                array_unique($vote["content"]);
            }
            foreach( $vote["content"] as $candidate_id ){
                if( $candidate_id === "na" ) break;
                $vote_count[$candidate_id] ++;
            }
        }

        $max = 0;
        $most_votes = [];

        foreach( $vote_count as $candidate_id => $num_votes ){
            if( $num_votes === $max ){
                $most_votes[] = $candidate_id;
            }

            if( $num_votes > $max ){
                $max = $num_votes;
                $most_votes = [$candidate_id];
            }
        }

        $winner = count($most_votes) === 1 ? $most_votes[0] : "Tie / No Winner";

        $vote_data["results"] = $vote_count;
        $vote_data["total_eligible_voters"] = $this->election->numPossibleVoters();
        $vote_data["eligible_voters_by_grade"] = $this->election->getEligibleVotersByGrade();
        $vote_data["total_votes"] = count($votes);
        $vote_data["votes_by_grade"] = $this->election->getVotesByGrade();
        $vote_data["candidates"] = $this->election->getCandidateAssociation();
        $vote_data["allowed_selections"] = $this->num_choices;
        $vote_data["count_duplicate_votes"] = $this->allows_duplicates;

        $vote_data["winner"] = $winner;
        return $vote_data;
    }

    /**
     * OPTIONAL: An election type may also have a procedure for analyze the results of an election and finding patterns and correlations in places
     * The analysis method must be called: analyzeResults
     * This function simply lets the script know if it should call the analysis method or not
     * Return true if the analyzeResults method exists and is functional, or false otherwise
     * @return bool
     */
    public function hasAnalysisMethod(): bool
    {
        // TODO: Implement hasAnalysisMethod() method.
        return false;
    }

    /**
     * Outputs html code to be rendered by the client that displays the results of that type of election
     * Assume that the results of all elections are stored in the following path: public/static/elections/<election db_code>/results.json
     * Assume that data containing the votes, grades, and timestamps of votes ara available at this path: public/static/elections/<election db_code>/votes.json
     * Results may also be generated on the client side using the information above and a script
     * @param $result
     */
    public static function displayResults($result): void
    {
        $result_data = $result->getResultData();
        require_once app_root."/templates/election_handlers/Plurality/results.php";
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
}