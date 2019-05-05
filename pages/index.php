<?php
if( Session::hasSession() ){
    header("Location: /load.php?page=/elections");
    exit;
}
?>
<!-- Script tag doesn't need nonce because apis.google.com is a trusted whitelisted source -->
<script src="https://apis.google.com/js/platform.js"></script>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <!-- ... content ... -->
    <div class="card-expand-default"></div>
    <h2 class="txt-ctr">Welcome</h2>
    <div class="sub-container">
        <!-- TODO small message here -->
        <p class="muli">This site was created for the purpose of making it more accessible for students to vote for the Student Union Leaders online. There are many structures that have been put into place to ensure the integrity of our elections. From now on, <b>all votes cast will be anonymous</b>. To learn more about that and the other new features, please visit our help page. If you want to tell us about any bugs with the site, suggestions, or report misconduct, you can do so, anonymously if you wish, on our contact page. Please note, that regardless of whether you choose to be anonymous or not, you will be required to be signed in to the site to send a message in order to prevent spam. Before you vote, we suggest you take a look at some statements from the candidates you will be choosing from on our candidates page.<br><br></p>
        <?php if( ! Session::hasSession() ): ?>

            <div class="flx-ctr">
                <div class="g-signin2 notranslate" data-onsuccess="onSignIn" data-theme="light" data-longtitle="true"></div>
            </div>
            <br>

        <?php endif; ?>
    </div>
</div>
