var Miniwini;
Miniwini = (function() {
  function Miniwini() {}
  Miniwini.prototype.constuctor = function() {};
  Miniwini.prototype.saveToDraft = function(f) {
    f.elements['state'].value = 'draft';
    return f.submit();
  };
  return Miniwini;
})();
window.miniwini;
$(function() {
  return window.miniwini = new Miniwini();
});