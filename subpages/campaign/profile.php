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
        <img class="candidate-photo change-candidate-pfp" src="/static/elections/<?php echo addslashes($candidate->db_code)."/candidates/".addslashes($candidate->id); ?>.jpg" alt="Candidate Picture">
    </div>
    <form id="pfp-upload" enctype="multipart/form-data">
        <input name="token" type="hidden" value="<?php echo $user->makeFormToken("update_candidate_pfp", $candidate->id, Web::UTCDate("+1 day"));?>">
        <input type="file" name="new_pfp">
    </form>
    <br>
</div>
<template id="pfp-confirm">
    <p class="txt-ctr">Are you sure you want to use this photo?</p>
    <div class="flx-ctr">
        <img class="candidate-photo pfp-confirm-set-src">
    </div>
</template>