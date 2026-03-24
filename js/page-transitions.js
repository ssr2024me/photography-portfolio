/* =============================================
   PHOTOGRAPHY-LIFE4ME — Page Transitions & Nav
   page-transitions.js
   Include in ALL pages (index, portfolio, about, contact)
   ============================================= */

(function () {

  /* ─── HAMBURGER MENU ─── */
  document.addEventListener('DOMContentLoaded', function () {

    // Inject hamburger button if not present
    const nav = document.querySelector('nav');
    if (nav && !nav.querySelector('.hamburger')) {
      const ham = document.createElement('button');
      ham.className = 'hamburger';
      ham.setAttribute('aria-label', 'Menu');
      ham.innerHTML = '<span></span><span></span><span></span>';
      nav.appendChild(ham);
    }

    const ham  = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (ham && navLinks) {
      ham.addEventListener('click', function () {
        ham.classList.toggle('open');
        navLinks.classList.toggle('open');
        document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
      });
      // Close when any link is clicked
      navLinks.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
          ham.classList.remove('open');
          navLinks.classList.remove('open');
          document.body.style.overflow = '';
        });
      });
    }

    /* ─── PAGE TRANSITION OVERLAY ─── */
    // Inject overlay div if not present
    if (!document.querySelector('.page-transition')) {
      const div = document.createElement('div');
      div.className = 'page-transition';
      document.body.prepend(div);
    }

    // *** FIX: Remove stale overlay on pageshow (handles back-button blank page) ***
    window.addEventListener('pageshow', function (e) {
      const overlay = document.querySelector('.page-transition');
      if (overlay) {
        overlay.style.animation = 'none';
        overlay.style.transform = 'scaleY(0)';
        overlay.style.visibility = 'hidden';
        overlay.style.pointerEvents = 'none';
      }
      // Also re-show main content
      const main = document.getElementById('main');
      if (main) {
        main.classList.add('show');
        const navEl = document.querySelector('nav');
        if (navEl) navEl.classList.add('show');
      }
    });

    // Intercept internal link clicks for smooth exit animation
    document.querySelectorAll('a[href]').forEach(function (link) {
      const href = link.getAttribute('href');
      // Only internal relative links
      if (!href || href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto') || href.startsWith('tel')) return;
      link.addEventListener('click', function (e) {
        e.preventDefault();
        const dest = href;
        const overlay = document.querySelector('.page-transition');
        if (overlay) {
          overlay.style.animation  = 'none';
          overlay.style.visibility = 'visible';
          overlay.style.transform  = 'scaleY(0)';
          overlay.style.transformOrigin = 'bottom';
          overlay.style.pointerEvents   = 'all';
          // Force reflow
          overlay.offsetHeight;
          overlay.style.transition = 'transform .5s cubic-bezier(.77,0,.18,1)';
          overlay.style.transform  = 'scaleY(1)';
          setTimeout(function () { window.location.href = dest; }, 480);
        } else {
          window.location.href = dest;
        }
      });
    });
  });

})();
