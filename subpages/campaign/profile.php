<div class="mdc-card mdc-layout-grid__cell--span-12">
    <br>
    <div class="flx-ctr pos-rel">
        <div class="candidate-photo change-pic-overlay">
            <p class="txt-ctr">Change Picture<br><i class="material-icons">camera_enhance</i></p>
        </div>
        <img class="candidate-photo" src="/static/elections/<?php echo addslashes($candidate->db_code)."/candidates/".addslashes($candidate->id); ?>.png" alt="Candidate Picture">
    </div>
    <input type="file" id="pfp-upload">
    <br>
</div>
<template id="pfp-confirm">
    <p class="txt-ctr">Are you sure you want to use this photo?</p>
    <div class="flx-ctr">
        <img class="candidate-photo pfp-confirm-set-src">
    </div>
</template>