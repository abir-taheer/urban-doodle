<?php
class Plurality implements ElectionHandler {
    private $election;
    /**
     * ElectionHandler constructor.
     * @param Election $election an instance of the election
     */
    public function __construct(Election $election)
    {
        $this->election = $election;
    }

    /**
     * Echos the completed html elements, and any necessary resources, to be inserted into the page for the voting selection
     * Choices for candidates must be under the name votes[] -- All votes must be accessible using $_POST["votes"]
     * DBCode of election must be accessible using -- $_POST["election"]
     */
    public function makeSelectionForm(): void
    {
        // TODO: Implement makeSelectionForm() method.
    }

    /**
     * Checks if raw vote data sent from the client and makes sure that it matches the necessary format
     * @param array $vote Raw post vote data from client -- $_POST["votes"]
     * @return bool
     */
    public function verifyVote($vote): bool
    {
        // TODO: Implement verifyVote() method.
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
    }

    /**
     * Convert encoded votes back into an array of votes that can be used by the countVotes method
     * @param string $str a string of votes encoded using the encodeVotes() method
     * @return array
     */
    public function decodeVotes($str): array
    {
        // TODO: Implement decodeVotes() method.
    }

    /**
     * Echos the completed html elements, and any necessary resources, to be inserted onto the confirmation page
     * Must also send out the $vote_id as a parameter
     * @param string $confirmation_id The user's votes, encoded, referenced by a form token
     */
    public function showConfirmation($confirmation_id): void
    {
        // TODO: Implement showConfirmation() method.
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
        // TODO: Implement countVotes() method.
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
    }

    /**
     * Outputs html code to be rendered by the client that displays the results of that type of election
     * Assume that the results of all elections are stored in the following path: public/static/elections/<election db_code>/results.json
     * Assume that data containing the votes, grades, and timestamps of votes ara available at this path: public/static/elections/<election db_code>/votes.json
     * Results may also be generated on the client side using the information above and a script
     * @param array $results_data - An array containing an array resembling one that would have been returned by the countVotes function
     * @param string $type - User that the results are being generated for. Either "admin" or "client"
     * If being generated for "admin", do not assume that results.json or votes.json exists
     */
    public static function displayResults($results_data, $type): void
    {
        // TODO: Implement displayResults() method.
    }
}