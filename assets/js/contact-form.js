/* Progressive enhancement over the contact form: the markup and CSS already
   render a complete, ordinary form (every field visible, one submit button
   at the end). This file only turns it into a stepped wizard when JS runs -
   without it, the form still works exactly as a single page. */
(function () {
  "use strict";

  function init() {
    var form = document.querySelector("[data-inquiry-form]");
    if (!form) return;
    var steps = Array.prototype.slice.call(form.querySelectorAll("[data-step]"));
    var submitButton = form.querySelector("[type=submit]");
    if (steps.length < 2 || !submitButton) return;

    var current = 0;

    var progress = document.createElement("div");
    progress.className = "form-progress";
    steps.forEach(function (step, index) {
      var item = document.createElement("span");
      item.className = "form-progress-step";
      item.innerHTML = "<em>" + (index + 1) + "</em>" + (step.getAttribute("data-step-label") || "Step " + (index + 1));
      progress.appendChild(item);
    });
    form.insertBefore(progress, steps[0]);

    var nav = document.createElement("div");
    nav.className = "form-nav";
    var backButton = document.createElement("button");
    backButton.type = "button";
    backButton.className = "btn btn-secondary";
    backButton.textContent = "Back";
    var nextButton = document.createElement("button");
    nextButton.type = "button";
    nextButton.className = "btn btn--copper";
    nextButton.textContent = "Continue";
    nav.appendChild(backButton);
    nav.appendChild(nextButton);
    submitButton.parentNode.insertBefore(nav, submitButton);

    function render() {
      steps.forEach(function (step, index) { step.style.display = index === current ? "" : "none"; });
      Array.prototype.forEach.call(progress.children, function (item, index) {
        item.classList.toggle("is-active", index === current);
        item.classList.toggle("is-done", index < current);
      });
      backButton.style.display = current === 0 ? "none" : "";
      nextButton.style.display = current === steps.length - 1 ? "none" : "";
      submitButton.style.display = current === steps.length - 1 ? "" : "none";
    }

    function firstInvalidField(step) {
      var fields = step.querySelectorAll("[required]");
      for (var i = 0; i < fields.length; i++) {
        var field = fields[i];
        if (field.type === "radio") {
          var group = step.querySelectorAll('input[name="' + field.name + '"]');
          var checked = Array.prototype.some.call(group, function (radio) { return radio.checked; });
          if (!checked) return field;
        } else if (!field.value.trim()) {
          return field;
        }
      }
      return null;
    }

    nextButton.addEventListener("click", function () {
      var invalid = firstInvalidField(steps[current]);
      if (invalid) {
        if (invalid.reportValidity) invalid.reportValidity();
        return;
      }
      if (current < steps.length - 1) {
        current++;
        render();
        form.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });

    backButton.addEventListener("click", function () {
      if (current > 0) {
        current--;
        render();
      }
    });

    render();
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
