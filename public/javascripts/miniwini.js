var Miniwini;
var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
Miniwini = (function() {
  function Miniwini() {
    this.notificationCheckInterval = 5000;
    this.checkNotification();
  }
  Miniwini.prototype.checkNotification = function() {
    var $elem;
    try {
      $elem = $('#notifications-count');
      return $.getJSON('/notification/count', __bind(function(data) {
        if (data && data.count > 0) {
          document.title = '(' + data.count + ') ' + document.title.replace(/^\(\d+\) /, '');
          $elem.data('time', data.last_updated_at.toString()).html(data.count).show();
        } else {
          document.title = document.title.replace(/^\(\d+\)$ /, '');
          $elem.hide();
        }
        return window.setTimeout(__bind(function() {
          return this.checkNotification();
        }, this), this.notificationCheckInterval);
      }, this));
    } catch (err) {

    }
  };
  Miniwini.prototype.notifications = function() {
    var $elem;
    try {
      $elem = $('#notifications');
      $(document).unbind('click');
      $(document).bind('click', __bind(function(evt) {
        if ($elem.css('display') !== 'none' && evt.target.id !== 'notifications-count') {
          return $elem.hide();
        }
      }, this));
      if ($('#notifications-count').data('time') === $elem.data('time')) {
        return window.setTimeout(__bind(function() {
          return $elem.toggle();
        }, this), 10);
      } else {
        return $.getJSON('/notification/all', __bind(function(data) {
          var html;
          html = [];
          $.each(data, __bind(function(idx, noti) {
            var h, time;
            switch (noti.action) {
              case "comment_on_topic":
                time = $.timeago(new Date(noti.created_at * 1000));
                h = "<div data-url=\"" + noti.url + "\" data-time=\"" + noti.created_at + "\"><figure data-type=\"avatar-medium\"><img src=\"" + noti.actor_avatar + "\" alt=\"" + noti.actor_name + "\"></figure><p>" + noti.actor_name + "님이 당신의 게시물에 댓글을 남겼습니다. <q>" + noti.body + "</q><time>" + time + "</time></p></div>";
                break;
              case "comment_on_comment":
                time = $.timeago(new Date(noti.created_at * 1000));
                h = "<div data-url=\"" + noti.url + "\" data-time=\"" + noti.created_at + "\"><figure data-type=\"avatar-medium\"><img src=\"" + noti.actor_avatar + "\" alt=\"" + noti.actor_name + "\"></figure><p>" + noti.actor_name + "님이 당신의 댓글에 댓글을 남겼습니다. <q>" + noti.body + "</q><time>" + time + "</time></p></div>";
            }
            if (idx === 0) {
              $('#notifications').data('time', noti.created_at.toString());
            }
            return html.push(h);
          }, this));
          $elem.html(html.join('')).toggle();
          return $('#notifications  div[data-url]').click(function() {
            var time, url;
            time = $(this).data('time');
            url = $(this).data('url');
            return $.ajax({
              url: '/notification/read?time=' + time,
              success: function(res) {
                return document.location.href = url;
              }
            });
          });
        }, this));
      }
    } catch (err) {

    }
  };
  Miniwini.prototype.submitPost = function(f) {
    $('#submitButton').attr('disabled', true);
    return true;
  };
  Miniwini.prototype.saveToDraft = function(f) {
    f.elements['state'].value = 'draft';
    return f.submit();
  };
  Miniwini.prototype.setPostType = function(tab) {
    var body, parser, type;
    type = $(tab).data('post-type');
    $('[data-group=post-types] li[data-post-type]').removeClass('active');
    $('[data-group=post-types] li[data-post-type=' + type + ']').addClass('active');
    $('[data-group=post-types] [id^=type-]').hide();
    $('[data-group=post-types] [id=type-' + type + ']').show();
    if (type === 'preview') {
      $('#preview-body').html('');
      if (($('#format').val()) === 'markdown') {
        body = $('#body').val();
        parser = new Showdown.converter();
        $('#preview-body').html(parser.makeHtml(body));
      } else {
        $.ajax({
          url: '/ajax/preview',
          type: 'POST',
          data: {
            body: $('#body').val()
          },
          success: __bind(function(html) {
            return $('#preview-body').html(html);
          }, this)
        });
      }
    }
    $('#preview-section')[type === 'preview' ? 'show' : 'hide']();
    return $('#common-controls')[type === 'preview' ? 'hide' : 'show']();
  };
  return Miniwini;
})();
window.miniwini;
$(function() {
  return window.miniwini = new Miniwini();
});