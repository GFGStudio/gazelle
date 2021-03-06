<?php
#declare(strict_types=1);

class SiteHistoryView
{
    public static function render_linkbox()
    {
        if (check_perms('users_mod')
      ) {
            ?>
<div class="linkbox">
  <a href="sitehistory.php?action=edit" class="brackets">Create new event</a>
</div>
<?php
        }
    }

    public static function render_events($Events)
    {
        $Categories = SiteHistory::get_categories();
        $SubCategories = SiteHistory::get_sub_categories();
        $CanEdit = check_perms('users_mod') ;
        foreach ($Events as $Event) {
            ?>
<div class="box">
  <div class="head colhead_dark">
    <div class="title">
      <?php if ($CanEdit) { ?>
      <a class="brackets"
        href="sitehistory.php?action=edit&amp;id=<?=$Event['ID']?>">Edit</a>
      <?php } ?>

      <?=date('F d, Y', strtotime($Event['Date'])); ?>
      -
      <a href="sitehistory.php?action=search&amp;category=<?=$Event['Category']?>"
        class="brackets"><?=$Categories[$Event['Category']]?></a>
      <a href="sitehistory.php?action=search&amp;subcategory=<?=$Event['SubCategory']?>"
        class="brackets"><?=$SubCategories[$Event['SubCategory']]?></a>

      <?php if (!empty($Event['Url'])) { ?>
      <a href="<?=$Event['Url']?>"><?=$Event['Title']?></a>
      <?php } else { ?>
      <?=$Event['Title']?>
      <?php } ?>
    </div>
    <div class="tags">
      <?php self::render_tags($Event['Tags'])?>
    </div>
  </div>
  <?php if (!empty($Event['Body'])) { ?>
  <div class="body">
    <?=Text::full_format($Event['Body'])?>
  </div>
  <?php } ?>
</div>
<?php
        }
    }

    private static function render_tags($Tags)
    {
        $Tags = explode(',', $Tags);
        natcasesort($Tags);
        $FormattedTags = '';
        foreach ($Tags as $Tag) {
            $FormattedTags .= "<a href=\"sitehistory.php?action=search&amp;tags=$Tag\">$Tag" . "</a>, ";
        }
        echo rtrim($FormattedTags, ', ');
    }

    public static function render_search() { ?>
<div class="box">
  <div class="head">Search</div>
  <div class="pad">
    <form class="search_form" action="sitehistory.php" method="get">
      <input type="hidden" name="action" value="search" />
      <input type="text" id="title" name="title" size="20" placeholder="Title" />
      <br /><br />
      <input type="text" id="tags" name="tags" size="20" placeholder="Comma-separated tags" />
      <br /><br />
      <select name="category" id="category">
        <option value="0">Choose a category</option>
        <?php
      $Categories = SiteHistory::get_categories();
      foreach ($Categories as $Key => $Value) {
          ?>
        <option<?=$Key == $Event['Category'] ? ' selected="selected"' : ''?>
          value="<?=$Key?>"><?=$Value?>
          </option>
          <?php
      } ?>
      </select>
      <br /><br />
      <select name="subcategory">
        <option value="0">Choose a subcategory</option>
        <?php
      $SubCategories = SiteHistory::get_sub_categories();
      foreach ($SubCategories as $Key => $Value) {
          ?>
        <option<?=$Key == $Event['SubCategory'] ? ' selected="selected"' : ''?>
          value="<?=$Key?>"><?=$Value?>
          </option>
          <?php
      } ?>
      </select>
      <br /><br />
      <input value="Search" type="submit" />
    </form>
  </div>
</div>
<?php }

    public static function render_edit_form($Event) { ?>
<form id="event_form" method="post" action="">
  <?php if ($Event) { ?>
  <input type="hidden" name="action" value="take_edit" />
  <input type="hidden" name="id"
    value="<?=$Event['ID']?>" />
  <?php } else { ?>
  <input type="hidden" name="action" value="take_create" />
  <?php } ?>
  <input type="hidden" name="auth"
    value="<?=G::$LoggedUser['AuthKey']?>" />
  <table cellpadding="6" cellspacing="1" border="0" class="layout border" width="100%">
    <tr>
      <td class="label">Title:</td>
      <td>
        <input type="text" id="title" name="title" size="50" class="required"
          value="<?=$Event['Title']?>" />
      </td>
    </tr>
    <tr>
      <td class="label">Link:</td>
      <td>
        <input type="text" id="url" name="url" size="50"
          value="<?=$Event['Url']?>" />
      </td>
    </tr>
    <tr>
      <td class="label">Date:</td>
      <td>
        <input type="date" id="date" name="date" class="required" <?=$Event ? ' value="' . date('Y-m-d', strtotime($Event['Date'])) . '"' : ''?>
        />
      </td>
    </tr>
    <tr>
      <td class="label">Category:</td>
      <td>
        <select id="category" name="category" class="required">
          <option value="0">Choose a category</option>
          <?php
    $Categories = SiteHistory::get_categories();
    foreach ($Categories as $Key => $Value) {
        ?>
          <option<?=$Key == $Event['Category'] ? ' selected="selected"' : ''?>
            value="<?=$Key?>"><?=$Value?>
            </option>
            <?php
    } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">Subcategory:</td>
      <td>
        <select id="category" name="sub_category" class="required">
          <option value="0">Choose a subcategory</option>
          <?php $SubCategories = SiteHistory::get_sub_categories();
    foreach ($SubCategories as $Key => $Value) { ?>
          <option<?=$Key == $Event['SubCategory'] ? ' selected="selected"' : ''?>
            value="<?=$Key?>"><?=$Value?>
            </option>
            <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">Tags:</td>
      <td>
        <input type="text" id="tags" name="tags" placeholder="Comma-separated tags; use periods/dots for spaces"
          size="50"
          value="<?=$Event['Tags']?>" />
        <select id="tag_list">
          <option>Choose tags</option>
          <?php
    $Tags = SiteHistory::get_tags();
    foreach ($Tags as $Tag) {
        ?>
          <option><?=$Tag?>
          </option>
          <?php
    } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">Body:</td>
      <td>
        <textarea id="body" name="body" cols="90" rows="8" tabindex="1"
          onkeyup="resize('body');"><?=$Event['Body']?></textarea>
      </td>
    </tr>
  </table>
  <input type="submit" name="submit" value="Submit" />
  <?php if ($Event) { ?>
  <input type="submit" name="delete" value="Delete" />
  <?php } ?>
</form>
<?php
  }

    public static function render_recent_sidebar($Events) { ?>
<div class="box">
  <div class="head colhead_dark">
    <strong><a href="sitehistory.php">Latest site history</a></strong>
  </div>
  <ul class="stats nobullet">
    <?php
    $Categories = SiteHistory::get_categories();
    foreach ($Events as $Event) {
        ?>
    <li>
      <a href="sitehistory.php?action=search&amp;category=<?=$Event['Category']?>"
        class="brackets"><?=$Categories[$Event['Category']]?></a>
      <?php if (!empty($Event['Url'])) { ?>
      <a href="<?=$Event['Url']?>"><?=Format::cut_string($Event['Title'], 20)?></a>
      <?php } else { ?>
      <?=Format::cut_string($Event['Title'], 20)?>
      <?php } ?>
    </li>
    <?php
    } ?>
  </ul>
</div>
<?php
  }
}
