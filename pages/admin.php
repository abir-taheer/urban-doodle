<?php
signInRequired();
$user = Session::getUser();
if( ! $user->isAdmin() ){
    replyError("Not Admin", "You do not have permissions to access this page.");
    exit;
}
Web::addScript("/static/js/admin.js");
header("X-Load-Sub: True");
?>
<div class="mdc-tab-bar mdc-layout-grid__cell--span-12 no-print" role="tablist" >
    <div class="mdc-tab-scroller">
        <div class="mdc-tab-scroller__scroll-area">
            <div class="mdc-tab-scroller__scroll-content">
                <?php if($user->hasAdminPrivilege("elections")): ?>
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
                <?php endif; ?>
                <button class="mdc-tab sub-page-tab sub-page-change" data-page="/admin/candidates">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">supervised_user_circle</span>
                            <span class="mdc-tab__text-label">Candidates</span>
                        </span>
                    <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                    <span class="mdc-tab__ripple"></span>
                </button>
                <button class="mdc-tab sub-page-tab sub-page-change" data-page="/admin/materials">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">format_shapes</span>
                            <span class="mdc-tab__text-label">Materials</span>
                        </span>
                    <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                    <span class="mdc-tab__ripple"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="mdc-layout-grid__cell--span-12">
    <div id="sub-variable-region" class="mdc-layout-grid__inner"></div>
</div>