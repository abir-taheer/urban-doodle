<?php
Web::addScript("/static/js/campaign/profile.js");
$user = Session::getUser();
?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <br>
    <div class="flx-ctr pos-rel">
        <div class="candidate-photo change-pic-overlay change-candidate-pfp">
            <p class="txt-ctr">Change Picture<br><i class="material-icons">camera_enhance</i></p>
        </div>
        <img class="candidate-photo change-candidate-pfp"
             src="/static/elections/<?php

             echo htmlspecialchars($candidate->db_code)."/candidates/".htmlspecialchars($candidate->id);

             ?>.jpg?nocache=<?php
             // Add a random query string to the candidate's profile pic image source
             // This is to keep the browser from displaying a cached version of the image
             // And also to make sure that they can see if changes happen effectively
             echo htmlspecialchars(bin2hex(random_bytes(2)));
             ?>"
                alt="Candidate Picture">
    </div>
    <form id="pfp-upload" enctype="multipart/form-data">
        <input name="token" type="hidden" value="<?php
        echo $user->makeFormToken("update_candidate_pfp", $candidate->id, Web::UTCDate("+1 day"));
        ?>">
        <input type="file" name="new_pfp">
    </form>
    <h2 class="txt-ctr muli"><?php echo htmlspecialchars($candidate->name); ?></h2>
    <h3 class="muli txt-ctr">Basic Information:</h3>
    <div class="flx-ctr">
        <button class="mdc-button new-info" data-form-token="<?php
            echo htmlspecialchars($user->makeFormToken("create_basic_info", $candidate->id, Web::UTCDate("+1 day")));
        ?>">New Info</button>
    </div>
    <br>
    <div class="sub-container">
        <form class="mdc-layout-grid__inner" id="reorder-items-form">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($user->makeFormToken("reorder_basic_info", $candidate->id, Web::UTCDate("+1 day"))); ?>">
            <?php foreach( $candidate->getBasicInfo() as $info ): ?>
            <div class="mdc-layout-grid__inner mdc-layout-grid__cell--span-12 full-item">
                <input type="hidden" name="item[]" value="<?php echo htmlspecialchars($info->track); ?>">
                <div class="mdc-layout-grid__cell--span-3-desktop info-type mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--span-2-tablet" data-actual-type="<?php echo htmlspecialchars($info->type); ?>"><?php echo htmlspecialchars($info->type); ?>:</div>
                <div class="mdc-layout-grid__cell--span-6-desktop info-content mdc-layout-grid__cell--span-4-phone mdc-layout-grid__cell--span-4-tablet muli" data-actual-content="<?php echo htmlspecialchars($info->content); ?>"><?php echo $info->getEncodedContent(); ?></div>

<!--                Icons at the right-->
                <div class="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-2-tablet mdc-layout-grid__cell--span-4-phone mdc-layout-grid__inner">
                    <i class="material-icons mdc-layout-grid__cell mdc-layout-grid__cell--span-1-phone clickable click-hover edit-info" data-form-token="<?php echo htmlspecialchars($user->makeFormToken("edit_basic_info", $info->track, Web::UTCDate("+1 day"))); ?>">edit</i>
                    <i class="material-icons mdc-layout-grid__cell mdc-layout-grid__cell--span-1-phone clickable click-hover delete-info" data-form-token="<?php echo htmlspecialchars($user->makeFormToken("delete_basic_info", $info->track, Web::UTCDate("+1 day"))); ?>" >clear</i>
                    <i class="material-icons mdc-layout-grid__cell mdc-layout-grid__cell--span-1-phone draggable desktop-only">drag_indicator</i>
                </div>
                <br class="mobile-only">
            </div>
            <?php endforeach; ?>
        </form>
    </div>
    <br><br>
</div>
<template id="pfp-confirm">
    <p class="txt-ctr">Are you sure you want to use this photo?</p>
    <div class="flx-ctr">
        <img class="candidate-photo pfp-confirm-set-src">
    </div>
</template>
<template id="new-basic-info">
    <br>
    <form id="basic-info-form">
        <input type="hidden" id="basic-info-token" name="token">
        <div class="mdc-text-field mdc-text-field--outlined">
            <input class="mdc-text-field__input" list="predefined-basic" name="type" id="new-info-type-input">
            <div class="mdc-notched-outline">
                <div class="mdc-notched-outline__leading"></div>
                <div class="mdc-notched-outline__notch">
                    <label class="mdc-floating-label">Info Type</label>
                </div>
                <div class="mdc-notched-outline__trailing"></div>
            </div>
        </div>
        <datalist id="predefined-basic">
            <option value="Website"></option>
            <option value="Email"></option>
            <option value="Phone"></option>
        </datalist>
        <br>
        <br>
        <div class="text-field-container">
            <div class="mdc-text-field text-field mdc-text-field--textarea">
                <div class="mdc-text-field-character-counter">0 / 255</div>
                <textarea name="content" class="mdc-text-field__input" maxlength="255" id="new-info-content-input"></textarea>
                <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label class="mdc-floating-label" for="textarea" style="">Content</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
            <div class="mdc-text-field-helper-line">
                <p class="mdc-text-field-helper-text">For Websites, leave out the <b>https://</b></p>
            </div>
        </div>
    </form>

</template>
<template id="delete-basic-confirm">
    <br>
    <h3>Are you sure you want to delete this basic info?</h3>
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-2-tablet mdc-layout-grid__cell--span-4-phone" id="info-type-display"></div>
            <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-4-phone" id="info-content-display"></div>
        </div>
    </div>
</template>