/* ==========================================================================
   Hero opening reveal — "A" geometry mask
   Progressive enhancement over the hero: the markup and site.css already
   render a complete, ordinary hero (full-bleed video, headline, lead copy,
   dossier, CTA — all visible immediately). This file only pins the hero and
   choreographs it when GSAP + ScrollTrigger loaded, motion is welcome, the
   viewport is wide enough to pin sensibly, and the connection isn't metered.
   ========================================================================== */

(function () {
  "use strict";

  function thrifty() {
    var connection = navigator.connection;
    if (!connection) return false;
    return connection.saveData === true || /2g/.test(connection.effectiveType || "");
  }

  function init() {
    var section = document.getElementById("top");
    if (!section) return;

    var mask = section.querySelector("[data-hero-mask]");
    var maskVideo = section.querySelector("[data-hero-mask-video]");
    var heroMedia = section.querySelector("[data-hero-media]");
    var heroVideo = section.querySelector("[data-hero-video]");
    var washes = section.querySelectorAll("[data-hero-wash]");
    var eyebrow = section.querySelector("[data-hero-eyebrow]");
    var words = section.querySelectorAll("[data-hero-headline] .word");
    var leadGroup = section.querySelectorAll("[data-hero-lead]");
    var dossier = section.querySelector("[data-hero-dossier]");
    var dossierRows = dossier ? dossier.querySelectorAll(".dossier-row") : [];
    var actions = section.querySelector("[data-hero-actions]");

    var reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var narrow = window.innerWidth < 900;
    var canEnhance = !!(window.gsap && window.ScrollTrigger) && !reduced && !narrow && !thrifty();
    if (!canEnhance) return; // the resting hero (site.css) already reads as complete

    gsap.registerPlugin(ScrollTrigger);

    mask.style.display = "block";
    var playing = maskVideo.play();
    if (playing && playing.catch) playing.catch(function () {});

    gsap.set(mask, { scale: 0.2, transformOrigin: "50% 50%" });
    gsap.set(heroMedia, { autoAlpha: 0 });
    gsap.set(washes, { autoAlpha: 0 });
    gsap.set(eyebrow, { autoAlpha: 0, y: 14 });
    gsap.set(words, { yPercent: 110 });
    gsap.set(leadGroup, { autoAlpha: 0, y: 16 });
    if (dossier) gsap.set(dossier, { autoAlpha: 0, x: 40 });
    gsap.set(dossierRows, { y: 14 });
    gsap.set(actions, { autoAlpha: 0, y: 16 });

    var timeline = gsap.timeline({
      scrollTrigger: {
        trigger: section,
        start: "top top",
        end: "+=300%",
        scrub: 0.5,
        pin: true,
        anticipatePin: 1
      }
    });

    timeline
      // Phase 1 (0–1.1): the A-shaped mask scales open around the looping clip.
      .to(mask, { scale: 1, duration: 1.1, ease: "power2.out" }, 0)
      // Phase 2 (1.3–2.6): a slow, linear dissolve into the same footage
      // full-bleed — long enough to read as one continuous shot rather than
      // a cut. Syncing currentTime right before the dissolve starts means the
      // two <video> elements are never more than a frame or two apart, so the
      // swap doesn't visibly jump.
      .call(function () {
        try { heroVideo.currentTime = maskVideo.currentTime; } catch (e) {}
      }, null, 1.28)
      .to(mask, { autoAlpha: 0, duration: 1.3, ease: "none" }, 1.3)
      .to(heroMedia, { autoAlpha: 1, duration: 1.3, ease: "none" }, 1.3)
      .to(washes, { autoAlpha: 1, duration: 1.1, ease: "power1.inOut" }, 1.5)
      .call(function () { maskVideo.pause(); }, null, 2.6)
      // Phase 3 (2.75–3.35): eyebrow, then the headline words rise in.
      .to(eyebrow, { autoAlpha: 1, y: 0, duration: 0.35, ease: "power2.out" }, 2.75)
      .to(words, { yPercent: 0, duration: 0.5, stagger: 0.07, ease: "power3.out" }, 2.85)
      // Phase 4 (3.5–3.95): the lead paragraph and "Learn more" link settle in.
      .to(leadGroup, { autoAlpha: 1, y: 0, duration: 0.45, stagger: 0.06, ease: "power1.out" }, 3.5)
      // Phase 5 (4.05–4.6): the Advisory Dossier slides in from the right.
      .to(dossier, { autoAlpha: 1, x: 0, duration: 0.55, ease: "power2.out" }, 4.05)
      .to(dossierRows, { y: 0, duration: 0.4, stagger: 0.05, ease: "power2.out" }, 4.15)
      // Phase 6 (4.75–5.2): the CTA buttons land last, then the hero unpins.
      .to(actions, { autoAlpha: 1, y: 0, duration: 0.45, ease: "power1.out" }, 4.75);

    window.addEventListener("load", function () { ScrollTrigger.refresh(); });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
