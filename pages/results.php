<?php if( ! isset($path[2]) || $path[2] === "" ): ?>
<?php foreach(Result::getAllResults() as $result): ?>
    <div class="mdc-card mdc-layout-grid__cell--span-4">

        <div class="mdc-card__primary-action change-page" data-page="/results/<?php echo htmlspecialchars($result->db_code); ?>">

            <div class="mdc-card__media mdc-card__media--16-9 " style="background-image: url(https://badgerherald.com/wordpress/wp-content/uploads/2016/10/giphy-23-1.gif);"></div>

            <div>
                <h2 class="mdc-typography mdc-typography--headline6 vote-card__pad"><?php echo htmlspecialchars($result->name); ?></h2>
            </div>

            <br>

        </div>

            <div class="mdc-card__actions">

                <div class="mdc-card__action-buttons">

                    <button class="mdc-button mdc-card__action mdc-card__action--button change-page" data-page="/results/<?php echo htmlspecialchars($result->db_code); ?>">View</button>

                </div>

                <div class="mdc-card__action-icons">

                    <button class="mdc-icon-button share-card material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true" data-share-url="<?php echo (web_ssl) ? "https://" : "http://"; echo addslashes(web_domain); ?>/results/<?php echo addslashes($result->db_code); ?>">share</button>

                </div>

            </div>

        </div>

    <?php endforeach; ?>
<?php else: ?>

    <?php
        $result = new Result($path[2]);
    ?>

    <?php if( $result->constructed ): ?>
        <?php
            $result_data = json_decode(file_get_contents(app_root."/public/static/elections/".$result->db_code."/results.json"), true);
        ?>

        <div class="mdc-card mdc-layout-grid__cell--span-12 mdc-card--outlined muli">

            <br>
            <h2 class="txt-ctr sub-container">Results: <?php echo htmlspecialchars($result->name); ?></h2>
            <div class="flx-ctr">

                <a class="mdc-button" target="_blank" href="/static/elections/<?php echo addslashes($result->db_code); ?>/votes.json">Votes JSON</a>

            </div>

            <hr class="sub-container">
            <div class="sub-container">

                <p>Total Eligible Voters: <b><?php echo $result_data["total_eligible_voters"]; ?></b></p>
                <p>Total Eligible Voters By Grade: </p>

                <div class="sub-container">

        <?php foreach( $result_data["eligible_voters_by_grade"] as $grade => $num_voters ): ?>
            <p><?php echo $grade." - <b>".$num_voters."</b>"; ?></p>
        <?php endforeach; ?>

                </div>

                <p>Total Votes: <b><?php echo $result_data["total_votes"]; ?></b></p>
                <p>Votes By Grade:</p>

                <div class="sub-container">

                    <?php foreach( $result_data["votes_by_grade"] as $grade => $num_votes ): ?>
                        <p><?php echo $grade." - <b>".$num_votes."</b>"; ?></p>
                    <?php endforeach; ?>

                </div>

            </div>
            <hr class="sub-container">
            <div class="sub-container">

                <?php foreach( $result_data["rounds"] as $round => $round_data ): ?>

                    <h3>Round <?php echo ($round + 1); ?></h3>

                    <p><?php echo $round_data["total_votes"]." votes this round"; ?></p>

                    <ul class="mdc-list mdc-list--two-line mdc-list--non-interactive">

                        <?php foreach( $round_data["votes"] as $candidate_id => $vote_count ): ?>
                            <?php
                            $vote_percentage =  strval((int) (($vote_count / $round_data["total_votes"]) * 10000));
                            $vote_percentage = substr($vote_percentage, 0, strlen($vote_percentage) - 2).".".substr($vote_percentage, strlen($vote_percentage) - 2);
                            ?>

                            <li class="mdc-list-item">
                            <span class="mdc-list-item__text">

                                <span class="mdc-list-item__primary-text"><?php echo htmlspecialchars($result_data["candidates"][$candidate_id]); ?></span>
                                <span class="mdc-list-item__secondary-text"><?php echo htmlspecialchars($vote_count)." votes - ~".$vote_percentage."%"; ?></span>

                            </span>
                            </li>

                        <?php endforeach; ?>

                    </ul>
                    <br>
                <?php endforeach; ?>

                <h3>Winner: <a class="green-txt muli"><?php echo htmlspecialchars($result_data["candidates"][$result_data["winner"]]); ?></a></h3>
                <br><br>
            </div>

        </div>
    <?php else: ?>

        <p class="mdc-layout-grid__cell--span-12">There is no past election with the ID passed in the url.</p>

    <?php endif; ?>
<?php endif; ?>
