<?php if( ! isset($path[2]) || $path[2] === ""  ): ?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <div class="sub-container">
        <h2 class="txt-ctr">Help</h2>
        <h3>Tech Checkup:</h3>
        <div class="sub-container">
            <!-- TODO INCLUDE SOME TECH COMPATABILITY CHECKUPS HERE -->
            <h3></h3>
            <a class="js-timer" data-timer-type="current" data-time-format="F m, y h:i:sa"></a>

        </div>
        <h3>FAQS:</h3>
        <!-- TODO MAKE A LIST OF THE AVAILABLE FAQS HERE -->
    </div>
</div>
<?php else: ?>
<?php // A SPECIFIC FAQ WAS REQUESTED TODO DISPLAY THE FAQ CONTENT ?>

<?php endif; ?>
