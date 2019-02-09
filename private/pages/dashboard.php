<?php
//TODO FINISH THIS PAGE
signInRequired();
$user = new User(Session::getEmail(), Session::getUserId());
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

            </form>
        </div>
    </div>
    <?php break; ?>
<?php case 0: ?>
    <?php break; ?>
<?php case 1: ?>
    <?php break; ?>
<?php endswitch; ?>

</div>
