<?php

# @todo Fix multiple authors and bounty preview

/*
 * Yeah, that's right, edit and new are the same place again.
 * It makes the page uglier to read but ultimately better as the alternative means
 * maintaining 2 copies of almost identical files.
 */

$NewRequest = $_GET['action'] === 'new';

if (!$NewRequest) {
    $RequestID = $_GET['id'];
    if (!is_number($RequestID)) {
        error(404);
    }
}

$Disabled = "";

if ($NewRequest && ($LoggedUser['BytesUploaded'] < 250 * 1024 * 1024 || !check_perms('site_submit_requests'))) {
    error('You do not have enough uploaded to make a request');
}

if (!$NewRequest) {
    if (empty($ReturnEdit)) {
        $Request = Requests::get_request($RequestID);
        if ($Request === false) {
            error(404);
        }

        // Define these variables to simplify _GET['groupid'] requests later on
        $CategoryID = $Request['CategoryID'];
        $Title = $Request['Title'];
        $TitleRJ = $Request['TitleRJ'];
        $TitleJP = $Request['TitleJP'];
        $CatalogueNumber = $Request['CatalogueNumber'];
        $DLsiteID = $Request['DLsiteID'];
        $Image = $Request['Image'];
        $GroupID = $Request['GroupID'];

        $VoteArray = Requests::get_votes_array($RequestID);
        $VoteCount = count($VoteArray['Voters']);

        $IsFilled = !empty($Request['TorrentID']);
        $CategoryName = $Categories[$CategoryID - 1];

        $ProjectCanEdit = (check_perms('project_team') && !$IsFilled && $CategoryID === '0');
        $CanEdit = ((!$IsFilled && $LoggedUser['ID'] === $Request['UserID'] && $VoteCount < 2) || $ProjectCanEdit || check_perms('site_moderate_requests'));

        if (!$CanEdit) {
            error(403);
        }

        $ArtistForm = Requests::get_artists($RequestID);
        $Tags = implode(', ', $Request['Tags']);
    }
}

  if ($NewRequest && !empty($_GET['artistid']) && is_number($_GET['artistid'])) {
      $DB->query("
        SELECT Name
        FROM artists_group
        WHERE artistid = ".$_GET['artistid']."
        LIMIT 1");
      list($ArtistName) = $DB->next_record();
      $ArtistForm = array(
      1 => array(array('name' => trim($ArtistName))),
    );
  } elseif ($NewRequest && !empty($_GET['groupid']) && is_number($_GET['groupid'])) {
      $ArtistForm = Artists::get_artist($_GET['groupid']);
      $DB->query("
        SELECT
        tg.Name,
        tg.NameRJ,
        tg.NameJP,
        tg.Year,
        tg.Studio,
        tg.Series,
        tg.CatalogueNumber,
        tg.DLsiteID,
        tg.WikiImage,
        GROUP_CONCAT(t.Name SEPARATOR ', '),
        tg.CategoryID
        FROM torrents_group AS tg
        JOIN torrents_tags AS tt ON tt.GroupID = tg.ID
        JOIN tags AS t ON t.ID = tt.TagID
        WHERE tg.ID = ".$_GET['groupid']);
      if (list($Title, $TitleRJ, $TitleJP, $Year, $Studio, $Series, $CatalogueNumber, $DLsiteID, $Image, $Tags, $CategoryID) = $DB->next_record()) {
          $GroupID = trim($_REQUEST['groupid']);
          $CategoryName = $Categories[$CategoryID - 1];
          $Disabled = 'readonly="readonly"';
      }
  }

View::show_header(($NewRequest ? 'Create Request' : 'Edit Request'), 'bbcode,requests,form_validate');
?>

<!-- HTML title -->
<div class="thin">
  <div class="header">
    <h2><?= ($NewRequest ? 'Create Request' : 'Edit Request') ?></h2>
  </div>

  <!-- New request hidden fields -->
  <div class="box pad">
    <form action="" method="post" id="request_form" onsubmit="Calculate();">
      <div>
        <?php  if (!$NewRequest) { ?>
        <input type="hidden" name="requestid"
          value="<?= $RequestID ?>" />
        <?php  } ?>
        <input type="hidden" name="auth"
          value="<?= $LoggedUser['AuthKey'] ?>" />
        <input type="hidden" name="action"
          value="<?= ($NewRequest ? 'takenew' : 'takeedit') ?>" />
      </div>

      <!-- Main table -->
      <table class="layout">
        <tr>
          <td colspan="2" class="center">Please make sure your request follows <a href="rules.php?p=requests">the request rules</a>!</td>
        </tr>
        <?php  if ($NewRequest || $CanEdit) { ?>

        <!-- Category -->
        <tr>
          <td class="label">Type</td>
          <td>
            <?php if (!empty($Disabled)) { ?>
            <input type="hidden" name="type"
              value="<?= $CategoryName ?>" />
            <select id="categories" name="type" onchange="Categories();" disabled="disabled">
              <?php } else { ?>
              <select id="categories" name="type" onchange="Categories();">
                <?php } ?>
                <?php foreach (Misc::display_array($Categories) as $Cat) { ?>
                <option value="<?= $Cat ?>" <?= (!empty($CategoryName) && ($CategoryName === $Cat) ? ' selected="selected"' : '') ?>><?=$Cat?>
                </option>
                <?php    } ?>
              </select>
          </td>
        </tr>

        <!-- Catalogue number -->
        <tr id="cataloguenumber_tr">
          <td class="label">Accession Number</td>
          <td>
            <input type="text" id="catalogue" name="cataloguenumber" size="15"
              value="<?= (isset($CatalogueNumber)?$CatalogueNumber:'') ?>"
              <?= $Disabled ?>/>
            <?php if (empty($Disabled)) { ?>
            <input type="button" autofill="jav" value="Autofill"></input>
            <?php } ?>
          </td>
        </tr>

        <!-- Three titles -->
        <tr>
          <td class="label">Torrent Title</td>
          <td>
            <input type="text" id="title" name="title" size="45"
              value="<?= (!empty($Title) ? $Title : '') ?>"
              <?= $Disabled ?>/>
          </td>
        </tr>

        <tr>
          <td class="label">Organism</td>
          <td>
            <input type="text" id="title_rj" name="title_rj" size="45"
              value="<?= (!empty($TitleRJ) ? $TitleRJ : '') ?>"
              <?= $Disabled ?>/>
          </td>
        </tr>

        <tr>
          <td class="label">Strain/Variety</td>
          <td>
            <input type="text" id="title_jp" name="title_jp" size="45"
              value="<?= !empty($TitleJP)?$TitleJP:'' ?>"
              <?= $Disabled ?>/>
          </td>
        </tr>
        <?php  } # Ends if NewRequest line 123 ?>

        <!-- Multiple artists -->
        <tr id="artist_tr">
          <td class="label">Author(s)</td>
          <td id="artistfields">
            <?php
              if (!empty($ArtistForm)) {
                $First = true;
                foreach ($ArtistForm as $Artist) {
                  # Cycle autocomplete
            ?>
            <input type="text" id="artist_0" name="artists[]" <?php Users::has_autocomplete_enabled('other'); ?>
            size="45" value="<?= display_str($Artist['name']) ?>" <?= $Disabled ?>/>
            <?php
              if (empty($Disabled)) {
                if ($First) { ?>
            <a class="add_artist_button brackets">+</a>
            <a class="remove_artist_button brackets">&minus;</a>
            <?php
                }
                $First = false;
              }
            ?>
            <br />
            <?php
                }
              } else {
            ?>
            <input type="text" id="artist_0" name="artists[]" <?php Users::has_autocomplete_enabled('other'); ?>
            size="45" <?= $Disabled ?>/>
            <?php if (empty($Disabled)) { ?>
            <a class="add_artist_button brackets">+</a> <a class="remove_artist_button brackets">&minus;</a>
            <?php
                } 
              }
            ?>
          </td>
        </tr>

        <!-- Picture -->
        <?php if ($NewRequest || $CanEdit) { ?>
        <tr id="image_tr">
          <td class="label">Picture</td>
          <td>
            <input type="text" id="image" name="image" size="45"
              value="<?= (!empty($Image) ? $Image : '') ?>"
              <?= $Disabled ?>/>
          </td>
        </tr>
        <?php  } ?>

        <!-- Tags -->
        <tr>
          <td class="label">Tags</td>
          <td>
            <?php
              $GenreTags = $Cache->get_value('genre_tags');
                if (!$GenreTags) {
                  $DB->query('
                    SELECT Name
                    FROM tags
                    WHERE TagType = \'genre\'
                    ORDER BY Name');
                  $GenreTags = $DB->collect('Name');
                  $Cache->cache_value('genre_tags', $GenreTags, 3600 * 6);
                }

                if (!empty($Disabled)) {
            ?>
            <select id="genre_tags" name="genre_tags" onchange="add_tag(); return false;" disabled="disabled">
            <?php } else { ?>
            <select id="genre_tags" name="genre_tags" onchange="add_tag(); return false;">
              <?php } ?>
              <option>---</option>
              <?php foreach (Misc::display_array($GenreTags) as $Genre) { ?>
              <option value="<?= $Genre ?>"><?= $Genre ?>
              </option>
              <?php  } ?>
            </select>
            <input type="text" id="tags" name="tags" size="45"
              value="<?= (!empty($Tags) ? display_str($Tags) : '') ?>"
              <?php Users::has_autocomplete_enabled('other'); ?>
              <?= $Disabled ?>/>
          </td>
        </tr>

        <!-- Description -->
        <tr>
          <td class="label">Description</td>
          <td>
            <?php new TEXTAREA_PREVIEW('description', 'req_desc', $Request['Description']??''); ?>
          </td>
        </tr>

        <!-- Existing torrent groups -->
        <?php  if (check_perms('site_moderate_requests')) { ?>
        <tr>
          <td class="label">Torrent Group</td>
          <td>
            If this request matches a torrent group <strong>already existing</strong> on the site, please indicate that here.<br />
            <?= site_url() ?>torrents.php?id=<input type="text"
              name="groupid"
              value="<?= isset($GroupID)?$GroupID:'' ?>"
              size="15" />
          </td>
        </tr>
        <?php
          } elseif (!empty($GroupID)
            #&& ($CategoryID !== 5) # ?
            #&& ($CategoryID !== 0) # ?
            ) {
        ?>

        <!-- Torrent group admin -->
        <tr>
          <td class="label">Torrent Group</td>
          <td>
            <a href="torrents.php?id=<?= $GroupID ?>"><?= site_url() ?>torrents.php?id=<?= $GroupID ?></a><br />
            This request <?= ($NewRequest ? 'will be' : 'is') ?> associated with the above torrent group.
            <?php if (!$NewRequest) { ?>
            If this is incorrect, please
            <a href="reports.php?action=report&amp;type=request&amp;id=<?= $RequestID ?>">report this request</a>
            so that staff can fix it.
            <?php } ?>
            <input type="hidden" name="groupid"
              value="<?= $GroupID ?>" />
          </td>
        </tr>

        <!-- Bounty -->
        <?php  } if ($NewRequest) { ?>
        <tr id="voting">
          <td class="label">Bounty</td>
          <td>
            <input type="text" id="amount_box" size="8"
              value="<?= (!empty($Bounty) ? $Bounty : '100') ?>"
              onchange="Calculate();" />
            <select id="unit" name="unit" onchange="Calculate();">
              <option value="mb" <?= (!empty($_POST['unit']) && $_POST['unit'] === 'mb' ? ' selected="selected"' : '') ?>>MB
              </option>
              <option value="gb" <?= (!empty($_POST['unit']) && $_POST['unit'] === 'gb' ? ' selected="selected"' : '') ?>>GB
              </option>
            </select>
            <input type="button" value="Preview" onclick="Calculate();" />
            <strong>The system deducts <?= ($RequestTax * 100) ?>% as tax</strong>
          </td>
        </tr>

        <!-- New stats -->
        <tr>
          <td class="label">New Stats</td>
          <td>
            <input type="hidden" id="amount" name="amount"
              value="<?= (!empty($Bounty) ? $Bounty : '100') ?>" />
            <input type="hidden" id="current_uploaded"
              value="<?= $LoggedUser['BytesUploaded'] ?>" />
            <input type="hidden" id="current_downloaded"
              value="<?= $LoggedUser['BytesDownloaded'] ?>" />
            <ul>
              <!-- <li>Bounty: <span id="bounty_after_tax">90.00 MB</span></li> -->
              <li>Uploaded: <span id="new_uploaded"><?= Format::get_size($LoggedUser['BytesUploaded']) ?></span></li>
              <li>Ratio: <span id="new_ratio"><?= Format::get_ratio_html($LoggedUser['BytesUploaded'], $LoggedUser['BytesDownloaded']) ?></span></li>
            </ul>
          </td>
        </tr>

        <!-- Submit -->
        <tr>
          <td colspan="2" class="center">
            <input type="submit" id="button" value="Create" disabled="disabled" />
          </td>
        </tr>
        <?php } else { ?>
        <tr>
          <td colspan="2" class="center">
            <input type="submit" id="button" value="Edit" />
          </td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>
</div>

<?php
View::show_footer();
