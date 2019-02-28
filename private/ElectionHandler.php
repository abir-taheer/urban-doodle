<?php
interface ElectionHandler {
    /**
     * ElectionHandler constructor.
     * @param string $db_code The ID of the election to get the relevant information for
     */
    public function __construct($db_code);

    /**
     * Returns the completed html elements to be inserted into the page for the voting selection
     * @return string
     */
    public function makeSelectionForm() : string;

    /**
     * Returns the completed html elements to be inserted onto the confirmation page
     * @return string
     */
    public function showConfirmation() : string;

    /**
     * Stores the current votes for the election in a CSV file in the public/static/elections directory. Candidates are stores using their names rather than their ID
     */
    public function storeVotesCSV() : void;


    /**
     * Stores the current votes for the election in a JSON file in the public/static/elections directory.
     * Candidates are stored using their ID's and there is a reference for correlating candidate ID to name
     * Example:
     * {
     *      "candidates": {
     *          "a1b2c3d4": "Guido van Rossum and Rasmus Lerdorf"
     *      }
     *      "votes": [
     *          "a1b2c3d4,d4c3b2a1,b1c2d3a4"
     *      ]
     * }
     */
    public function storeVotesJSON() : void;
    public function countVotes() : array;
    public function hasAnalysisMethod() : bool;
    //TODO make an analysis class that provides insights about the election
    //public static function analyzeElection();
}