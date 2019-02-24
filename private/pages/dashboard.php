<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = Session::getUser();
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
            <form id="unrecognized" data-action="/requests.php" data-callback="reload">
                <input name="token" type="hidden" value="<?php echo $user->makeFormToken("unrecognized", "submit", strtotime("+ 1 hour")); ?>">
                <div class="unready" data-type="f-txt-input">
                    <input class="mdl-textfield__input validate" name="full_name" type="text" data-validation="r">
                        <label class="mdl-textfield__label">Full Name*</label>
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
                    <label class="mdl-textfield__label">Grade*</label>
                </div><br>
                <div class="unready" data-type="f-txt-input">
                    <input class="mdl-textfield__input validate" name="osis" type="text" pattern="^\d{0,9}$" data-validation="pr">
                    <label class="mdl-textfield__label">OSIS*</label>
                    <span class="mdl-textfield__error">Input is not an OSIS!</span>
                </div><br><br>
                <p class="faculty-warning fear mdl-color-text--red-600">This is meant for faculty only! Setting your grade to faculty means that your votes will have no effect towards elections! <b>This cannot be changed!</b></p>
                <p class="mdl-color-text--red-500 error-text fear">There were errors with your submission:</p>
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
    <?php
        $u_req = $user->unrecognized_request;
        $data_list = array(
            array("Request ID", $u_req['track'], "device_hub"),
            array("Name", $u_req['name'], "person_pin"),
            array("Email", $u_req['email'], "contact_mail"),
            array("Grade", $u_req['grade'], "school"),
            array("OSIS", $u_req['osis'], "dialpad"),
            array("Submitted On", date("F d, Y  h:ia", strtotime($u_req['created'])), "access_time")
        );
    ?>
    <div class="unready" data-type="std-card-cont">
        <div class="unready" data-type="std-expand"></div>
        <h3 class="sumana text-center card-heading">Email Not Recognized</h3>
        <div class="sub-container">
            <p class="text-center"><a>Your request to be able to vote has been received. The details are below. You will receive an email email the request has been approved.</a></p>
            <ul class="mdl-list">
                <?php foreach($data_list as $i ): ?>
                <li class="mdl-list__item">
                    <span class="mdl-list__item-primary-content">
                        <i class="material-icons mdl-list__item-icon"><?php echo $i[2]; ?></i>
                        <?php echo htmlentities($i[0]); ?> :&nbsp;<a class="notranslate"><?php echo htmlentities($i[1]); ?></a>
                    </span>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </div>
    <?php break; ?>
<?php case 1: ?>
    <?php print_r($user->getElections()); foreach( $user->getElections() as $e ): ?>
        <div class="unready election-card" data-type="small-card-cont">
            <div class="mdl-card__title mdl-card--expand mdl-color--teal-300 mdl-color-text--white">
                <h2 class="mdl-card__title-text"><?php echo htmlentities($e->name); ?></h2>
            </div>
            <div class="mdl-card__supporting-text mdl-color-text--grey-600">
                Non dolore elit adipisicing ea reprehenderit consectetur culpa.
            </div>
            <form data-action="/requests.php" data-callback="page-url" data-page="/vote/<?php echo urlencode($e->db_code)."/".urlencode(ucwords($e->name)); ?>"></form>
            <div class="mdl-card__actions mdl-card--border">
                <a class="mdl-button form-submit mdl-js-button">Vote</a>
                <div class="unready button-spinner fear" data-type="spinner"></div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php break; ?>
<?php endswitch; ?>

</div>
