<?php

$Cost = 5000;

if (isset($_POST['title'])) {
    if (strlen($_POST['title']) > 30) {
        error("Title too long");
    }

    $Title = htmlspecialchars($_POST['title'], ENT_QUOTES);
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
                Title = ?
              WHERE ID = ?", $Title, $UserID);

            $DB->query("
              UPDATE users_info
              SET AdminComment = CONCAT(NOW(), ' - Changed title to ', ?, ' via the store\n\n', AdminComment)
              WHERE UserID = ?", $Title, $UserID);
              
            $Cache->delete_value('user_info_'.$UserID);
            $Cache->delete_value('user_info_heavy_'.$UserID);
        } else {
            error("Not enough points");
        }
    }

    View::show_header('Store'); ?>
<div>
  <h2>Purchase Successful</h2>
  <div class="box">
    <p>You purchased the title
      "<?= $Title ?>"
    </p>
    <p>
      <a href="/store.php">Back to Store</a>
    </p>
  </div>
</div>
<?php
View::show_footer();
} else {
    View::show_header('Store'); ?>
<div>
  <div class="box text-align: center;">
    <form action="store.php" method="POST">
      <input type="hidden" name="item" value="title">
      <strong>
        Enter the title you want
      </strong>
      <br />
      <input type="text" name="title" maxlength="30" value="">
      <input type="submit">
    </form>
    <p>
      <a href="/store.php">Back to Store</a>
    </p>
  </div>
</div>
<?php
View::show_footer();
}
