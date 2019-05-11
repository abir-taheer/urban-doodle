<?php if( ! isset($path[4]) || trim($path[4]) === "" ): ?>

<?php elseif( $path[4] === "create" ):
    Web::addScript("/static/js/campaign/updates/create.js");
    ?>
    <div class="mdc-card mdc-layout-grid__cell--span-12">
        <div class="sub-container">
            <h2 class="txt-ctr mdc-typography--headline4">New Update</h2>
            <div id="markdown-editor">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-12">
                        <p>Editor</p>
                        <textarea>
# Hey!

## This is *Markdown* :sparkles:

It's what you'll be using to make your **awesome** posts.

To learn more go here [here](https://<?php echo htmlspecialchars(web_domain); ?>/help/markdown)

Even if you're already familiar with it from GitHub, you should check out the link because we added some of our own awesome new features.

psst, this preview updates automatically as you write. Isn't that {awesome}(rainbow)!</textarea>
                    </div>
                    <div class="mdc-layout-grid__cell--span-12">
                        <p>Preview</p>
                        <div class="markdown-preview muli"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

<?php endif; ?>
