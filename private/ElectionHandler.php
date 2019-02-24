<?php
interface ElectionHandler {
    public function __construct($db_code);
    public function makeSelectionForm() : string;
    public function showConfirmation() : string;
    public function storeVote() : void;
    public function countVotes() : array;
    public function hasAnalysisMethod() : bool;
    //TODO make an analysis class that provides insights about the election
    //public static function analyzeElection();
}