(function () {
  function debounce(fn, delay) {
    var timer;
    return function () {
      clearTimeout(timer);
      timer = setTimeout(fn.bind(this), delay);
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form.auto-submit');
    forms.forEach(function (form) {
      var search = form.querySelector('input[name="search"]');
      if (search) {
        var delay = parseInt(search.getAttribute('data-debounce')) || 400;
        search.addEventListener('input', debounce(function () {
          if (this.value.length >= 2 || this.value.length === 0) {
            form.submit();
          }
        }, delay));
      }

      form.querySelectorAll('select[data-auto-submit]').forEach(function (sel) {
        sel.addEventListener('change', function () { form.submit(); });
      });
    });
  });
})();
