<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = Session::getUser();
Web::addScript("/static/js/contact.js");
?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <div class="sub-container">
        <h3 class="txt-ctr">Contact Us:</h3>
        <form class="prevent_contact">
            <input type="hidden" name="token" value="<?php echo addslashes($user->makeFormToken("submit_contact", "", Web::UTCDate("+1 day"))); ?>">
            <div class="flx-ctr">
                <div class="mdc-text-field mdc-text-field--textarea mdc-text-field--no-label">
                    <textarea class="mdc-text-field__input" name="message" rows="8" cols="40" aria-label="message"></textarea>
                    <div class="mdc-notched-outline">
                        <div class="mdc-notched-outline__leading"></div>
                        <div class="mdc-notched-outline__trailing"></div>
                    </div>
                </div>
            </div>
            <br>
            <div class="flx-ctr">
                <button class="mdc-button mdc-button--outlined">Submit</button>
            </div>
        </form>
        <br>
    </div>
</div>