<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = new User(Session::getEmail(), Session::getUserId());
?>
<div class="mdl-grid">

<?php switch($user->status):
case -1: ?>
    <?php Web::addScript("/static/js/unrecognized.js"); ?>
    <div class="unready" data-type="std-card-cont">
        <div class="unready" data-type="std-expand"></div>
        <h3 class="sumana text-center card-heading">Email Not Recognized</h3>
        <div class="sub-container">
            <p class="text-center "><a>We could not locate a user with the provided email address. Please fill out the form below to request the ability to vote and you will be notified by email when your request has been approved.</a></p>
            <form id="unrecognized">
                <div class="unready" data-type="f-txt-input">
                    <input class="mdl-textfield__input validate" name="full_name" type="text" data-validation="r">
                        <label class="mdl-textfield__label">Full Name</label>
                </div><br>
                <div class="unready" data-type="f-txt-input">
                    <select class="mdl-textfield__input validate" name="grade" data-validation="r">
                        <option></option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="f">Faculty</option>
                    </select>
                    <label class="mdl-textfield__label">Grade</label>
                </div><br>
                <div class="unready" data-type="f-txt-input">
                    <input class="mdl-textfield__input validate" name="osis" type="text" pattern="^\d{0,9}$" data-validation="r">
                    <label class="mdl-textfield__label">OSIS</label>
                    <span class="mdl-textfield__error">Input is not an OSIS!</span>
                </div><br>
                <p class="incomplete-warning fear mdl-color-text--red-600">Your form is incomplete! Please fill it out completely!</p>
                <p class="faculty-warning fear mdl-color-text--red-600">This is meant for faculty only! Setting your grade to faculty means that your votes will have no effect towards elections! <b>This cannot be changed!</b></p>
                <div class="errors-list">
                    <p class="mdl-color-text--red-500">There were errors with your submission:</p>
                    <ul class="form-errors mdl-color-text--red-500">
                        <li>Your form is not complete!</li>
                    </ul>
                </div>
            </form>
            <div>
                <button class="unready form-submit" data-type="btn-rpl-act">
                    Submit
                </button>
                <div class="unready button-spinner fear" data-type="spinner"></div>
            </div>
            <br><br>
        </div>
    </div>
    <?php break; ?>
<?php case 0: ?>
    <?php break; ?>
<?php case 1: ?>
    <?php break; ?>
<?php endswitch; ?>

</div>
