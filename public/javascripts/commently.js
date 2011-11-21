var Commently;
var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
Commently = (function() {
  function Commently() {
    this.apiURL = 'http://miniwini.dev/commently';
    this.initialize();
  }
  Commently.prototype.log = function(err) {
    return alert(err);
  };
  Commently.prototype.setURL = function(url) {
    return this.apiURL = url;
  };
  Commently.prototype.initialize = function() {
    return $.each(jQuery('[data-group=commently][data-type=comments]'), __bind(function(idx, container) {
      container = $(container);
      return this.comments(container.data('url'), container);
    }, this));
  };
  Commently.prototype.getRoot = function(url) {
    return $('[data-group=commently][data-type=comments][data-url=' + url + ']');
  };
  Commently.prototype.comments = function(url, target) {
    if (typeof target === 'string') {
      target = $(target);
    }
    return $.ajax({
      url: this.apiURL,
      type: 'GET',
      data: 'url=' + encodeURIComponent(url),
      beforeSend: function(xhr, setting) {},
      success: __bind(function(result, textStatus, xhr) {
        var comments;
        comments = JSON.parse(result);
        this.draw(comments.data, target);
        return this.drawForm(comments.form, target);
      }, this),
      error: function(xhr, textStatus, error) {
        return alert('error : ' + error);
      },
      complete: function(xhr, status) {}
    });
  };
  Commently.prototype.draw = function(comments, target) {
    var c, child, depth, parent, parent_depth, root, _i, _len;
    try {
      root = $('<div>');
      for (_i = 0, _len = comments.length; _i < _len; _i++) {
        c = comments[_i];
        if (c.parent_id) {
          parent = jQuery('#commently-comment-' + c.parent_id, root);
          parent_depth = parent.attr('class');
          depth = parent_depth != null ? parseInt(parent_depth.replace(/^depth-/, '')) + 1 : 1;
          child = $(c.html).addClass('depth-' + depth);
          parent.after(child);
        } else {
          root.append(c.html);
        }
      }
      return target.append($('> article', root));
    } catch (err) {
      return this.log(err);
    }
  };
  Commently.prototype.drawForm = function(form, target) {
    var f;
    target.append(form);
    f = $('form', target);
    return f.bind('submit', __bind(function(evt) {
      f = $(evt.target);
      evt.stopPropagation();
      evt.preventDefault();
      $.ajax({
        url: f.attr('action'),
        type: 'POST',
        data: f.serialize(),
        beforeSend: __bind(function(xhr, setting) {
          return this.makeBusy(f, true);
        }, this),
        success: function(result, textStatus, xhr) {
          var url;
          url = $(result).data('url');
          return target.append(result);
        },
        error: function(xhr, textStatus, error) {
          this.makeBusy(f, false);
          return alert('error');
        },
        complete: __bind(function(xhr, status) {
          this.makeBusy(f, false);
          return this.reset(f);
        }, this)
      });
      return false;
    }, this));
  };
  Commently.prototype.reply = function(id) {
    var article, form, root;
    article = jQuery('#commently-comment-' + id);
    root = this.getRoot(article.data('url'));
    form = jQuery('[data-type=form-container]', root);
    $('#commently-reply-' + id).append(form);
    $('textarea', form).trigger('focus');
    return $('input[name=parent_id]', form).val(id);
  };
  Commently.prototype.makeBusy = function(f, busy) {
    if (busy) {
      $('input[type=submit]', f).val('wait...').attr('disabled', true);
      return $('textarea', f).attr('disabled', true);
    } else {
      $('input[type=submit]', f).val('submit').attr('disabled', false);
      return $('textarea', f).attr('disabled', false);
    }
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