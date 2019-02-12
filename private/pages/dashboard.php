<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = new User(Session::getEmail(), Session::getUserId());
Web::addScript("/static/js/dashboard.js");
?>
<div class="mdl-grid">

<?php switch($user->status):
case -1: ?>
    <div class="unready" data-type="std-card-cont">
        <div class="unready" data-type="std-expand"></div>
        <h3 class="sumana text-center card-heading">Email Not Recognized</h3>
        <div class="sub-container">
            <p class="text-center "><a>We could not locate a user with the email address that was provided by Google. Please fill out the form below to request the ability to vote and you will be notified by email when your request has been approved.</a></p>
            <form>
                <div class="unready" data-type="f-txt-input">
                    <input class="mdl-textfield__input" name="full_name" type="text" required>
                        <label class="mdl-textfield__label">Full Name</label>
                </div><br>
                <div class="unready" data-type="f-txt-input">
                    <select class="mdl-textfield__input" name="grade" required>
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
                    <input class="mdl-textfield__input" name="osis" type="text" pattern="^\d{0,9}$">
                    <label class="mdl-textfield__label">OSIS</label>
                    <span class="mdl-textfield__error">Input is not an OSIS!</span>
                </div><br>
                <button class="unready" data-type="btn-rpl-act">
                    Submit
                </button>
            </form>
            <br>
        </div>
    </div>
    <?php break; ?>
<?php case 0: ?>
    <?php break; ?>
<?php case 1: ?>
    <?php break; ?>
<?php endswitch; ?>

</div>
