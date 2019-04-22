<?php
// Show all of the elections here
$user = Session::getUser();
?>
<?php if( ! isset($path[3]) || $path[3] === "" ): ?>
    <?php foreach( Election::getAllElections() as $e ): ?>
        <div class="mdc-card mdc-layout-grid__cell--span-4">
            <div class="mdc-card__primary-action sub-page-change" data-page="/admin/candidates/<?php echo htmlspecialchars($e->db_code); ?>">
                <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url(/static/img/election_covers/<?php echo $e->pic; ?>);"></div>
                <div>
                    <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($e->name); ?></h2>
                </div>
                <br>
            </div>
            <div class="mdc-card__actions">
                <div class="mdc-card__action-buttons">
                    <button class="mdc-button mdc-card__action mdc-card__action--button sub-page-change" data-page="/admin/candidates/<?php echo htmlspecialchars($e->db_code); ?>">View Candidates</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?php
    try{
        $election = new Election($path[3]);
        require_once "../classes/election_handlers/".$election->type.".php";
        $handler = new $election->type($election);
        if (! $handler instanceof ElectionHandler) {
            throw new Exception("Class for handling an election must implement the ElectionHandler interface.");
        }
        $results = $handler->countVotes();

    } catch (Exception $e){
        echo "<p class=\"mdc-layout-grid__cell--span-12\">There is no current election with the ID that was passed in the URL</p>";
        exit;
    }
    ?>
    <?php if( ! isset($path[4]) || $path[4] === "" ): ?>
<!--    A page with an overview of all of the candidates for this election-->
    <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined">
        <div class="sub-container">
            <h2 class="txt-ctr">Candidates: <?php echo htmlspecialchars($election->name); ?></h2>

        </div>
    </div>
    <?php elseif( $path[4] === "create" ): ?>
<!--    Form to make a candidate for the current election-->
    <?php else: ?>
<!--    Check if the candidate id exists and display relevant info for the candidate-->
    <?php endif; ?>
<?php endif;?>
