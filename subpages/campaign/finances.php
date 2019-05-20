<?php if( ! isset($path[4]) || trim($path[4]) === "" ): ?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <div class="sub-container">
        <h2 class="txt-ctr mdc-typography--headline4">Finances</h2>
        <button class="mdc-button mdc-button--outlined sub-page-change" data-page="/campaign/<?php echo $candidate->id."/finances/create"; ?>"><i class="material-icons">add</i> &nbsp; New Finance</button>
        <br><br>
        <p>Spending Limit: <a class="grey-txt">$50.00</a></p>
        <p>Total Spent: <a class="<?php $spent = Finance::getTotalSpending($candidate->id); echo ($spent > 50) ? "red-txt" : "green-txt"; ?>">
                $<?php echo money_format("%i", $spent); ?></a>
        </p>

        <ul class="mdc-list mdc-list--two-line no-top-pad">
            <?php foreach( Finance::getFinancesByCandidate($candidate->id) as $finance ): ?>
                <li class="mdc-list-item sub-page-change"
                    tabindex="0"
                    data-page="/campaign/<?php echo $candidate->id."/finances/".$finance->track; ?>">
                            <span class="mdc-list-item__text">
                                <span class="mdc-list-item__primary-text uline grey-txt"><?php echo htmlspecialchars($finance->title); ?></span>
                                <span class="mdc-list-item__secondary-text js-timer">
                                    <a class="green-txt">$<?php echo money_format("%i", $finance->amount); ?></a>
                                    <a class="js-timer"
                                       data-timer-type="to-local-time"
                                       data-time-format="F jS, Y h:i:sa"
                                       data-time-date="<?php echo base64_encode($finance->time->format(DATE_ATOM)); ?>">Update Time</a>
                                </span>
                            </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php elseif( $path[4] === "create" ): ?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <br><br>
    <div class="sub-container">
        <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/finances"; ?>"><-- Back To Finances</button>
        <h2 class="txt-ctr mdc-typography--headline4">Create Financial Record</h2>
        <form data-action="/requests.php" data-callback="change-page" data-reload-page="/campaign/<?php echo $candidate->id."/finances"?>">
            <input type="hidden" name="token" value="<?php echo $user->makeFormToken("create_finance", $candidate->id, Web::UTCDate("+1 Day")); ?>">
            <div class="mdc-text-field mdc-text-field--outlined">
                <input class="mdc-text-field__input" name="title" maxlength="128" placeholder="e.g. Campaign stickers">
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label class="mdc-floating-label">Title/Items Purchased</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div><br><br>
            <div class="mdc-text-field mdc-text-field--outlined">
                <input class="mdc-text-field__input" maxlength="9" name="amount" pattern="^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$" placeholder="12.67">
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label class="mdc-floating-label">Amount Spent</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Amount must be formatted like a monetary amount!</div>
            </div>
            <div class="mdc-text-field mdc-text-field--outlined">
                <input class="mdc-text-field__input" type="url" name="link">
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label class="mdc-floating-label">Link to receipt</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text" aria-hidden="true">Can be an image or pdf. Must be a valid url (make sure it starts with http:// or https://)! If hosted on Google Drive, make sure that it is shared with everyone.</div>
            </div>
            <div class="mdc-text-field mdc-text-field--textarea">
                <textarea name="use" class="mdc-text-field__input" rows="4" cols="30" maxlength="255" placeholder="We distributed these stickers to prospective voters on the bridge."></textarea>
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label for="textarea" class="mdc-floating-label">Use/Extra Information</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text">Its use and or anything out of the ordinary about this purchase. Max 255 characters.</div>
            </div>
            <br>
        </form>
        <div>
            <button class="mdc-button mdc-button--raised submit-form">Submit</button>
        </div>
        <br><br>
    </div>
</div>
<?php else:
$finance = new Finance($path[4]);
?>
<div class="mdc-card mdc-layout-grid__cell--span-12">
    <br><br>
    <div class="sub-container">
        <button class="mdc-button sub-page-change" data-page="/campaign/<?php echo $candidate->id."/finances"; ?>"><-- Back To Finances</button>
        <h2 class="txt-ctr mdc-typography--headline4">View Finance</h2>
        <?php if( $finance->constructed ): ?>
            <p>Date Created: <a class="js-timer grey-txt"
                                data-timer-type="to-local-time"
                                data-time-format="F jS, Y h:i:sa"
                                data-time-date="<?php echo base64_encode($finance->time->format(DATE_ATOM)); ?>">Update Time</a></p>
            <p>Amount: <a class="green-txt">$<?php echo money_format("%i", $finance->amount); ?></a></p>
            <p>Title: <a class="grey-txt"><?php echo htmlspecialchars($finance->title); ?></a></p>
            <p>Use/Extra: <a class="grey-txt"><?php echo htmlspecialchars($finance->extra); ?></a></p>
        <?php else:?>
            <p>No Finance record was found with that ID</p>
        <?php endif; ?>
    </div>
    <br><br>
</div>
<?php endif; ?>
