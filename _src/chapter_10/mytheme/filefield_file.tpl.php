<?php
/**
 *  filefield_file.tpl.php
 *  This will display the file object or link for all filefields.
 *  The following variables are available:
 *    $id: The unique count of this filefield.
 *    $zebra: 'even' or 'odd'.
 *    $file: The original file object.
 *    $url: The URL to the file itself.
 *    $icon: A representative icon based on the file's mime type.
 *    $object: The HTML to display our file, as an object or link.
 *    $thumbnail: An image to click to load our file.
 */
?>
<div class="filefield-wrapper">
  <?php if ($thumbnail) : ?>
    <?php
      // Make the embedded object invisible until clicked.
      $style = 'style="display: none;"';
    ?>
    <div class="filefield-thumbnail">
      <?php print l($thumbnail, $url, array('html' => TRUE, 'attributes' => array('id' => "filefield-file-thumbnail-$id", 'class' => 'filefield-file-thumbnail', 'rel' => "filefield-file-file-$id"))); ?>
    </div>
  <?php endif; ?>

  <div id="filefield-file-file-<?php print $id; ?>" class="<?php print $classes; ?> clear-block" <?php print $style; ?> >
    <?php print $object; ?>
  </div>
</div>

