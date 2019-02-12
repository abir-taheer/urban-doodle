<!-- Script tag doesn't need nonce because apis.google.com is a trusted whitelisted source -->
<script src="https://apis.google.com/js/platform.js"></script>
<div class="mdl-grid">
    <div class="unready" data-type="std-card-cont">
        <div class="unready" data-type="std-expand"></div>
        <h3 class="sumana text-center card-heading">Welcome</h3>
        <div class="sub-container">
            <!-- TODO small message here -->
            <?php if( ! Session::hasSession() ): ?>

                <div class="center-flex">
                    <div class="g-signin2 notranslate" data-onsuccess="onSignIn" data-theme="light" data-longtitle="true"></div>
                </div>
                <br>

            <?php endif; ?>
        </div>
    </div>
</div>