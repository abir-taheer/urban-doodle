<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = Session::getUser();
?>
<?php switch($user->status):
case -1: ?>
    <?php Web::addScript("/static/js/unrecognized.js"); ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="card-expand-default"></div>
        <h3 class="sumana txt-ctr">Email Not Recognized</h3>
        <div class="sub-container">
            <p class="txt-ctr"><a>We could not locate a user with the provided email address. Please fill out the form below to request the ability to vote and you will be notified by email when your request has been approved.</a></p>
            <form id="unrecognized" data-action="/requests.php" data-callback="reload">
                <input name="token" type="hidden" value="<?php echo $user->makeFormToken("unrecognized", "submit", Web::UTCDate("+1 hour")); ?>">
                <div class="mdc-text-field mdc-text-field--outlined">
                    <input type="text" name="full_name" class="mdc-text-field__input">
                    <label class="mdc-floating-label">Full Name</label>
                    <div class="mdc-line-ripple"></div>
                </div><br><br>
                <div class="mdc-select mdc-select--outlined">
                    <i class="mdc-select__dropdown-icon"></i>
                    <select class="mdc-select__native-control" name="grade">
                        <option value="" disabled selected></option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="f">Faculty</option>
                    </select>
                    <label class="mdc-floating-label">Grade</label>
                    <div class="mdc-line-ripple"></div>
                </div>
                <br><br>
                <div class="mdc-text-field mdc-text-field--outlined">
                    <input type="text" name="osis" pattern="^\d{0,9}$" class="mdc-text-field__input">
                    <label class="mdc-floating-label">OSIS</label>
                    <div class="mdc-line-ripple"></div>
                </div>
                <div class="mdc-text-field-helper-line">
                    <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" >Must be 9 digits.</div>
                </div>
                <br><br>

                <p class="faculty-warning fear mdl-color-text--red-600">This is meant for faculty only! Setting your grade to faculty means that your votes will have no effect towards elections! <b>This cannot be changed!</b></p>
                <p class="mdl-color-text--red-500 error-text fear">There were errors with your submission:</p>
            </form>
            <div>
                <button class="mdc-button submit-form" >Submit</button>
            </div>
            <br><br>
        </div>
    </div>
    <?php break; ?>
<?php case 0: ?>
    <?php
        $u_req = $user->unrecognized_request;
        $submitted = new DateTime($u_req['created']);
        $data_list = array(
            array("Request ID", $u_req['track'], "device_hub"),
            array("Name", $u_req['name'], "person_pin"),
            array("Email", $u_req['email'], "contact_mail"),
            array("Grade", $u_req['grade'], "school"),
            array("OSIS", $u_req['osis'], "dialpad"),
            array("Submitted On", $submitted->format("F d, Y  h:ia"), "access_time")
        );
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="card-expand-default"></div>
        <h3 class="sumana txt-ctr">Email Not Recognized</h3>
        <div class="sub-container">
            <p class="txt-ctr"><a>Your request to be able to vote has been received. The details are below. You will receive an email email the request has been approved.</a></p>
            <ul class="mdc-list">
                <?php foreach($data_list as $i ): ?>
                    <li class="mdc-list-item" tabindex="0">
                        <i class="material-icons mdl-list__item-icon"><?php echo $i[2]; ?></i>
                        <span class="mdc-list-item__text">
                        &nbsp;&nbsp;<?php echo htmlentities($i[0]); ?> :&nbsp;<a class="notranslate"><?php echo htmlentities($i[1]); ?></a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <br>
    </div>
    <?php break; ?>
<?php case 1: ?>
        <?php if( count($user->getElections()) === 0 ): ?>
            <p class="mdc-layout-grid__cell--span-12">There are currently no ongoing elections for you to vote in.</p>
        <?php endif; ?>
    <?php foreach( $user->getElections() as $e ): ?>
            <div class="mdc-card mdc-layout-grid__cell--span-4">
                <div class="mdc-card__primary-action change-page" data-page="/vote/<?php echo htmlspecialchars($e->db_code); ?>">
                    <div class="mdc-card__media mdc-card__media--16-9" style="background-image: url(/static/img/election_covers/<?php echo $e->pic; ?>);"></div>
                    <div>
                        <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($e->name); ?></h2>
                    </div>
                    <div class="mdc-typography mdc-typography--body2 vote-card__pad">
                        <?php if( Web::getUTCTime() < $e->start_time ): //Before Election starts, notify when election will start?>
                            <a>Election will start: <?php
                                $starts = clone $e->start_time;
                                $starts->setTimezone(new DateTimeZone(app_time_zone));
                                echo $starts->format("M d, Y h:ia");
                                ?></a>
                        <?php else: //If we've passed the start time, 2 possibilities ?>
                            <?php if( Web::getUTCTime() < $e->end_time ): //Time if before the end time, voting in session ?>
                                <a>Election ends in: <span class="js-timer js-timer__warning" data-timer-type="countdown" data-count-down-date="<?php echo base64_encode($e->end_time->format(DATE_ATOM)); ?>" data-count-down-warning="3600000"></span></a>
                            <?php else: //Election is over ?>
                                <a>Election has concluded. Please await results.</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <br>
                </div>
                <div class="mdc-card__actions">
                    <div class="mdc-card__action-buttons">
                        <button class="mdc-button mdc-card__action mdc-card__action--button change-page" data-page="/vote/<?php echo htmlspecialchars($e->db_code); ?>">Vote</button>
                        <button class="mdc-button mdc-card__action mdc-card__action--button change-page" data-page="/candidates/<?php echo htmlspecialchars($e->db_code); ?>">Candidates</button>
                    </div>
                    <div class="mdc-card__action-icons">
                        <button class="mdc-icon-button share-card material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true" data-share-url="<?php echo (web_ssl) ? "https://" : "http://"; echo addslashes(web_domain); ?>/vote/<?php echo addslashes($e->db_code); ?>">share</button>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>

    <?php break; ?>
<?php endswitch; ?>