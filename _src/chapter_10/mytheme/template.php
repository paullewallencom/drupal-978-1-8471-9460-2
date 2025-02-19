<?php
// $Id: template.php,v 1.16 2007/10/11 09:51:29 goba Exp $

/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function phptemplate_body_class($left, $right) {
  if ($left != '' && $right != '') {
    $class = 'sidebars';
  }
  else {
    if ($left != '') {
      $class = 'sidebar-left';
    }
    if ($right != '') {
      $class = 'sidebar-right';
    }
  }

  if (isset($class)) {
    print ' class="'. $class .'"';
  }
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' &raquo; ', $breadcrumb) .'</div>';
  }
}

/**
 * Allow themable wrapping of all comments.
 */
function phptemplate_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    return '<div id="comments"><h2 class="comments">'. t('Comments') .'</h2>'. $content .'</div>';
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_page(&$vars) {
  $vars['tabs2'] = menu_secondary_local_tasks();

  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }
}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function phptemplate_comment_submitted($comment) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Generates IE CSS links for LTR and RTL languages.
 */
function phptemplate_get_ie_styles() {
  global $language;

  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />';
  if (defined('LANGUAGE_RTL') && $language->direction == LANGUAGE_RTL) {
    $iecss .= '<style type="text/css" media="all">@import "'. base_path() . path_to_theme() .'/fix-ie-rtl.css";</style>';
  }

  return $iecss;
}

function phptemplate_comment_controls($form) {
  $output = '<div class="container-inline">';
  $output .=  drupal_render($form);
  $output .= '</div>';
  $output .= '<div class="description">'. t('Select your preferred way to display the comments and click "Save settings" to activate your changes.') .'</div>';
  return theme('box', t('Super-Duper Comment Controls'), $output);
}

/**
 *  implement hook_preprocess_filefield_file.
 *  This interjects itself in the theme('filefield_file')
 *  structure, creating variables available for use by
 *  fielfield_file.tpl.php.
 */
function phptemplate_preprocess_filefield_file(&$variables) {
  $file = $variables['file'];
  $path = $file['filepath'];
  $url = file_create_url($path);
  $variables['url'] = $url;
  $variables['icon'] = theme('filefield_icon', $file);
  if ($file['filemime'] == 'video/quicktime') {
    $variables['object'] = <<<OBJECT
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
  codebase="http://www.apple.com/qtactivex/qtplugin.cab"
  width="360" height="240">
  <param name="src"
    value="$url" />
  <param name="controller" value="true" />
  <param name="autoplay" value="true" />
  <!--[if !IE]>-->
  <object type="video/quicktime"
    data="$url"
    width="360" height="240">
    <param name="autoplay" value="true" />
    <param name="controller" value="true" />
  </object>
  <!--<![endif]-->
</object>
OBJECT;
    $node = node_load($file['nid']);
    if ($node->type == 'album') {
      $variables['classes'] = 'filefield-file-song';
      if (module_exists('jquery_media')) {
        jquery_media_add(array('media class' => '.filefield-file-song a', 'media height' => 20, 'media width' => 200));
      }
    }
    else {
      $variables['classes'] = 'filefield-file';
    }

    if ($node->field_cartoon_thumbnail[0]['filepath']) {
      $variables['thumbnail'] = theme('imagecache', 'cartoon-overlay', $node->field_cartoon_thumbnail[0]['filepath']);
      drupal_add_js(path_to_theme() .'/thumbnail-overlay.js', 'theme');
    }
  }
  else {
    $variables['object'] = l($file['filename'], $url);
  }
}

/**
 *  Helper function to format node reference videos.
 *  If the field is field_video, then we'll display the video
 *  or thumbnail appropriately.
 *  Otherwise, we'll return the default teaser or full display
 *  of the referenced node.
 */
function _phptemplate_nodereference_formatter_full_teaser($element, $op) {
  $output = '';
  if (!empty($element['#item']['nid']) && is_numeric($element['#item']['nid'])) {
    $node = node_load($element['#item']['nid']);
    if ($node->type != 'video') {
      return theme_nodereference_formatter_full_teaser($element);
    }
    $output .= '<div class="video">';
    $output .= content_format('field_video', $node->field_video[0], ($op == 'teaser' ? 'video_thumbnail' : 'default'), $node);
    $output .= '</div>';
  }
  return $output;
}

/**
 *  Override theme('nodereference_formatter_teaser').
 */
function phptemplate_nodereference_formatter_teaser($element) {
  return _phptemplate_nodereference_formatter_full_teaser($element, 'teaser');
}

/**
 *  Override theme('nodereference_formatter_full').
 */
function phptemplate_nodereference_formatter_full($element) {
  return _phptemplate_nodereference_formatter_full_teaser($element, 'full');
}

/**
 *  implement hook_preprocess_nodereference_formatter_default.
 *  This interjects itself in the theme('nodereference_formatter_default')
 *  structure, creating variables available for use by
 *  nodereference_formatter_default.tpl.php.
 */
function phptemplate_preprocess_nodereference_formatter_default(&$variables) {
  $variables['link'] = '';
  if (!empty($variables[0]['#item']['nid']) && is_numeric($variables[0]['#item']['nid']) && ($variables['title'] = _nodereference_titles($variables[0]['#item']['nid']))) {
    // Create the default link.
    $variables['url'] = 'node/'. $variables[0]['#item']['nid'];
    $variables['link'] = l($variables['title'], $variables['url']);
    // Load the referenced node.
    $variables['node'] = node_load($variables[0]['#item']['nid']);
    if ($variables['node']->type == 'clip') {
      // Load the file contained in the audio clip.
      $variables['file'] = $variables['node']->field_audio_clip[0];
      if (user_access('view filefield uploads') && is_file($variables['file']['filepath'])) {
        // Override the $link variable created above to link to the file.
        $path = $variables['file']['filepath'];
        $variables['url'] = file_create_url($path);
        $variables['title'] = $variables['file']['description'] ? $variables['file']['description'] : $variables['file']['filename'];
        $variables['link'] = l($variables['title'], $variables['url'], array('attributes' => array('class' => 'media-clip')));
        if (module_exists('jquery_media')) {
          // Add a smaller version of the player with jQuery Media.
          jquery_media_add(array('media class' => 'a.media-clip', 'media height' => 20, 'media width' => 200));
        }
      }
    }
  }
}
