var Commently;
var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
Commently = (function() {
  function Commently() {}
  Commently.prototype.init = function() {
    return $('.commently-form').bind('submit', __bind(function(evt) {
      var f;
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
          return $('div[data-page-url="' + url + '"]').append(result);
        },
        error: function(xhr, textStatus, error) {},
        complete: __bind(function(xhr, status) {
          return this.makeBusy(f, false);
        }, this)
      });
      return false;
    }, this));
  };
  Commently.prototype.makeBusy = function(f, busy) {
    if (busy) {
      return $('input[type=submit]', f).val('wait...').attr('disabled', true);
    } else {
      return $('input[type=submit]', f).val('submit').attr('disabled', false);
    }
  };
  return Commently;
})();
$(function() {
  var commently;
  commently = new Commently();
  return commently.init();
});