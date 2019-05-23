<?php if( !isset($path[3]) || trim($path[3]) === "" ):?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">Materials</h2>
            <p class="linkish sub-page-change clickable txt-ctr" data-page="/admin/materials/elections">Sort By Elections</p>
            <p class="linkish sub-page-change clickable txt-ctr" data-page="/admin/materials/all">Sort By All</p>
        </div>
    </div>
<?php elseif($path[3] === "elections"): ?>
    <?php if( !isset($path[4]) || trim($path[4]) === "" ):?>
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
                        <button class="mdc-button mdc-card__action mdc-card__action--button sub-page-change" data-page="/admin/materials/elections/<?php echo htmlspecialchars($e->db_code); ?>">View Materials</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif($path[3] === "elections"): ?>
    <?php endif; ?>
<?php elseif($path[3] === "all"): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">Materials</h2>
            <?php foreach( [
                               "approved"=> [
                                   Material::getApprovedMaterials(),
                                   "green-txt"
                               ],
                               "denied"=> [
                                   Material::getDeniedMaterials(),
                                   "red-txt"
                               ],
                               "pending" => [
                                   Material::getPendingMaterials(),
                                   "grey-txt"
                               ]
                           ] as $type => $materials_list):
                ?>
                <h2 class="<?php echo $materials_list[1]; ?>">
                    <?php echo ucwords($type); ?>
                </h2>
                <?php if( count($materials_list[0]) === 0):?>
                <p class="small-txt">There currently aren't any <?php echo $type; ?> materials</p>
            <?php endif; ?>
                <ul class="mdc-list mdc-list--two-line no-top-pad">
                    <?php foreach( $materials_list[0] as $material ): ?>
                        <li class="mdc-list-item sub-page-change"
                            tabindex="0"
                            data-page="/admin/materials/view/<?php echo $material->track; ?>">
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
<?php elseif($path[3] === "view"):
    Web::addScript("/static/js/admin/materials.js");
    $material = new Material($path[4]); ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <br>
            <button class="mdc-button sub-page-change" data-page="/admin/materials/"><-- Back To Materials</button>
            <h3 class="txt-ctr mdc-typography--headline4">View Material:</h3>
            <?php if( $material->constructed ): ?>
                <?php
                $candidate = new Candidate($material->candidate_id);
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
                <br><br>
                <form data-action="/requests.php" data-callback="reload" data-reload-page="/admin/materials">
                    <input type="hidden" name="token" value="<?php echo $user->makeFormToken("review_material", $material->track, Web::UTCDate("+1 day")); ?>">
                    <div class="mdc-select">
                        <i class="mdc-select__dropdown-icon"></i>
                        <select class="mdc-select__native-control select-approval-type" name="type">
                            <option value="approve">
                                Approve
                            </option>
                            <option value="deny" selected>
                                Deny
                            </option>
                        </select>
                        <label class="mdc-floating-label">Approval</label>
                        <div class="mdc-line-ripple"></div>
                    </div>
                    <br><br>
                    <div class="denial-form">
                        <div class="mdc-text-field mdc-text-field--textarea">
                            <textarea name="reason" class="mdc-text-field__input" rows="8" cols="40"></textarea>
                            <div class="mdc-notched-outline">
                                <div class="mdc-notched-outline__leading"></div>
                                <div class="mdc-notched-outline__notch">
                                    <label for="textarea" class="mdc-floating-label">Denial Reason</label>
                                </div>
                                <div class="mdc-notched-outline__trailing"></div>
                            </div>
                        </div>
                    </div>
                </form>
                <br>
                <button class="mdc-button mdc-button--raised submit-form">Submit</button>
            <?php else: ?>
                <div class="sub-container">
                    <p class="txt-ctr">That material could not be found</p>
                </div>
            <?php endif; ?>
            <br><br>
        </div>
    </div>
<?php endif; ?>