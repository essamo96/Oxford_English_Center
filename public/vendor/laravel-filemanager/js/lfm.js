(function( $ ){

  $.fn.filemanager = function(type, options) {
    type = type || 'file';

    this.on('click', function(e) {
      var route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
      var target_input = $(this).data('input');
      var target_preview = $(this).data('preview');
      localStorage.setItem('target_input', target_input);
      localStorage.setItem('target_preview', target_preview);

      // The custom file manager (admin/file_manager) reads the picked file's
      // target back via a `target=<inputId>` query param and calls
      // window.opener.metronicFilePickerCallback(url, targetId) — it never
      // calls the legacy SetUrl() below. Without this param the popup has no
      // way to know which input/preview to fill, so nothing happened on pick.
      window.open(route_prefix + '?type=' + type + '&target=' + encodeURIComponent(target_input), 'FileManager', 'width=1000,height=700');

      // Kept for backward compatibility with any picker still using the old contract.
      window.SetUrl = function (url, file_path) {
          //set the value of the desired input to image url
          var target_input = $('#' + localStorage.getItem('target_input'));
          target_input.val(file_path).trigger('change');

          //set or change the preview image src
          var target_preview = $('#' + localStorage.getItem('target_preview'));
          target_preview.attr('src', url).trigger('change');
      };
      return false;
    });
  }

})(jQuery);
