<?php if( !isset($path[2]) ):
    $all_elections = Election::getAllElections();
    ?>
    <?php if( count($all_elections) === 0 ): ?>
        <p class="mdc-layout-grid__cell--span-12">There are currently no ongoing elections.</p>
    <?php endif; ?>
    <?php foreach( $all_elections as $e ): ?>
        <div class="mdc-card mdc-layout-grid__cell--span-4">
            <div class="mdc-card__primary-action change-page" data-page="/candidates/<?php echo htmlspecialchars($e->db_code); ?>">
                <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url(/static/img/election_covers/<?php echo $e->pic; ?>);"></div>
                <div>
                    <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($e->name); ?></h2>
                </div>
                <div class="mdc-typography mdc-typography--body2 vote-card__pad"><a>View Candidates -></a></div>
                <br>
            </div>
        </div>
    <?php endforeach; ?>
<?php else:?>
    <?php
    try{
        $e = new Election($path[2]);
    } catch (Exception $e){
        replyError("Error:", "We apologize, but there currently are not any elections with the ID: ".htmlspecialchars($path[2])."<br><br><button class=\"mdc-button change-page\" data-page=\"/candidates\"><- Back To Candidates</button>");
        exit;
    }
    ?>
    <?php if(!isset($path[3]) || $path[3] === "" ): ?>
        <?php
        // Election was successfully found
        $candidates = $e->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ): ?>
            <div class="mdc-card mdc-layout-grid__cell--span-4">
                <div class="mdc-card__primary-action change-page" data-page="/candidates/<?php echo $e->db_code."/".$candidate->id; ?>">
                    <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url('/static/elections/<?php echo htmlspecialchars($candidate->db_code)."/candidates/".htmlspecialchars($candidate->id); ?>.jpg');"></div>
                    <div>
                        <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($candidate->name); ?></h2>
                    </div>
                </div>
                <div class="mdc-card__actions">
                    <div class="mdc-card__action-buttons">
                        <button class="mdc-button mdc-card__action mdc-card__action--button change-page" data-page="/candidates/<?php echo $e->db_code."/".$candidate->id; ?>">View</button>
                        <?php if( Session::hasSession() && false ): ?>
                        <button class="mdc-button mdc-card__action mdc-card__action--button"><?php
                            $user = Session::getUser();
                            echo $user->isFollowing($candidate->id) ? "Unfollow" : "Follow";
                            ?></button>
                        <?php endif; ?>

                    </div>
                    <div class="mdc-card__action-icons">
                        <button class="mdc-icon-button share-card material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true" data-share-url="<?php echo web_ssl ? "https://": "http://";echo web_domain; ?>/candidates/<?php echo $e->db_code."/".$candidate->id; ?>">share</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else:
        // Election was found, but now check to see if the candidate can be found
        $candidate = new Candidate($path[3]);

        // In the case that the candidate was not found or is associated with another election:
        // Send back the error page and stop script execution
        if( $candidate->db_code !== $path[2] || ! $candidate->constructed ){
            replyError("Error:", "We apologize, but there currently are not any candidates with that path.<br><br><button class=\"mdc-button change-page\" data-page=\"/candidates\"><- Back To Candidates</button>");
            exit;
        }
        
        // Include the candidate data below
        ?>
        <div class="mdc-card mdc-layout-grid__cell--span-12">
            <br>
            <div class="sub-container">
                <button class="mdc-button change-page" data-page="."><- Back To Candidates</button>
                <br>
                <div class="flx-ctr">
                    <img class="candidate-photo" src="/static/elections/<?php echo htmlspecialchars($candidate->db_code)."/candidates/".htmlspecialchars($candidate->id); ?>.jpg" alt="Candidate Photo">
                </div>
                <h2 class="txt-ctr muli"><?php echo htmlspecialchars($candidate->name); ?></h2>
                <div class="mdc-layout-grid__inner">
                    <?php foreach( $candidate->getBasicInfo() as $info ): ?>
                        <div class="mdc-layout-grid__cell--span-4 desktop-only" ></div>
                        <div class="mdc-layout-grid__cell--span-2 muli"><?php echo htmlspecialchars($info->type); ?>:</div>
                        <div class="mdc-layout-grid__cell--span-6 muli"><?php echo $info->getEncodedContent(); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <br>
        </div>
    <?php endif; ?>
<?php endif; ?>

