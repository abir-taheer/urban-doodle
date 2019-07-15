<?php
if( $path[2] === "search-api" ):
    // Return a json encoded results for the help search

elseif( ! isset($path[2]) || $path[2] === ""  ): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline2">Help</h2>
            <h2>Tech Checkup:</h2>
            <div class="sub-container">
                <!-- TODO INCLUDE SOME TECH COMPATABILITY CHECKUPS HERE -->
                <h3></h3>
                <a class="js-timer" data-timer-type="current" data-time-format="l F jS, Y h:i:sa"></a>

            </div>
            <h2>Help Articles:</h2>
            <h3 class="mdc-typography--headline6 clickable linkish change-page" data-page="/help/markdown">Markdown</h3>
            <div class="sub-container">
                <?php
                $markdowns = Help::getByGroup("markdown");
                shuffle($markdowns);
                foreach( $markdowns as $number => $help): ?>
                    <?php if( $number > 3 ) break; ?>
                    <p class="change-page linkish clickable" data-page="/help/<?php echo htmlspecialchars("markdown"."/".$help->track); ?>"><?php echo htmlspecialchars($help->title); ?></p>
                <?php endforeach; ?>
            </div>
            <!-- TODO MAKE A LIST OF THE AVAILABLE FAQS HERE -->
        </div>
    </div>
<?php else: ?>
    <?php if( ! isset($path[3]) || $path[3] === ""  ):
        // only the group has been provided
        $helps_for_group = Help::getByGroup($path[2]);
        ?>
        <div class="mdc-card mdc-layout-grid__cell--span-12">
            <div class="sub-container help-results">
                <button class="mdc-button change-page" data-page="/help"><- Back To Help</button>
                <h1 class="txt-ctr mdc-typography--headline2">Help - <?php echo ucwords($path[2]); ?></h1>
                <?php if(count($helps_for_group) === 0): ?>
                    <p class="txt-ctr">There are no help threads associated with that help group.</p>

                <?php endif; ?>
                <?php // Get all of the help threads for this group
                foreach( $helps_for_group as $help):
                ?>
                    <p class="txt-ctr change-page linkish clickable mdc-typography--headline6" data-page="/help/<?php echo htmlspecialchars($path[2]."/".$help->track); ?>"><?php echo htmlspecialchars($help->title); ?></p>
                <?php endforeach; ?>
                <br><br>
            </div>
        </div>
    <?php else:
        $help = new Help($path[3]);
        ?>
        <?php if( $help->constructed && $help->inGroup($path[2])): ?>
            <div class="mdc-card mdc-layout-grid__cell--span-12">
                <div class="sub-container">
                    <button class="mdc-button change-page" data-page="/help/<?php echo htmlspecialchars($path[2]); ?>"><- Back To <?php echo htmlspecialchars(ucwords($path[2])); ?> Help</button>
                    <h1 class="txt-ctr mdc-typography--headline2"><?php echo htmlspecialchars($help->title); ?></h1>
                    <p class="txt-ctr small-txt grey-txt">Last Edited: <time datetime="<?php
                            $edited_date = Web::UTCDate($help->date)->format(DATE_ATOM);
                            echo $edited_date;
                        ?>" class="js-timer" data-timer-type="to-local-time" data-time-format="F d, Y  h:ia" data-time-date="<?php
                            echo base64_encode($edited_date);
                        ?>"></time></p>
                    <div class="markdown-content-unready markdown-content"><?php echo base64_encode($help->content); ?></div>
                </div>
                <br><br>
            </div>
        <?php else: ?>
            <div class="mdc-card mdc-layout-grid__cell--span-12">
                <div class="sub-container">
                    <button class="mdc-button change-page" data-page="/help/<?php echo htmlspecialchars($path[2]); ?>"><- Back To <?php echo htmlspecialchars(ucwords($path[2])); ?></button>
                    <h1 class="txt-ctr mdc-typography--headline2">Not Found In <?php echo htmlspecialchars(ucwords($path[2])); ?></h1>
                    <p class="txt-ctr">We could not locate a help thread with the provided ID that belongs in this group.</p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
