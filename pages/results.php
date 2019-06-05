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

                    <button class="mdc-icon-button share-card material-icons mdc-card__action mdc-card__action--icon--unbounded" title="Share" data-mdc-ripple-is-unbounded="true" data-share-url="<?php echo (web_ssl) ? "https://" : "http://"; echo htmlspecialchars(web_domain); ?>/results/<?php echo htmlspecialchars($result->db_code); ?>">share</button>

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
            require_once app_root."/classes/election_handlers/".$result->type.".php";
            $reflection = new \ReflectionClass( $result->type );
            $handler =  $reflection->newInstanceWithoutConstructor();
        ?>
        <div class="mdc-card mdc-layout-grid__cell--span-12 muli">

            <br>
            <h2 class="txt-ctr sub-container">Results: <?php echo htmlspecialchars($result->name); ?></h2>
            <div class="flx-ctr">

                <a class="mdc-button" target="_blank" href="/static/elections/<?php echo htmlspecialchars($result->db_code); ?>/votes.json">Votes JSON</a>

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
                <?php $handler::displayResults($result); ?>
                <h3>Winner: <a class="green-txt muli"><?php echo htmlspecialchars($result_data["candidates"][$result_data["winner"]]); ?></a></h3>
                <br><br>
            </div>

        </div>
    <?php else: ?>

        <p class="mdc-layout-grid__cell--span-12">There is no past election with the ID passed in the url.</p>

    <?php endif; ?>
<?php endif; ?>
