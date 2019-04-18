<?php
// Show all of the elections here
?>
<?php if( ! isset($path[3]) || $path[3] === "" ): ?>
    <?php foreach( Election::getAllElections() as $e ): ?>
        <div class="mdc-card mdc-layout-grid__cell--span-4">
            <div class="mdc-card__primary-action sub-page-change" data-page="/admin/elections/<?php echo htmlspecialchars($e->db_code); ?>" tabindex="0" data-mdc-auto-init="MDCRipple">
                <div class="mdc-card__media mdc-card__media--16-9 vote-card__media"></div>
                <div>
                    <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($e->name); ?></h2>
                </div>
                <br>
            </div>
            <div class="mdc-card__actions">
                <div class="mdc-card__action-buttons">
                    <button class="mdc-button mdc-card__action mdc-card__action--button sub-page-change" data-page="/admin/elections/<?php echo htmlspecialchars($e->db_code); ?>" data-mdc-auto-init="MDCRipple">View</button>
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
    <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outline">
        <div class="sub-container">
            <h2 class="txt-ctr"><?php echo htmlspecialchars($election->name); ?></h2>
            <?php print_r($election); ?>
            <h3 class="txt-ctr">Live Results</h3>
            <?php foreach( $results["rounds"] as $round => $round_data ): ?>
                <h4>Round <?php echo ($round + 1); ?></h4>
                <p><?php echo $round_data["total_votes"]." votes total"; ?></p>
                <ul class="mdc-list mdc-list--two-line mdc-list--non-interactive">
                    <?php foreach( $round_data["votes"] as $candidate_id => $vote_count ): ?>
                        <?php
                        $vote_percentage =  strval((int) (($vote_count / $round_data["total_votes"]) * 10000));
                        $vote_percentage = substr($vote_percentage, 0, strlen($vote_percentage) - 2).".".substr($vote_percentage, strlen($vote_percentage) - 2);
                        ?>
                        <li class="mdc-list-item">
                            <span class="mdc-list-item__text">
                                <span class="mdc-list-item__primary-text"><?php echo htmlspecialchars($results["candidates"][$candidate_id]); ?></span>
                                <span class="mdc-list-item__secondary-text"><?php echo htmlspecialchars($vote_count)." votes - ~".$vote_percentage."%"; ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <br>
            <?php endforeach; ?>
            <h3>Winner: <a class="green-txt"><?php echo htmlspecialchars($results["candidates"][$results["winner"]]); ?></a></h3>
        </div>
    </div>
<?php endif;?>
