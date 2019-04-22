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
        <?php
            Web::addScript("/static/js/admin/create_candidate.js");
            Web::sendDependencies();
        ?>
<!--    Form to make a candidate for the current election-->
    <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined">
        <div class="sub-container">
            <h2 class="txt-ctr">Create Candidate: <?php echo htmlspecialchars($election->name); ?></h2>
            <div class="sub-container">
                <form data-action="/requests.php" data-callback="change-page" data-reload-page="/admin/candidates/<?php echo addslashes($election->db_code); ?>">
                    <div class="mdc-text-field">
                        <input class="mdc-text-field__input" name="name">
                        <div class="mdc-line-ripple"></div>
                        <label class="mdc-floating-label">Name</label>
                    </div>
                    <br><br>
                    <div class="mdc-text-field">
                        <input class="mdc-text-field__input" name="initial">
                        <div class="mdc-line-ripple"></div>
                        <label class="mdc-floating-label">Initials</label>
                    </div>
                    <div class="mdc-text-field-helper-line">
                        <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent">
                            This will be used to create the candidate's default profile photo
                        </div>
                    </div>

                    <h3>Main Candidate Emails:</h3>
                    <div class="all-editors-container">
                        <div class="sub-container editor-container">
                            <p>Editor 1:</p>
                            <div class="mdc-text-field">
                                <input class="mdc-text-field__input" name="editor[0][email]">
                                <div class="mdc-line-ripple"></div>
                                <label class="mdc-floating-label">Editor Email</label>
                            </div>
                            <i class="material-icons remove-editor clickable">clear</i>
                        </div>
                    </div>
                    <br>
                    <button class="mdc-button add-editor">Add Editor</button>

                </form>
                <div class="sub-container">
                    <button class="mdc-button">Create Candidate</button>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
<!--    Check if the candidate id exists and display relevant info for the candidate-->
    <?php endif; ?>
<?php endif;?>
