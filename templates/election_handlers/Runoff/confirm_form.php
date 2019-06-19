<div class="mdc-card mdc-layout-grid__cell--span-12 instant">
    <h3 class="txt-ctr">Confirm Selection: <?php echo htmlspecialchars($this->election->name); ?></h3>
    <p class="txt-ctr small-txt sub-container red-txt">Please verify that the votes below are in the order that you previously selected.</p>
    <form class="confirm-form">
        <input type="hidden" name="token" value="<?php echo $user->makeFormToken("submit_vote", $votes, Web::UTCDate("+1 hour")) ?>">
        <ul class="mdc-list rank-candidates mdc-list--non-interactive sub-container">
            <?php foreach( $candidates as $id ): ?>
                <?php $candidate = new Candidate($id); ?>
                <li class="mdc-list-item">
                    <span class="mdc-list-item__text no-select">
                        <a class="candidate-name"><?php echo htmlspecialchars($candidate->name); ?></a>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </form>
    <br>
    <div class="sub-container">
        <button class="mdc-button confirm-votes mdc-button--unelevated" >Confirm</button>
        &nbsp;&nbsp;
        <button class="mdc-button cancel-confirm">Cancel</button>
    </div>
    <br>
</div>