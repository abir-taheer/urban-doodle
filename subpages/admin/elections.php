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
        hi
<?php endif;?>
