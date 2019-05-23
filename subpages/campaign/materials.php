<?php
    use setasign\Fpdi\Fpdi;
    if( ! isset($path[4]) || trim($path[4]) === "" ): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">Materials</h2>
            <button class="mdc-button mdc-button--outlined sub-page-change" data-page="/campaign/<?php echo $candidate->id."/materials/upload"; ?>"><i class="material-icons">add</i> &nbsp; New Material</button>
            <br><br>
            <?php foreach( [
                               "approved"=> [
                                   $candidate->getApprovedMaterials(),
                                   "green-txt"
                               ],
                               "denied"=> [
                                   $candidate->getDeniedMaterials(),
                                   "red-txt"
                               ],
                               "pending" => [
                                   $candidate->getPendingMaterials(),
                                   "grey-txt"
                               ]
                           ] as $type => $materials_list):
                ?>
                <h2 class="<?php echo $materials_list[1]; ?>">
                    <?php echo ucwords($type); ?>
                </h2>
                <?php if( count($materials_list[0]) === 0):?>
                <p class="small-txt">You currently don't have any <?php echo $type; ?> materials</p>
            <?php endif; ?>
                <ul class="mdc-list mdc-list--two-line no-top-pad">
                    <?php foreach( $materials_list[0] as $material ): ?>
                        <li class="mdc-list-item sub-page-change"
                            tabindex="0"
                            data-page="/campaign/<?php echo $candidate->id."/materials/".$material->track; ?>">
                            <span class="mdc-list-item__text">
                                <span class="mdc-list-item__primary-text uline <?php echo $materials_list[1]; ?>"><?php echo htmlspecialchars($material->title); ?></span>
                                <span class="mdc-list-item__secondary-text"><b><?php echo htmlspecialchars(ucwords($material->type)); ?></b> submitted on
                                    <a class="js-timer"
                                       data-timer-type="to-local-time"
                                       data-time-format="F jS, Y h:i:sa"
                                       data-time-date="<?php echo base64_encode($material->date->format(DATE_ATOM)); ?>">Update Time</a>
                                </span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
<?php elseif( $path[4] === "upload" ):
    Web::addScript("/static/js/campaign/materials/upload.js");
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <br><br>
            <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/materials"; ?>"><-- Back To Materials</button>
            <h2 class="txt-ctr mdc-typography--headline4">Submit Materials for Approval</h2>
            <p class="small-txt">Here you'll need to approve things like posters and social media posts</p>
            <form class="no-submit materials-form" data-action="/requests.php" data-callback="change-page" data-reload-page="/campaign/<?php
                echo htmlspecialchars($candidate->id."/materials");
            ?>">
                <input name="token" type="hidden" value="<?php
                    echo htmlspecialchars($user->makeFormToken("create_materials", $candidate->id, Web::UTCDate("+1 day")));
                    ?>">

                <div class="mdc-text-field">
                    <input class="mdc-text-field__input" name="title" maxlength="128">
                    <div class="mdc-line-ripple"></div>
                    <label class="mdc-floating-label">Name/Title</label>
                </div>
                <div class="mdc-text-field-helper-line">
                    <div class="mdc-text-field-helper-text">Max. 128 characters</div>
                </div>
                <br>
                <div class="mdc-select">
                    <i class="mdc-select__dropdown-icon"></i>
                    <select class="mdc-select__native-control select-material-type" name="type">
                        <option value="" disabled selected></option>
                        <option value="poster">
                            Poster
                        </option>
                        <option value="other">
                            Other
                        </option>
                    </select>
                    <label class="mdc-floating-label">Type</label>
                    <div class="mdc-line-ripple"></div>
                </div>
                <br><br>
                <div class="poster-upload fear">
                    <button class="mdc-button mdc-button--outlined upload-material-poster">+ Upload Poster (PDF)</button>
                    <input type="file" name="poster" class="fear poster-upload-input">
                    <p class="grey-txt fear">Uploaded File: <a class="material-filename"></a></p>
                    <br><br>
                    <div class="mdc-text-field mdc-text-field--textarea">
                        <textarea class="mdc-text-field__input" name="extra" rows="8" cols="40" maxlength="255"></textarea>
                        <div class="mdc-notched-outline">
                            <div class="mdc-notched-outline__leading"></div>
                            <div class="mdc-notched-outline__notch">
                                <label class="mdc-floating-label">Extra Information:</label>
                            </div>
                            <div class="mdc-notched-outline__trailing"></div>
                        </div>
                    </div>
                    <div class="mdc-text-field-helper-line">
                        <div class="mdc-text-field-helper-text">Max. 255 characters</div>
                    </div>
                </div>
                <div class="other-content fear">
                    <div class="mdc-text-field mdc-text-field--textarea">
                        <textarea name="content" class="mdc-text-field__input" rows="8" cols="40" maxlength="2048"></textarea>
                        <div class="mdc-notched-outline">
                            <div class="mdc-notched-outline__leading"></div>
                            <div class="mdc-notched-outline__notch">
                                <label class="mdc-floating-label">Content</label>
                            </div>
                            <div class="mdc-notched-outline__trailing"></div>
                        </div>
                    </div>
                    <div class="mdc-text-field-helper-line">
                        <div class="mdc-text-field-helper-text">Max. 2048 characters</div>
                    </div>
                </div>
            </form>
            <br><br>
            <div>
                <button class="mdc-button mdc-button--raised submit-materials">Submit Material</button>
            </div>
            <br><br>
        </div>
    </div>
