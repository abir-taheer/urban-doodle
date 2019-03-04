<!-- Script tag doesn't need nonce because apis.google.com is a trusted whitelisted source -->
<script src="https://apis.google.com/js/platform.js"></script>
<div class="mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12">
    <!-- ... content ... -->
    <div class="card-expand-default"></div>
    <h2 class="txt-ctr">Welcome</h2>
    <div class="sub-container">
        <!-- TODO small message here -->
        <?php if( ! Session::hasSession() ): ?>

            <div class="flx-ctr">
                <div class="g-signin2 notranslate" data-onsuccess="onSignIn" data-theme="light" data-longtitle="true"></div>
            </div>
            <br>

        <?php endif; ?>
    </div>
</div>
