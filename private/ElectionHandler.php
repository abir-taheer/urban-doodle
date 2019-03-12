<?php
interface ElectionHandler {
    /**
     * ElectionHandler constructor.
     * @param string $db_code The ID of the election to get the relevant information for
     */
    public function __construct(string $db_code);

    /**
     * Returns the completed html elements, and any necessary resources, to be inserted into the page for the voting selection
     * @return string
     */
    public function makeSelectionForm() : string;

    /**
     * Returns the completed html elements, and any necessary resources, to be inserted onto the confirmation page
     * @return string
     */
    public function showConfirmation() : string;

    /**
     * Stores the current votes for the election in a CSV file in the public/static/elections directory.
     * Candidates are stores using their names rather than their ID
     * The specifics of the CSV can vary, but must convey all of thee votes in a reasonable and understandable fashion
     * TODO get the csv handling figured out, function excluded as of right now
     */
    //public function storeVotesCSV() : void;


    /**
     * Returns an array containing the votes, unchanged from the database
     * Candidates are stored using their ID's and there is a reference for correlating candidate ID to name
     * Example of format:
     * {
     *      "candidates": {
     *          "a1b2c3d4": "Guido van Rossum and Rasmus Lerdorf"
     *      }
     *      "votes": [
     *          "a1b2c3d4,d4c3b2a1,b1c2d3a4"
     *      ]
     * }
     * @return array
     */
    public function getVotesArray() : array;

    /**
     * Tallies up the votes for all of the candidates and returns an array containing the results
     * This may vary based on the type of election, but it MUST:
     *      Initialize the counting by giving all of the candidates 0 votes
     *      Must not assign any votes to candidates who have been disqualified
     *      Return a winner/ Contain a case for a tie
     *      Contain enough information to be displayed on the results page
     * @return array
     */
    public function countVotes() : array;

    /**
     * OPTIONAL: An election type may also have a procedure for analyze the results of an election and finding patterns and correlations in places
     * The analysis method must be called: analyzeResults
     * This function simply lets the script know if it should call the analysis method or not
     * Return true if the analyzeResults method exists and is functional, or false otherwise
     * @return bool
     */
    public function hasAnalysisMethod() : bool;


    //TODO make an analysis class that provides insights about the election
    //public static function analyzeElection();
}