<?php
// Show all of the elections here
$user = Session::getUser();
?>
<?php if( ! isset($path[3]) || $path[3] === "" ): ?>
<!--A list of all of the available elections-->
    <?php foreach( Election::getAllElections() as $e ): ?>
        <div class="mdc-card mdc-layout-grid__cell--span-4">
            <div class="mdc-card__primary-action sub-page-change" data-page="/admin/elections/<?php echo htmlspecialchars($e->db_code); ?>">
                <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url(/static/img/election_covers/<?php echo $e->pic; ?>);"></div>
                <div>
                    <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($e->name); ?></h2>
                </div>
                <br>
            </div>
            <div class="mdc-card__actions">
                <div class="mdc-card__action-buttons">
                    <button class="mdc-button mdc-card__action mdc-card__action--button sub-page-change" data-page="/admin/elections/<?php echo htmlspecialchars($e->db_code); ?>">View Election</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif( $path[3] === "create" ): ?>
    <?php
    Web::addScript("/static/js/admin/create_election.js");
    Web::sendDependencies();
    ?>
<!--A form to make a new election-->
    <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined">
        <h2 class="txt-ctr">Create Election:</h2>
        <div class="sub-container">
            <form data-action="/requests.php" data-callback="change-page" data-reload-page="/admin/elections">
                <input type="hidden" name="token" value="<?php echo $user->makeFormToken("create_election", "", Web::UTCDate("+1 day")); ?>">
                <div class="mdc-layout-grid">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-text-field mdc-layout-grid__cell--span-4" data-mdc-auto-init="MDCTextField">
                            <input class="mdc-text-field__input" name="name">
                            <div class="mdc-line-ripple"></div>
                            <label class="mdc-floating-label">Name</label>
                        </div>

                        <div class="mdc-layout-grid__cell--span-8"></div>

                        <div class="mdc-select mdc-layout-grid__cell--span-4" data-mdc-auto-init="MDCSelect">
                            <i class="mdc-select__dropdown-icon"></i>
                            <select class="mdc-select__native-control" name="type">
                                <option value="" disabled selected></option>
                                <?php foreach( scandir(app_root."/classes/election_handlers/") as $type ): ?>
                                    <?php if( in_array($type, [".", ".."]) ){ continue; } ?>
                                    <option value="<?php echo addslashes(substr($type, 0, strlen($type) - 4)); ?>">
                                        <?php echo htmlspecialchars(substr($type, 0, strlen($type) - 4)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label class="mdc-floating-label">Election Type</label>
                            <div class="mdc-line-ripple"></div>
                        </div>

                        <div class="mdc-layout-grid__cell--span-8"></div>

                        <div class="mdc-text-field mdc-text-field--with-leading-icon mdc-layout-grid__cell--span-4" data-mdc-auto-init="MDCTextField">
                            <i class="material-icons mdc-text-field__icon">event</i>
                            <input class="mdc-text-field__input" type="datetime-local" name="start">
                            <div class="mdc-line-ripple"></div>
                            <label class="mdc-floating-label">Start Time</label>
                        </div>

                        <div class="mdc-layout-grid__cell--span-8"></div>

                        <div class="mdc-text-field mdc-text-field--with-leading-icon mdc-layout-grid__cell--span-4" data-mdc-auto-init="MDCTextField">
                            <i class="material-icons mdc-text-field__icon">event</i>
                            <input class="mdc-text-field__input" type="datetime-local" name="end">
                            <div class="mdc-line-ripple"></div>
                            <label class="mdc-floating-label">End Time</label>
                        </div>

                        <div class="mdc-layout-grid__cell--span-8"></div>

                        <div class="mdc-layout-grid__cell--span-4">
                            <p class="roboto">Allowed Grades:</p>
                            <div class="mdc-layout-grid__inner">
                            <?php foreach (range(9,12) as $grade): ?>
                                <div class="mdc-form-field mdc-layout-grid__cell--span-3" data-mdc-auto-init="MDCFormField">
                                    <div class="mdc-checkbox" data-mdc-auto-init="MDCCheckbox">
                                        <input type="checkbox" class="mdc-checkbox__native-control" name="grade[<?php echo addslashes($grade); ?>]"/>
                                        <div class="mdc-checkbox__background">
                                            <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                                                <path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"></path>
                                            </svg>
                                            <div class="mdc-checkbox__mixedmark"></div>
                                        </div>
                                    </div>
                                    <label><?php echo htmlspecialchars($grade); ?></label>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="pic" value="" id="election_pic">
            </form>
            <div class="image-preview clickable change-preview">
                <img alt="Election Photo" src="/static/img/image.png" width="200">
            </div>
            <button class="mdc-button change-preview">Select Election Image</button><br><br>
            <button class="mdc-button submit-form">Submit</button>
        </div>
        <br>
    </div>
    <div class="mdc-dialog election-pic-dialog">
        <div class="mdc-dialog__container">
            <div class="mdc-dialog__surface">
                <h2 class="mdc-dialog__title">Select Image</h2>
                <div class="mdc-dialog__content">
                    <div class="mdc-layout-grid">
                        <div class="mdc-layout-grid__inner pick-election-img">
                            <div class="mdc-layout-grid__cell upload-election-img click-hover clickable" data-form-token="<?php echo $user->makeFormToken("upload_election_image", "cover", Web::UTCDate("+1 day")); ?>">
                                <div class="flx-ctr">
                                    <i class="material-icons">add_photo_alternate</i>
                                </div>
                                <p class="txt-ctr">Upload Photo (0.5MB max)</p>
                            </div>
                            <?php foreach( scandir(app_root."/public/static/img/election_covers") as $pic ): ?>
                                <?php if( in_array($pic, [".", ".."]) ){ continue; } ?>
                                <div class="mdc-layout-grid__cell selectable" data-src="<?php echo $pic; ?>">
                                    <img class="mdc-image-list__image" src="/static/img/election_covers/<?php echo $pic; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <footer class="mdc-dialog__actions">
                    <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="no">
                        <span class="mdc-button__label">Cancel</span>
                    </button>
                    <button type="button" class="mdc-button mdc-dialog__button can-select-img" data-mdc-dialog-action="yes" disabled>
                        <span class="mdc-button__label">Select</span>
                    </button>
                </footer>
            </div>
        </div>
        <div class="mdc-dialog__scrim"></div>
    </div>
<?php else: ?>
    <?php
        Web::addScript("/static/js/admin/election.js");
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
<!--Show an overview of the election data with the ability to edit as well as see live outcome-->
    <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined">
        <div class="sub-container">
            <h2 class="txt-ctr"><?php echo htmlspecialchars($election->name); ?></h2>

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
            <br><br>
        </div>
    </div>
<?php endif;?>
