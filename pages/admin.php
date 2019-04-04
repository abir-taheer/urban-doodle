<?php
signInRequired();
$user = Session::getUser();
if( ! $user->isAdmin() ){
    replyError("Not Admin", "You do not have permissions to access this page.");
}
Web::addScript("/static/js/admin.js");
?>
<div class="mdc-tab-bar mdc-layout-grid__cell--span-12" role="tablist" data-mdc-auto-init="MDCTabBar">
    <div class="mdc-tab-scroller">
        <div class="mdc-tab-scroller__scroll-area">
            <div class="mdc-tab-scroller__scroll-content">
                <button class="mdc-tab mdc-tab--active" role="tab" aria-selected="true" tabindex="0">
                    <span class="mdc-tab__content">
                        <span class="mdc-tab__icon material-icons" aria-hidden="true">favorite</span>
                        <span class="mdc-tab__text-label">Favorites</span>
                    </span>
                    <span class="mdc-tab-indicator mdc-tab-indicator--active">
                        <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                    </span>
                    <span class="mdc-tab__ripple"></span>
                </button>
                <button class="mdc-tab" role="tab" aria-selected="true" tabindex="0">
                    <span class="mdc-tab__content">
                        <span class="mdc-tab__icon material-icons" aria-hidden="true">person</span>
                        <span class="mdc-tab__text-label">Banana</span>
                    </span>
                    <span class="mdc-tab-indicator">
                        <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                    </span>
                    <span class="mdc-tab__ripple" data-mdc-auto-init="MDCRipple"></span>
                </button>
                <button class="mdc-tab" role="tab" aria-selected="true" tabindex="0">
                    <span class="mdc-tab__content">
                        <span class="mdc-tab__icon material-icons" aria-hidden="true">contact_support</span>
                        <span class="mdc-tab__text-label">Support</span>
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
