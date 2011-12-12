var Commently;
var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
Commently = (function() {
  function Commently() {
    this.parser = new Showdown.converter();
  }
  Commently.prototype.initialize = function(form) {
    this.autocompleter = $('#commently-autocomplete');
    this.textarea = $('.commently-form textarea');
    this.checkbox_preview = $('.commently-form input[name=preview]');
    this.checkbox_markdown = $('.commently-form input[name=format]');
    this.label_preview = $('#label-preview');
    this.previewbox = $('#commently-preview');
    this.body = document.getElementById('commently-body');
    this.userIndex = 1;
    this.bg = $('#commently-backgrounder');
    this.root = this.getRoot();
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
  Commently.prototype.comment = function(id) {
    return $('#commently-comment-' + id);
  };
  Commently.prototype.log = function(err) {
    return alert(err);
  };
  Commently.prototype.getRoot = function(url) {
    return $('[data-group=commently][data-type=comments][data-url=' + url + ']');
  };
  Commently.prototype.reply = function(id) {
    var article, con, form, root;
    article = this.comment(id);
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
  Commently.prototype.keydown = function(evt) {
    var key;
    try {
      key = parseInt(evt.keyCode);
      if ((key === 37 || key === 39) && this.autocompleteCount()) {
        evt.preventDefault();
        evt.stopPropagation();
        return false;
      }
    } catch (err) {
      return console.log(err);
    }
  };
  Commently.prototype.keypress = function(evt) {
    var key;
    try {
      key = parseInt(evt.keyCode);
      console.log("key : " + key);
      if (key === 13 && this.autocompleteCount()) {
        evt.preventDefault();
        evt.stopPropagation();
        this.selectAutocomplete();
        return false;
      }
    } catch (err) {
      return console.log(err);
    }
  };
  Commently.prototype.autocompleteCount = function() {
    return $('>div', this.autocompleter).size();
  };
  Commently.prototype.autocomplete = function(evt) {
    var buffer, chr, cnt, code, idx, key, mark, sel;
    sel = this.body.selectionStart;
    key = evt.keyCode;
    buffer = [];
    mark = false;
    console.log("=========" + key);
    if (this.autocompleter.html() && (key === 37 || key === 39)) {
      cnt = this.autocompleteCount();
      if (key === 39) {
        this.userIndex += 1;
        if (this.userIndex > cnt) {
          this.userIndex = 1;
        }
      } else if (key === 37) {
        this.userIndex -= 1;
        if (this.userIndex < 1) {
          this.userIndex = cnt;
        }
      }
      this.activateAutocomplete(this.userIndex);
      evt.preventDefault();
      evt.stopPropagation();
      return false;
    }
    for (idx = sel; sel <= 0 ? idx <= 0 : idx >= 0; sel <= 0 ? idx++ : idx--) {
      chr = this.body.value.charAt(idx);
      code = this.body.value.charCodeAt(idx);
      if (code === 32 || code === 10) {
        this.clearAutocomplete();
        return;
      }
      if (chr === '@' || chr === '/') {
        mark = true;
        break;
      }
      buffer.unshift(chr);
    }
    if (mark && buffer.length) {
      return $.getJSON('/ajax/find_user?keyword=' + buffer.join(''), __bind(function(data) {
        if (data != null) {
          return this.showAutocomplete(data);
        }
      }, this));
    } else {
      return this.clearAutocomplete();
    }
  };
  Commently.prototype.selectAutocomplete = function() {
    var chr, curPos, discarded, i, marker, startPos, strAfter, strBefore, user;
    try {
      user = $('>div:nth-child(' + this.userIndex + ')', this.autocompleter);
      curPos = this.body.selectionStart;
      startPos = curPos;
      discarded = 0;
      for (i = curPos; curPos <= 0 ? i <= 0 : i >= 0; curPos <= 0 ? i++ : i--) {
        chr = this.body.value.charAt(i - 1);
        if (chr === '@') {
          marker = '@';
          break;
        } else if (chr === '/') {
          marker = '/';
          break;
        }
        startPos--;
        discarded++;
      }
      strBefore = this.body.value.substr(0, startPos);
      strAfter = this.body.value.substr(startPos + discarded);
      this.body.value = strBefore + user.data('name') + marker + strAfter;
      return this.body.focus();
    } catch (err) {

    } finally {
      this.clearAutocomplete();
    }
  };
  Commently.prototype.clearAutocomplete = function() {
    this.autocompleter.html('');
    return this.userIndex = 1;
  };
  Commently.prototype.activateAutocomplete = function(idx) {
    var user;
    $('>div', this.autocompleter).removeClass('active');
    user = $('>div:nth-child(' + idx + ')', this.autocompleter);
    return user.addClass('active');
  };
  Commently.prototype.showAutocomplete = function(data) {
    var h, idx, user, _len, _ref;
    h = [];
    _ref = data.slice(0, 5);
    for (idx = 0, _len = _ref.length; idx < _len; idx++) {
      user = _ref[idx];
      h.push('<div ' + (idx === 0 ? 'class="active"' : '') + ' data-name="' + user.name + '" data-userid="' + user.userid + '"><img src="' + user.avatar_url + '" height="30"><div><strong>' + user.name + '</strong><br>' + user.userid + '</div></div>');
    }
    return this.autocompleter.html(h.join(''));
  };
  Commently.prototype.edit = function(id) {
    var article, root;
    article = this.comment(id);
    root = this.getRoot(article.data('url'));
    $.getJSON('/ajax/commently/' + id, __bind(function(data) {
      var con, form, remaining, time;
      if (!data || !data.id || parseInt(data.can_edit_within) <= 0) {
        alert('댓글을 수정할 수 없습니다.');
        return;
      }
      $('article', root).hide();
      time = Math.floor(data.can_edit_within);
      if (time < 60) {
        remaining = time + '초';
      } else {
        remaining = Math.floor(time / 60) + '분';
      }
      form = $('[data-type=form-container]', root);
      con = $('#commently-reply-' + id);
      $('[data-type=form-wrapper]', root).append(form).prepend('<div class="commently-edit-title">댓글 수정하기 <small onclick="commently.cancelEdit(' + id + ')">[돌아가기]</small><span>수정 가능 시간이 <strong>' + remaining + '</strong> 남았습니다.</span></div>');
      $('input[name=id]', form).val(id);
      return $('textarea', form).focus().val(data.body).addClass('active');
    }, this));
    return false;
  };
  Commently.prototype.cancelEdit = function(id) {
    var article, form, root;
    article = this.comment(id);
    root = this.getRoot(article.data('url'));
    form = $('[data-type=form-container]', root);
    $('article', root).show();
    $('.commently-edit-title', root).remove();
    $('input[name=id]', form).val('');
    return $('textarea', form).val('');
  };
  Commently.prototype["delete"] = function(id) {
    var article, root;
    article = this.comment(id);
    if ($('.commently-deleting', article).size()) {
      article.removeClass('deleting');
      $('.commently-deleting', article).remove();
      return false;
    }
    root = this.getRoot(article.data('url'));
    article.addClass('deleting');
    $('[data-type=body]', article).after('<div class="commently-deleting"><a href="/commently/delete/' + id + '">댓글을 삭제하시면 클릭하세요.</a><small>삭제된 댓글은 되살릴 수 없습니다.</small></div>');
    return false;
  };
  return Commently;
})();
window.commently;
$(function() {
  return window.commently = new Commently();
});