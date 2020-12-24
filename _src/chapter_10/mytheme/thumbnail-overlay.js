if (Drupal.jsEnabled) {
  $(document).ready(function () {
    $('a.filefield-file-thumbnail').click(function () {
      var video = '#'+ $(this).attr('rel');
      $(this).hide();
      $(video).show();
      return false;
    });
  });
}

