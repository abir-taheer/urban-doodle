<?php
signInRequired();
$user = Session::getUser();
if( ! $user->isAdmin() ){
    replyError("Not Admin", "You do not have permissions to access this page.");
}
Web::addScript("/static/js/admin.js");
header("X-Load-Sub: True");
?>
<div class="mdc-tab-bar mdc-layout-grid__cell--span-12" role="tablist" data-mdc-auto-init="MDCTabBar">
    <div class="mdc-tab-scroller">
        <div class="mdc-tab-scroller__scroll-area">
            <div class="mdc-tab-scroller__scroll-content">
                <button class="mdc-tab sub-page-tab mdc-tab--active sub-page-change" data-page="/admin/elections">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">how_to_vote</span>
                            <span class="mdc-tab__text-label">Elections</span>
                        </span>
                    <span class="mdc-tab-indicator mdc-tab-indicator--active">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                    <span class="mdc-tab__ripple"></span>
                </button>
                <button class="mdc-tab sub-page-tab sub-page-change" data-page="/admin/live_results">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">attach_money</span>
                            <span class="mdc-tab__text-label">Finances</span>
                        </span>
                    <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                    <span class="mdc-tab__ripple" data-mdc-auto-init="MDCRipple"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="mdc-layout-grid__cell--span-12">
    <div id="sub-variable-region" class="mdc-layout-grid__inner"></div>
</div>