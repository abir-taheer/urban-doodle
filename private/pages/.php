<script src="https://apis.google.com/js/platform.js"></script>
<div class="mdl-grid">
    <div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col">
        <div class="mdl-card__title mdl-card--expand mdl-color--teal-300"></div>
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