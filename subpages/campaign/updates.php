<?php if( ! isset($path[4]) || trim($path[4]) === "" ): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">Updates</h2>
            <button class="mdc-button mdc-button--outlined sub-page-change" data-page="/campaign/<?php echo $candidate->id."/updates/create"; ?>"><i class="material-icons">add</i> &nbsp; New Update</button>
            <br><br>
            <?php foreach( [
                "approved"=> [
                    Update::getApprovedUpdatesByGroup($candidate->id),
                    "green-txt"
                ],
                "denied"=> [
                    Update::getDeniedUpdatesByGroup($candidate->id),
                    "red-txt"
                ],
                "pending" => [
                    Update::getUnreviewedUpdatesByGroup($candidate->id),
                    "grey-txt"
                ]
                ] as $type => $updates_list):
            ?>
                <h2 class="<?php echo $updates_list[1]; ?>">
                    <?php echo ucwords($type); ?>
                </h2>
                <?php if( count($updates_list[0]) === 0):?>
                    <p class="small-txt">You currently don't have any <?php echo $type; ?> updates</p>
                <?php endif; ?>
                <ul class="mdc-list mdc-list--two-line no-top-pad">
                    <?php foreach( $updates_list[0] as $update ): ?>
                        <li class="mdc-list-item sub-page-change"
                            tabindex="0"
                            data-page="/campaign/<?php echo $candidate->id."/updates/".$update->track; ?>">
                            <span class="mdc-list-item__text">
                                <span class="mdc-list-item__primary-text uline <?php echo $updates_list[1]; ?>"><?php echo htmlspecialchars($update->title); ?></span>
                                <span class="mdc-list-item__secondary-text js-timer"
                                      data-timer-type="to-local-time"
                                      data-time-format="F jS, Y h:i:sa"
                                      data-time-date="<?php echo base64_encode($update->date->format(DATE_ATOM)); ?>">
                                  Update Time
                                </span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
<?php elseif( $path[4] === "create" ):
    Web::addScript("/static/js/campaign/updates/create.js");
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <br>
            <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/updates"; ?>"><-- Back To Updates</button>
            <h2 class="txt-ctr mdc-typography--headline4">New Update</h2>
            <form class="no-submit" data-action="/requests.php" data-callback="change-page" data-reload-page="/campaign/<?php
                echo htmlspecialchars($candidate->id."/updates");
                ?>">
                <input name="token" type="hidden" value="<?php echo htmlspecialchars($user->makeFormToken("create_update", $candidate->id, Web::UTCDate("+1 day"))); ?>">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-8">
                        <div class="mdc-text-field mdc-text-field--outlined full-width-txt-field">
                            <input class="mdc-text-field__input update-title" name="update-title" value="Using Markdown" maxlength="64">
                            <div class="mdc-notched-outline">
                                <div class="mdc-notched-outline__leading"></div>
                                <div class="mdc-notched-outline__notch">
                                    <label class="mdc-floating-label">Title</label>
                                </div>
                                <div class="mdc-notched-outline__trailing"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div id="markdown-editor">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell--span-6 muli">
                            <div class="markdown-editor-buttons">
                                <button class="mdc-icon-button material-icons titlify">title</button>
                                <button class="mdc-icon-button material-icons boldify">format_bold</button>
                                <button class="mdc-icon-button material-icons italify">format_italic</button>
                                <button class="mdc-icon-button material-icons strikify">strikethrough_s</button>
                                <button class="mdc-icon-button material-icons colorify rainbow">format_color_text</button>
                                <button class="mdc-icon-button material-icons clearify">format_clear</button>
                                <button class="mdc-icon-button material-icons imagify">add_photo_alternate</button>
                                <button class="mdc-icon-button material-icons quotify">format_quote</button>
                                <button class="mdc-icon-button material-icons desktop-only splitify fear">vertical_split</button>
                                <button class="mdc-icon-button material-icons desktop-only fullify">view_stream</button>
                            </div>
                            <div class="mdc-text-field mdc-text-field--textarea mdc-text-field--fullwidth	">
    <!--                            <div class="mdc-text-field-character-counter">0 / 1024</div>-->
                                <textarea name="content" class="mdc-text-field__input" rows="8" cols="40" maxlength="1024">
# Hey!
## This is *Markdown* :sparkles:
It's what you'll be using to make your ~~great~~ {**amazing**}(red) posts.

To learn more go [here](https://<?php echo htmlspecialchars(web_domain); ?>/help/markdown)
Even if you're already familiar with it from GitHub, you should check out the link because we added some of our own awesome new features.
psst, this preview updates automatically as you write. Isn't that {awesome}(rainbow)!</textarea>
                                <div class="mdc-notched-outline">
                                    <div class="mdc-notched-outline__leading"></div>
                                    <div class="mdc-notched-outline__notch">
                                        <label for="textarea" class="mdc-floating-label">Post Editor</label>
                                    </div>
                                    <div class="mdc-notched-outline__trailing"></div>
                                </div>
                            </div>
                            <a class="small-txt">Max length: 1024 characters.</a>
                        </div>
                        <div class="mdc-layout-grid__cell--span-6 muli">
                            <p>Preview</p>
                            <div class="markdown-preview markdown-content"></div>
                        </div>
                    </div>
                </div>
            </form>
            <button class="mdc-button mdc-button--raised pre-confirm">Submit Post</button>
            <div class="full-confirm fear">
                <button class="mdc-button mdc-button--raised submit-form">Confirm</button>
                <button class="mdc-button mdc-button--raised cancel-update-confirm">Cancel</button>
            </div>
            <br><br>
        </div>
    </div>
<?php else:
    $update = new Update($path[4]);
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <br>
            <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/updates"; ?>"><-- Back To Updates</button>
            <h3 class="txt-ctr mdc-typography--headline4">View Update:</h3>
            <?php if( $update->constructed ): ?>
                <?php
                    switch( $update->status ){
                        case -1:
                            $update_status = "Denied";
                            $update_color = "red-txt";
                            break;
                        case 0:
                            $update_status = "Pending";
                            $update_color = "grey-txt";
                            break;
                        case 1:
                            $update_status = "Approved";
                            $update_color = "green-txt";
                            break;
                    }
                ?>
                <p>Type: <a class="grey-txt"><?php echo htmlspecialchars(ucwords($update->type)); ?> Update</a></p>
                <p>Submitted On:
                    <a class="js-timer grey-txt"
                       data-timer-type="to-local-time"
                       data-time-format="F jS, Y h:i:sa"
                       data-show-local
                       data-time-date="<?php echo base64_encode($update->date->format(DATE_ATOM));?>"></a>
                </p>
                <p>Status: <b class="<?php echo $update_color; ?>"><?php echo $update_status; ?></b></p>
                <?php if( $update->status === -1 ): ?>
                    <p>Denial Reason:</p>
                    <blockquote class="red-blockquote"><?php echo htmlspecialchars($update->denial_reason); ?></blockquote>
                <?php endif; ?>
                <h3>Preview:</h3>
                <div class="boxed">
                    <div class="sub-container">
                        <h1><?php echo htmlspecialchars($update->title); ?></h1>
                        <div class="markdown-content-unready markdown-content">
                            <?php echo base64_encode($update->content); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="sub-container">
                    <p class="txt-ctr">That update could not be found</p>
                </div>
            <?php endif; ?>
            <br><br>
        </div>
    </div>
<?php endif; ?>
