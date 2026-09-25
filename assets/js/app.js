(function () {
  "use strict";

  function serviceData() {
    var node = document.getElementById("service-data");
    if (!node) return [];
    try { return JSON.parse(node.textContent || "[]"); } catch (error) { return []; }
  }

  function initFinder() {
    var root = document.querySelector("[data-finder]");
    var mandates = serviceData();
    if (!root || !mandates.length) return;

    var tabList = root.querySelector(".finder-tabs");
    var titleEl = root.querySelector("[data-finder-title]");
    var blurbEl = root.querySelector("[data-finder-blurb]");
    var itemsEl = root.querySelector("[data-finder-items]");
    var cardsEl = root.querySelector("[data-finder-cards]");
    var sectorsEl = root.querySelector("[data-finder-sectors]");
    var body = root.querySelector(".finder-body");
    body.id = "finder-panel";
    body.setAttribute("role", "tabpanel");

    var buttons = mandates.map(function (mandate, index) {
      var button = document.createElement("button");
      button.type = "button";
      button.className = "finder-tab";
      button.setAttribute("role", "tab");
      button.id = "service-" + mandate.key;
      button.setAttribute("aria-controls", "finder-panel");
      button.textContent = mandate.label;
      button.addEventListener("click", function () { select(index); });
      button.addEventListener("keydown", function (event) {
        var next = null;
        if (event.key === "ArrowRight") next = (index + 1) % mandates.length;
        if (event.key === "ArrowLeft") next = (index - 1 + mandates.length) % mandates.length;
        if (next === null) return;
        event.preventDefault();
        select(next);
        buttons[next].focus();
      });
      tabList.appendChild(button);
      return button;
    });

    function select(index) {
      var mandate = mandates[index];
      if (!mandate) return;
      buttons.forEach(function (button, buttonIndex) {
        var active = buttonIndex === index;
        button.setAttribute("aria-selected", active ? "true" : "false");
        button.tabIndex = active ? 0 : -1;
      });
      titleEl.textContent = mandate.title;
      blurbEl.textContent = mandate.blurb;
      itemsEl.innerHTML = "";
      mandate.items.forEach(function (item) {
        var row = document.createElement("div");
        row.className = "finder-item";
        row.textContent = item;
        itemsEl.appendChild(row);
      });
      cardsEl.innerHTML = "";
      mandate.cards.forEach(function (card) {
        var wrap = document.createElement("div");
        wrap.className = "finder-card";
        var mark = document.createElement("span");
        mark.className = "finder-card-mark";
        mark.style.background = card.fill;
        var heading = document.createElement("h4");
        heading.textContent = card.title;
        var copy = document.createElement("p");
        copy.textContent = card.body;
        wrap.appendChild(mark); wrap.appendChild(heading); wrap.appendChild(copy);
        cardsEl.appendChild(wrap);
      });
      sectorsEl.innerHTML = "";
      mandate.sectors.forEach(function (sector) {
        var chip = document.createElement("span");
        chip.className = "chip";
        chip.textContent = sector;
        sectorsEl.appendChild(chip);
      });
    }

    document.querySelectorAll("[data-service-key]").forEach(function (link) {
      link.addEventListener("click", function () {
        var key = link.getAttribute("data-service-key");
        var index = mandates.findIndex(function (item) { return item.key === key; });
        if (index >= 0) select(index);
      });
    });
    select(0);
  }

  function initCounters() {
    var nodes = Array.prototype.slice.call(document.querySelectorAll("[data-count-to]"));
    var reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!nodes.length || reduced || !("IntersectionObserver" in window)) return;
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var node = entry.target;
        var target = parseInt(node.getAttribute("data-count-to"), 10) || 0;
        var suffix = node.getAttribute("data-suffix") || "";
        var start = performance.now();
        function tick(now) {
          var progress = Math.min(1, (now - start) / 1400);
          node.textContent = Math.round(target * (1 - Math.pow(1 - progress, 3))) + suffix;
          if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
        observer.unobserve(node);
      });
    }, { threshold: 0.6 });
    nodes.forEach(function (node) { observer.observe(node); });
  }

  function initNav() {
    var toggle = document.querySelector("[data-nav-toggle]");
    var nav = document.querySelector("[data-primary-nav]");
    if (!toggle || !nav) return;
    function close() { nav.classList.remove("is-open"); toggle.setAttribute("aria-expanded", "false"); }
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    nav.addEventListener("click", function (event) { if (event.target.closest("a")) close(); });
    document.addEventListener("keydown", function (event) { if (event.key === "Escape") close(); });
    window.addEventListener("resize", function () { if (window.innerWidth > 980) close(); });
  }

  function initSmoothScroll() {
    if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    document.addEventListener("click", function (event) {
      var link = event.target.closest('a[href^="#"]');
      if (!link) return;
      var id = link.getAttribute("href");
      if (!id || id === "#") return;
      var target = document.querySelector(id);
      if (!target) return;
      event.preventDefault();
      target.scrollIntoView({ behavior: "smooth", block: "start" });
      history.replaceState(null, "", id);
    });
  }

  function initHeroVideo() {
    var video = document.querySelector("[data-hero-video]");
    if (!video) return;
    var reduced = window.matchMedia ? window.matchMedia("(prefers-reduced-motion: reduce)") : null;
    function play() { var result = video.play(); if (result && result.catch) result.catch(function () {}); }
    function thrifty() { var connection = navigator.connection; return connection && (connection.saveData === true || /2g/.test(connection.effectiveType || "")); }
    var hd = video.getAttribute("data-src-hd");
    if (hd && window.innerWidth >= 1200 && !thrifty() && !(reduced && reduced.matches)) {
      video.src = hd; video.load(); video.addEventListener("canplay", play, { once: true }); play();
    }
    if (!reduced) return;
    function sync() { if (reduced.matches) video.pause(); else play(); }
    sync();
    if (reduced.addEventListener) reduced.addEventListener("change", sync);
  }

  function init() { initFinder(); initCounters(); initNav(); initSmoothScroll(); initHeroVideo(); }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();

