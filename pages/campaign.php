<?php
signInRequired();
$user = Session::getUser();
if( ! $user->isManager() ){
    replyError("Access Denied", "You do not have sufficient permissions to view this page.");
    exit;
}
$for = $user->managerOf();
?>
<?php if( !isset($path[2]) || $path[2] === "" ): ?>
    <?php if( count($for) === 1 ){
        Web::sendRedirect("/campaign/".$for[0]->id."/");
        exit;
    } ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <h3 class="txt-ctr">Select A Candidate To Manage:</h3>
        <div class="sub-container">
            <?php foreach($for as $candidate): ?>
                <p class="change-page txt-ctr clickable click-hover linkish" data-page="/campaign/<?php echo htmlspecialchars($candidate->id); ?>"><?php echo htmlspecialchars($candidate->name); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php else:
    // There is a candidate id in the url, manage that specific candidate
    header("X-Load-Sub: True");
    Web::addScript("/static/js/campaign.js");
    $candidate = new Candidate($path[2]);
    ?>
    <div class="mdc-tab-bar mdc-layout-grid__cell--span-12" role="tablist" data-mdc-auto-init="MDCTabBar">
        <div class="mdc-tab-scroller">
            <div class="mdc-tab-scroller__scroll-area">
                <div class="mdc-tab-scroller__scroll-content">
                    <button class="mdc-tab sub-page-tab mdc-tab--active sub-page-change" data-page="/campaign/<?php echo $candidate->id; ?>/profile">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">person</span>
                            <span class="mdc-tab__text-label">Profile</span>
                        </span>
                            <span class="mdc-tab-indicator mdc-tab-indicator--active">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                        <span class="mdc-tab__ripple"></span>
                    </button>
                    <button class="mdc-tab sub-page-tab sub-page-change" data-page="/campaign/<?php echo $candidate->id; ?>/updates">
                          <span class="mdc-tab__content">
                              <span class="mdc-tab__icon material-icons" aria-hidden="true">public</span>
                              <span class="mdc-tab__text-label">Updates</span>
                          </span>
                      <span class="mdc-tab-indicator">
                              <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                          </span>
                      <span class="mdc-tab__ripple"></span>
                    </button>
                    <button class="mdc-tab sub-page-tab sub-page-change" data-page="/campaign/<?php echo $candidate->id; ?>/finances">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">attach_money</span>
                            <span class="mdc-tab__text-label">Finances</span>
                        </span>
                            <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                        <span class="mdc-tab__ripple"></span>
                    </button>
                    <button class="mdc-tab sub-page-tab sub-page-change" data-page="/campaign/<?php echo $candidate->id; ?>/materials">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">format_shapes</span>
                            <span class="mdc-tab__text-label">Materials</span>
                        </span>
                            <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                        <span class="mdc-tab__ripple"></span>
                    </button>
                    <button class="mdc-tab sub-page-tab sub-page-change" data-page="/campaign/<?php echo $candidate->id; ?>/strikes">
                        <span class="mdc-tab__content">
                            <span class="mdc-tab__icon material-icons" aria-hidden="true">gavel</span>
                            <span class="mdc-tab__text-label">Strikes</span>
                        </span>
                        <span class="mdc-tab-indicator">
                            <span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span>
                        </span>
                        <span class="mdc-tab__ripple" ></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell--span-12">
        <div id="sub-variable-region" class="mdc-layout-grid__inner"></div>
    </div>

<?php endif; ?>