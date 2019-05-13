<?php if( ! isset($path[4]) || trim($path[4]) === "" ): ?>

<?php elseif( $path[4] === "create" ):
    Web::addScript("/static/js/campaign/updates/create.js");
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">New Update</h2>
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell--span-8">
                    <div class="mdc-text-field mdc-text-field--outlined full-width-txt-field">
                        <input class="mdc-text-field__input update-title" name="update-title" value="Update Title" maxlength="64">
                        <div class="mdc-notched-outline">
                            <div class="mdc-notched-outline__leading"></div>
                            <div class="mdc-notched-outline__notch">
                                <label class="mdc-floating-label">Title</label>
                            </div>
                            <div class="mdc-notched-outline__trailing"></div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <br>
            <div id="markdown-editor">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-6 muli">
                        <div class="markdown-editor-buttons">
                            <button class="mdc-icon-button material-icons titlify">title</button>
                            <button class="mdc-icon-button material-icons boldify">format_bold</button>
                            <button class="mdc-icon-button material-icons italify">format_italic</button>
                            <button class="mdc-icon-button material-icons strikify">strikethrough_s</button>
                            <button class="mdc-icon-button material-icons colorify rainbow">format_color_text</button>
                            <button class="mdc-icon-button material-icons clearify">format_clear</button>
                            <button class="mdc-icon-button material-icons imagify">add_photo_alternate</button>
                            <button class="mdc-icon-button material-icons quotify">format_quote</button>
                            <button class="mdc-icon-button material-icons desktop-only splitify fear">vertical_split</button>
                            <button class="mdc-icon-button material-icons desktop-only fullify">view_stream</button>
                        </div>
                        <div class="mdc-text-field mdc-text-field--textarea mdc-text-field--fullwidth	">
<!--                            <div class="mdc-text-field-character-counter">0 / 1024</div>-->
                            <textarea name="content" class="mdc-text-field__input" rows="8" cols="40" maxlength="1024">
# Hey!
## This is *Markdown* :sparkles:
It's what you'll be using to make your ~~great~~ {**amazing**}(red) posts.

To learn more go here [here](https://<?php echo htmlspecialchars(web_domain); ?>/help/markdown)
Even if you're already familiar with it from GitHub, you should check out the link because we added some of our own awesome new features.
psst, this preview updates automatically as you write. Isn't that {awesome}(rainbow)!</textarea>
                            <div class="mdc-notched-outline">
                                <div class="mdc-notched-outline__leading"></div>
                                <div class="mdc-notched-outline__notch">
                                    <label for="textarea" class="mdc-floating-label">Post Editor</label>
                                </div>
                                <div class="mdc-notched-outline__trailing"></div>
                            </div>
                        </div>
                        <a class="small-txt">Max length: 1024 characters.</a>
                    </div>
                    <div class="mdc-layout-grid__cell--span-6 muli">
                        <p>Preview</p>
                        <div class="markdown-preview"></div>
                    </div>
                </div>
            </div>
            <button class="mdc-button mdc-button--raised">Submit Post</button>
            <br><br>
        </div>
    </div>
<?php else: ?>

<?php endif; ?>
