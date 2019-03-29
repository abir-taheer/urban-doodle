<?php if( !isset($path[2]) ): ?>
<?php foreach( Election::getAllElections() as $e ): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-4">
        <div class="mdc-card__primary-action change-page" data-page="/candidates/<?php echo htmlspecialchars($e->db_code); ?>" tabindex="0" data-mdc-auto-init="MDCRipple">
            <div class="mdc-card__media mdc-card__media--16-9 candidate-card__media"></div>
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
    <?php if(!isset($path[3])): ?>
        <?php
        $candidates = $e->getCandidates();
        shuffle($candidates);
        foreach( $candidates as $candidate ): ?>
            <div class="mdc-card mdc-layout-grid__cell--span-4">
                <div class="mdc-card__primary-action change-page" data-page="<?php echo $e->db_code."/".$candidate->id; ?>" tabindex="0" data-mdc-auto-init="MDCRipple">
                    <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url(&quot;https://material-components.github.io/material-components-web-catalog/static/media/photos/3x2/2.jpg&quot;);"></div>
                    <div>
                        <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($candidate->name); ?></h2>
                    </div>
                </div>
                <div class="mdc-card__actions">
                    <div class="mdc-card__action-buttons">
                        <button class="mdc-button mdc-card__action mdc-card__action--button">View</button>
                        <?php if( Session::hasSession() ) : ?>
                        <button class="mdc-button mdc-card__action mdc-card__action--button"><?php
                            $user = Session::getUser();
                            echo $user->isFollowing($candidate->id) ? "Unfollow" : "Follow";
                            ?></button>
                        <?php endif; ?>

                    </div>
                    <div class="mdc-card__action-icons">
                        <button class="mdc-icon-button material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true">share</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <hi>df</hi>
    <?php endif; ?>
<?php endif; ?>

