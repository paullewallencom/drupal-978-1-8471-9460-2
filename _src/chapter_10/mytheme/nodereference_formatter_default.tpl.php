<?php
/**
 *  nodereference_formatter_default.tpl.php
 *  This will display either a link to the referenced node, or to its file if the node is a clip.
 *  The following variables are available:
 *    $id: The unique count of this filefield.
 *    $zebra: 'even' or 'odd'.
 *    $link: The link to display.
 *    $title: The title of the link.
 *    $url: The URL path of the link.
 *    $node: The referenced node.
 *    $file: If the referenced node is a clip, then this is the audio file it contains.
 */
?>
<?php print $link; ?>

