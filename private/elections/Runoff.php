<?php
class Runoff implements ElectionHandler {
    private $db_code;
    public function __construct($db_code){
        $this->db_code = $db_code;
    }

    public function makeSelectionForm(): string{
        return "";
    }
    public function showConfirmation(): string{
        return "";
    }
    public function storeVote(): void{
        //TODO FINISH THIS
    }
    public function countVotes(): array{
        return array();
    }
    public function hasAnalysisMethod(): bool {
        return false;
    }
}