(function () {
  "use strict";

  var modal, grid, emptyNotice, currentTarget, cachedImages;

  function resolveUrl(path) {
    if (!path) return "";
    if (/^https?:\/\//i.test(path)) return path;
    var base = window.ACMIRS_BASE_URL || "/";
    return base + path.replace(/^\//, "");
  }

  function ensureModal() {
    if (modal) return modal;
    modal = document.createElement("div");
    modal.className = "media-modal";
    modal.innerHTML =
      '<div class="media-modal-backdrop" data-media-close></div>' +
      '<div class="media-modal-panel">' +
        '<div class="media-modal-head"><h2>Choose an image</h2><button type="button" class="media-modal-close" data-media-close aria-label="Close">&times;</button></div>' +
        '<div class="media-modal-grid" data-media-grid></div>' +
        '<p class="media-modal-empty" data-media-empty style="display:none">No images uploaded yet. Visit the Media Library to upload one.</p>' +
      '</div>';
    document.body.appendChild(modal);
    grid = modal.querySelector("[data-media-grid]");
    emptyNotice = modal.querySelector("[data-media-empty]");
    modal.addEventListener("click", function (event) {
      if (event.target.closest("[data-media-close]")) closeModal();
    });
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") closeModal();
    });
    return modal;
  }

  function openModal(targetId) {
    currentTarget = document.getElementById(targetId);
    ensureModal();
    modal.classList.add("is-open");
    loadImages();
  }

  function closeModal() {
    if (modal) modal.classList.remove("is-open");
  }

  function renderImages(images) {
    grid.innerHTML = "";
    emptyNotice.style.display = images.length ? "none" : "block";
    images.forEach(function (image) {
      var button = document.createElement("button");
      button.type = "button";
      button.className = "media-modal-item";
      button.innerHTML = '<img src="' + resolveUrl(image.path) + '" alt="" loading="lazy">';
      button.addEventListener("click", function () { selectImage(image); });
      grid.appendChild(button);
    });
  }

  function loadImages() {
    if (cachedImages) {
      renderImages(cachedImages);
      return;
    }
    grid.innerHTML = '<p class="media-modal-loading">Loading…</p>';
    fetch(resolveUrl("admin/media.php?format=json"), { credentials: "same-origin" })
      .then(function (response) { return response.json(); })
      .then(function (images) {
        cachedImages = images;
        renderImages(images);
      })
      .catch(function () {
        grid.innerHTML = '<p class="media-modal-loading">Could not load images.</p>';
      });
  }

  function selectImage(image) {
    if (currentTarget) {
      currentTarget.value = image.path;
      currentTarget.dispatchEvent(new Event("input", { bubbles: true }));
    }
    closeModal();
  }

  function updatePreview(input) {
    var preview = document.querySelector('[data-preview-for="' + input.id + '"]');
    if (!preview) return;
    var value = input.value.trim();
    if (value === "") {
      preview.style.display = "none";
      preview.removeAttribute("src");
      return;
    }
    preview.src = resolveUrl(value);
    preview.style.display = "block";
  }

  document.addEventListener("click", function (event) {
    var trigger = event.target.closest("[data-media-picker]");
    if (!trigger) return;
    event.preventDefault();
    openModal(trigger.getAttribute("data-media-picker"));
  });

  document.addEventListener("input", function (event) {
    if (event.target.matches(".image-field input[type='text']")) {
      updatePreview(event.target);
    }
  });
})();
