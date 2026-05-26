/**
 * THE GRINDSCAPE — main.js
 * Phase 5: JavaScript & Form Validation
 * Handles: navbar scroll, form validation, modals, scroll reveals
 */

(function () {
  'use strict';

  /* ──────────────────────────────────────
     NAVBAR — Scroll Shadow Effect
  ────────────────────────────────────── */
  const navbar = document.getElementById('mainNavbar');
  if (navbar) {
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // run once on load
  }

  /* ──────────────────────────────────────
     SCROLL REVEAL — Intersection Observer
  ────────────────────────────────────── */
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    revealEls.forEach((el) => observer.observe(el));
  }

  /* ──────────────────────────────────────
     SHARED FORM VALIDATION FACTORY
     Works for both the modal form and the
     standalone contact page form.
  ────────────────────────────────────── */

  /**
   * @param {Object} config
   *  formId, submitBtnId, ageInputId, ageErrorId,
   *  msgInputId, charCountId, successId,
   *  modalId (optional — if in a modal)
   */
  function initForm(config) {
    const form      = document.getElementById(config.formId);
    const submitBtn = document.getElementById(config.submitBtnId);
    if (!form || !submitBtn) return;

    const ageInput    = document.getElementById(config.ageInputId);
    const ageError    = document.getElementById(config.ageErrorId);
    const msgInput    = document.getElementById(config.msgInputId);
    const charCount   = document.getElementById(config.charCountId);
    const successBox  = document.getElementById(config.successId);

    /* Live character counter */
    if (msgInput && charCount) {
      msgInput.addEventListener('input', function () {
        const len = this.value.length;
        charCount.textContent = `${len} / 15 minimum characters`;
        charCount.style.color = len >= 15 ? '#6B8F71' : '#8a7068';
      });
    }

    /* Age live re-validation */
    if (ageInput && ageError) {
      ageInput.addEventListener('input', function () {
        validateAge(this, ageError);
      });
    }

    /* Submit handler */
    submitBtn.addEventListener('click', function () {
      if (successBox) successBox.classList.add('d-none');

      // Run age custom validation before Bootstrap's check
      if (ageInput && ageError) validateAge(ageInput, ageError);

      // Trigger Bootstrap validation styles
      form.classList.add('was-validated');

      if (!form.checkValidity()) {
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      // ── All valid ──
      const nameField = form.querySelector('[data-role="name"]') || document.getElementById(config.nameInputId);
      const name = nameField ? nameField.value.trim() : 'Guest';

      if (successBox) {
        successBox.className = 'alert mt-3';
        successBox.style.cssText =
          'background:#f0faf0;border:1px solid #b2ddb5;border-radius:12px;color:#2d6a30;font-size:0.9rem;';
        successBox.innerHTML = `
          <strong>Reservation Confirmed!</strong><br>
          Thank you, <strong>${escapeHtml(name)}</strong>! We've received your request and
          will send a confirmation to your email shortly. See you soon at The Grindscape! ☕
        `;
      }

      resetForm(form, charCount, ageInput);

      // Auto-close modal if applicable
      if (config.modalId) {
        setTimeout(() => {
          const modalEl = document.getElementById(config.modalId);
          const modal = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
          if (modal) {
            modal.hide();
            if (successBox) successBox.classList.add('d-none');
          }
        }, 3200);
      }
    });

    // Reset on modal close (if applicable)
    if (config.modalId) {
      const modalEl = document.getElementById(config.modalId);
      if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', () => {
          resetForm(form, charCount, ageInput);
          if (successBox) successBox.classList.add('d-none');
          if (ageError) ageError.textContent = 'Please enter a valid age (12 years and above).';
        });
      }
    }
  }

  /* ── Helpers ── */
  function validateAge(input, errorEl) {
    const val = parseInt(input.value, 10);
    if (isNaN(val) || val < 12) {
      input.setCustomValidity('too_young');
      if (errorEl) errorEl.textContent = '⚠ Safety Alert: Guests must be 12 years or older.';
    } else if (val > 120) {
      input.setCustomValidity('invalid_age');
      if (errorEl) errorEl.textContent = 'Please enter a realistic age.';
    } else {
      input.setCustomValidity('');
      if (errorEl) errorEl.textContent = 'Please enter a valid age (12 years and above).';
    }
  }

  function resetForm(form, charCount, ageInput) {
    form.reset();
    form.classList.remove('was-validated');
    if (charCount) {
      charCount.textContent = '0 / 15 minimum characters';
      charCount.style.color = '';
    }
    if (ageInput) ageInput.setCustomValidity('');
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  /* ──────────────────────────────────────
     INIT — Modal Form (index.html, history.html, variety.html)
  ────────────────────────────────────── */
  initForm({
    formId:      'contactForm',
    submitBtnId: 'submitContactForm',
    nameInputId: 'modalName',
    ageInputId:  'modalAge',
    ageErrorId:  'modalAgeError',
    msgInputId:  'modalMessage',
    charCountId: 'charCount',
    successId:   'formSuccess',
    modalId:     'contactModal',
  });

  /* ──────────────────────────────────────
     INIT — Standalone Contact Page Form
  ────────────────────────────────────── */
  initForm({
    formId:      'pageContactForm',
    submitBtnId: 'submitPageForm',
    nameInputId: 'pageName',
    ageInputId:  'pageAge',
    ageErrorId:  'pageAgeError',
    msgInputId:  'pageMessage',
    charCountId: 'charCountPage',
    successId:   'pageFormSuccess',
    // No modalId → standalone page
  });

  /* ──────────────────────────────────────
     MENU TABS (history.html)
     Toggle menu category sections
  ────────────────────────────────────── */
  const menuTabBtns = document.querySelectorAll('[data-menu-tab]');
  const menuSections = document.querySelectorAll('[data-menu-section]');

  menuTabBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const target = this.dataset.menuTab;

      menuTabBtns.forEach((b) => b.classList.remove('active'));
      this.classList.add('active');

      menuSections.forEach((section) => {
        if (target === 'all' || section.dataset.menuSection === target) {
          section.style.display = '';
          section.classList.add('reveal', 'visible');
        } else {
          section.style.display = 'none';
        }
      });
    });
  });

  /* ──────────────────────────────────────
     GALLERY LIGHTBOX HINT (variety.html)
     Simple caption toggle on click
  ────────────────────────────────────── */
  document.querySelectorAll('.gallery-container').forEach((container) => {
    container.addEventListener('click', function () {
      this.classList.toggle('focused');
    });
  });

})();