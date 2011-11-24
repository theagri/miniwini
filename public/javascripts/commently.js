var Commently;
Commently = (function() {
  function Commently() {}
  Commently.prototype.log = function(err) {
    return alert(err);
  };
  Commently.prototype.getRoot = function(url) {
    return $('[data-group=commently][data-type=comments][data-url=' + url + ']');
  };
  Commently.prototype.reply = function(id) {
    var article, form, root;
    article = $('#commently-comment-' + id);
    root = this.getRoot(article.data('url'));
    form = jQuery('[data-type=form-container]', root);
    $('#commently-reply-' + id).append(form);
    $('textarea', form).addClass('active').focus();
    return $('input[name=parent_id]', form).val(id);
  };
  Commently.prototype.reset = function(f) {
    return $('textarea', f).val('');
  };
  return Commently;
})();
window.commently;
$(function() {
  return window.commently = new Commently();
});