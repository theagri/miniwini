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
    var article, con, form, root;
    article = $('#commently-comment-' + id);
    root = this.getRoot(article.data('url'));
    form = $('[data-type=form-container]', root);
    con = $('#commently-reply-' + id);
    if (con.html()) {
      $('[data-type=form-wrapper]', root).append(form);
      article.removeClass('replying');
      $('input[name=parent_id]', form).val('');
    } else {
      con.append(form);
      article.addClass('replying');
      $('input[name=parent_id]', form).val(id);
    }
    return $('textarea', form).addClass('active').focus();
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