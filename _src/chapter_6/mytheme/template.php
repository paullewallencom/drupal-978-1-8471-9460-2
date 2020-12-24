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
    if ($node->field_cartoon_thumbnail[0]['filepath']) {
      $variables['thumbnail'] = theme('imagecache', 'cartoon-overlay', $node->field_cartoon_thumbnail[0]['filepath']);
      drupal_add_js(path_to_theme() .'/thumbnail-overlay.js', 'theme');
    }
  }
  else {
    $variables['object'] = l($file['filename'], $url);
  }
}
