<?php if( ! isset($path[4]) || trim($path[4]) === "" ): ?>

<?php elseif( $path[4] === "upload" ):
    Web::addScript("/static/js/campaign/materials/upload.js");
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">Submit Materials for Approval</h2>
            <form class="no-submit" data-action="/requests.php" data-callback="change-page" data-reload-page="/campaign/<?php
                echo htmlspecialchars($candidate->id."/materials");
            ?>">
                <input name="token" type="hidden" value="<?php
                    echo htmlspecialchars($user->makeFormToken("create_materials", $candidate->id, Web::UTCDate("+1 day")));
                    ?>">
                <div class="mdc-select">
                    <i class="mdc-select__dropdown-icon"></i>
                    <select class="mdc-select__native-control select-material-type" name="type">
                        <option value="" disabled selected></option>
                        <option value="poster">
                            Poster
                        </option>
                        <option value="other">
                            Other
                        </option>
                    </select>
                    <label class="mdc-floating-label">Type</label>
                    <div class="mdc-line-ripple"></div>
                </div>


                <div class="poster-upload fear">

                </div>
                <div class="other-content fear">

                </div>
                <br><br>

            </form>
            <div>
                <button class="mdc-button mdc-button--raised submit-form">Submit Material</button>
                <br><br>
            </div>
        </div>
    </div>
<?php else: ?>

<?php endif; ?>
