<?php if( ! isset($path[2]) || $path[2] === "" ): ?>
<?php foreach(Result::getAllResults() as $result): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-4">

        <div class="mdc-card__primary-action change-page" data-page="/results/<?php echo htmlspecialchars($result->db_code); ?>">

            <div class="mdc-card__media mdc-card__media--16-9 " style="background-image: url(https://badgerherald.com/wordpress/wp-content/uploads/2016/10/giphy-23-1.gif);"></div>

            <div>
                <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($result->name); ?></h2>
            </div>

            <br>

        </div>

            <div class="mdc-card__actions">

                <div class="mdc-card__action-buttons">

                    <button class="mdc-button mdc-card__action mdc-card__action--button change-page" data-page="/results/<?php echo htmlspecialchars($result->db_code); ?>">View</button>

                </div>

                <div class="mdc-card__action-icons">

                    <button class="mdc-icon-button share-card material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true" data-share-url="<?php echo (web_ssl) ? "https://" : "http://"; echo htmlspecialchars(web_domain); ?>/results/<?php echo htmlspecialchars($result->db_code); ?>">share</button>

                </div>

            </div>

        </div>

    <?php endforeach; ?>
<?php else: ?>

    <?php
        $result = new Result($path[2]);
    ?>

    <?php if( $result->constructed ): ?>
        <?php
            require_once app_root."/classes/election_handlers/".$result->type.".php";
            $reflection = new \ReflectionClass( $result->type );
            $handler =  $reflection->newInstanceWithoutConstructor();
        ?>
<div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined muli">

    <br>
    <h2 class="txt-ctr sub-container">Results: <?php echo htmlspecialchars($result->name); ?></h2>
    <p class="txt-ctr">Started: <time
            class="js-timer"
            data-timer-type="to-local-time"
            data-time-format="F d, Y  h:ia"
            data-show-local
            data-time-date="<?php echo base64_encode($result->start_time->format(DATE_ATOM)); ?>"
            datetime="<?php echo $result->start_time->format(DATE_ATOM); ?>"></time></p>
    <p class="txt-ctr">Ended: <time
            class="js-timer"
            data-timer-type="to-local-time"
            data-time-format="F d, Y  h:ia"
            data-show-local
            data-time-date="<?php echo base64_encode($result->end_time->format(DATE_ATOM)); ?>"
            datetime="<?php echo $result->end_time->format(DATE_ATOM); ?>"></time></p>
    <div class="flx-ctr">

        <a class="mdc-button" target="_blank" href="/static/elections/<?php echo htmlspecialchars($result->db_code); ?>/votes.json">Votes JSON</a>

    </div>
    <hr class="sub-container">
    <?php $handler::displayResults($result); ?>
</div>
    <?php else: ?>

        <p class="mdc-layout-grid__cell--span-12">There is no past election with the ID passed in the url.</p>

    <?php endif; ?>
<?php endif; ?>