<?php else:
    $material = new Material($path[4]);
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <br>
            <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/materials"; ?>"><-- Back To Materials</button>
            <h3 class="txt-ctr mdc-typography--headline4">View Material:</h3>
            <?php if( $material->constructed ): ?>
                <?php
                $source_path = "/poster.php?track=".$material->track;
                switch( $material->status ){
                    case -1:
                        $approval_status = "Denied";
                        $approval_color = "red-txt";
                        break;
                    case 0:
                        $approval_status = "Pending";
                        $approval_color = "grey-txt";
                        break;
                    case 1:
                        $approval_status = "Approved";
                        $approval_color = "green-txt";
                        break;
                }
                ?>
                <p>Type: <a class="grey-txt"><?php echo htmlspecialchars(ucwords($material->type)); ?></a></p>
                <p>Submitted On:
                    <a class="js-timer grey-txt"
                       data-timer-type="to-local-time"
                       data-time-format="F jS, Y h:i:sa"
                       data-show-local
                       data-time-date="<?php echo base64_encode($material->date->format(DATE_ATOM));?>"></a>
                </p>
                <p>Status: <b class="<?php echo $approval_color; ?>"><?php echo $approval_status; ?></b></p>
                <?php if( $material->status === -1 ): ?>
                    <p>Denial Reason:</p>
                    <blockquote class="red-blockquote"><?php echo htmlspecialchars($material->denial_reason); ?></blockquote>
                <?php endif; ?>
                <p>Title: <a><?php echo htmlspecialchars($material->title); ?></a></p>
                <?php if( $material->type === "poster" ): ?>
                    <p>Extra: <a class="grey-txt"><?php echo $material->content; ?></a></p>
                    <?php if( $material->status !== 1 ): ?>
                        <p class="small-txt red-txt">A watermark will automatically be added to the preview below once it is approved. Do NOT print the poster as it is right now!</p>
                    <?php endif; ?>
                    <p class="linkish"><a href="<?php echo $source_path; ?>" target="_blank">Click here to open in new tab</a></p>
                    <iframe class="pdf-preview-iframe" src="<?php echo $source_path; ?>"></iframe>
                <?php elseif($material->type === "other"): ?>
                    <p>Content: <a class="grey-txt"><?php echo htmlspecialchars($material->content); ?></a></p>
                <?php endif; ?>
            <?php else: ?>
                <div class="sub-container">
                    <p class="txt-ctr">That material could not be found</p>
                </div>
            <?php endif; ?>
            <br><br>
        </div>
    </div>
<?php endif; ?>