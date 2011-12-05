var Commently;
var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
Commently = (function() {
  function Commently() {
    this.parser = new Showdown.converter();
  }
  Commently.prototype.initialize = function(form) {
    this.textarea = $('.commently-form textarea');
    this.checkbox_preview = $('.commently-form input[name=preview]');
    this.checkbox_markdown = $('.commently-form input[name=format]');
    this.label_preview = $('#label-preview');
    this.previewbox = $('#commently-preview');
    if (this.isMarkdown()) {
      this.label_preview.show();
    }
    this.textarea.bind('click', function() {
      $(this).addClass('active');
      return $(this).unbind('click');
    });
    this.checkbox_preview.change(__bind(function() {
      this.textarea.unbind('keyup');
      if (!this.isMarkdown()) {
        return;
      }
      if (this.previewEnabled()) {
        this.previewbox.show();
        this.preview(this.textarea.val());
        this.textarea.bind('keyup', __bind(function() {
          return this.preview(this.textarea.val());
        }, this));
      } else {
        this.previewbox.hide();
      }
      this.textarea.addClass('active');
      return this.textarea.focus();
    }, this));
    return this.checkbox_markdown.change(__bind(function() {
      this.previewbox[this.isMarkdown() && this.previewEnabled() ? "show" : "hide"]();
      this.label_preview[this.isMarkdown() ? "show" : "hide"]();
      this.textarea.addClass('active');
      return this.textarea.focus();
    }, this));
  };
  Commently.prototype.isMarkdown = function() {
    return typeof this.checkbox_markdown.attr('checked') !== 'undefined';
  };
  Commently.prototype.previewEnabled = function() {
    return typeof this.checkbox_preview.attr('checked') !== 'undefined';
  };
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
  Commently.prototype.preview = function(src) {
    return $('#commently-preview').html(this.parser.makeHtml(src));
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