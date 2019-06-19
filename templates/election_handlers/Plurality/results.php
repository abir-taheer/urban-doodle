<div class="sub-container">

    <p>Total Eligible Voters: <b><?php echo $result_data["total_eligible_voters"]; ?></b></p>
    <p>Total Votes: <b><?php echo $result_data["total_votes"]; ?></b></p>

    <?php if(count($result_data["eligible_voters_by_grade"]) > 1): ?>
    <p>Total Eligible Voters By Grade: </p>
    <div class="sub-container">

        <?php foreach( $result_data["eligible_voters_by_grade"] as $grade => $num_voters ): ?>
            <p><?php echo $grade." - <b>".$num_voters."</b>"; ?></p>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <?php if(count($result_data["votes_by_grade"]) > 1): ?>
    <p>Votes By Grade:</p>
    <div class="sub-container">

        <?php foreach( $result_data["votes_by_grade"] as $grade => $num_votes ): ?>
            <p><?php echo $grade." - <b>".$num_votes."</b>"; ?></p>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

</div>
<hr class="sub-container">
<div class="sub-container">
    <ul class="mdc-list mdc-list--two-line mdc-list--non-interactive">

        <?php foreach( $result_data["results"] as $candidate_id => $vote_count ): ?>
            <?php
            $vote_percentage = ($result_data["total_votes"] != 0 ) ? ($vote_count / $result_data["total_votes"]) * 100 : 0;
            $vote_percentage = round($vote_percentage ,2);
            ?>

            <li class="mdc-list-item">
                    <span class="mdc-list-item__text">
                        <span class="mdc-list-item__primary-text"><?php echo htmlspecialchars($result_data["candidates"][$candidate_id]); ?></span>
                        <span class="mdc-list-item__secondary-text"><?php echo htmlspecialchars($vote_count)." votes - ".$vote_percentage."%"; ?></span>
                    </span>
            </li>

        <?php endforeach; ?>

    </ul>
    <h3>Winner: <a class="muli"><?php echo ($result_data["winner"] === "Tie / No Winner") ? "<a class='red-txt'>Tie / No Winner</a>" : "<a class='green-txt'>&#10024; ".htmlspecialchars($result_data["candidates"][$result_data["winner"]])." &#10024;</a>"; ?></a></h3>
    <br><br>
</div>