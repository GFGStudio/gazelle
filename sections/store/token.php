<?php

$Cost = 1000;

$Purchase = "1 freeleech token";
$UserID = $LoggedUser['ID'];

$DB->query("
  SELECT BonusPoints
  FROM users_main
  WHERE ID = $UserID");

if ($DB->has_results()) {
    list($Points) = $DB->next_record();

    if ($Points >= $Cost) {
        $DB->query("
          UPDATE users_main
          SET BonusPoints = BonusPoints - $Cost,
            FLTokens = FLTokens + 1
          WHERE ID = $UserID");

        $DB->query("
          UPDATE users_info
          SET AdminComment = CONCAT('".sqltime()." - Purchased a freeleech token from the store\n\n', AdminComment)
          WHERE UserID = $UserID");

        $Cache->delete_value('user_info_heavy_'.$UserID);
        $Worked = true;
    } else {
        $Worked = false;
        $ErrMessage = "Not enough ".BONUS_POINTS.".";
    }
}

View::show_header('Store'); ?>
<div>
  <h2>Purchase
    <?= $Worked?"Successful":"Failed"?>
  </h2>
  <div class="box">
    <p>
      <?= $Worked?("You purchased ".$Purchase):("Error: ".$ErrMessage)?>
    </p>
    <p>
      <a href="/store.php">Back to Store</a>
    </p>
  </div>
</div>
<?php
View::show_footer();
