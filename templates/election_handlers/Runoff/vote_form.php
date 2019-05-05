<div class="mdc-card mdc-layout-grid__cell--span-12 instant">
<h3 class="txt-ctr"><?php echo htmlspecialchars($this->election->name); ?></h3>
<p class="txt-ctr small-txt sub-container">Order the candidates based on your preference by holding down and dragging. <a class="desktop-only">Click on the X to remove a candidate from your ballot</a><a class="mobile-only">Swipe on a candidate to remove them from your ballot</a>.</p>
<form class="vote-form">
    <input type="hidden" name="election" value="<?php echo $this->election->db_code; ?>">
    <ul class="mdc-list sub-container candidate-select">
        <?php foreach( $candidates as $candidate ): ?>
            <li class="mdc-list-item">
                <input type="hidden" name="votes[confirmed][]" value="<?php echo htmlspecialchars($candidate->id); ?>">
                <span class="mdc-list-item__text no-select">
                <a class="candidate-name"><?php echo htmlspecialchars($candidate->name); ?></a>
                <span class="right-icons">
                            <i class="material-icons candidate-lower">arrow_downward</i>
                    <i class="material-icons candidate-remove desktop-only">clear</i>
                    <i class="material-icons drag-icon">drag_indicator</i>
                        </span>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</form>
    <div class="not-vote-txt fear sub-container">
        <p class="txt-ctr">Removed from ballot:</p>
        <p class="txt-ctr small-txt">Click on a candidate to add them back to your ballot.</p>
    </div>
    <div class="mdc-chip-set non-vote-container sub-container"></div>
    <br>
    <div class="sub-container">
        <button class="mdc-button vote-submit mdc-button--unelevated">Submit</button>
    </div>
    <br>
</div>
