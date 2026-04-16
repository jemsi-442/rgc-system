import './bootstrap';

const regionSelect = document.querySelector('[data-region-select]');
const districtSelect = document.querySelector('[data-district-select]');
const branchSelect = document.querySelector('[data-branch-select]');
const roleSelect = document.querySelector('[data-role-select]');
const roleGuidancePanel = document.querySelector('[data-role-guidance-panel]');
const csrfRefreshOnRestore = document.querySelector('[data-csrf-refresh-on-restore]');
const authForms = Array.from(document.querySelectorAll('[data-auth-form]'));
const districtField = document.querySelector('[data-district-field]');
const branchField = document.querySelector('[data-branch-field]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const siteHeader = document.querySelector('[data-site-header]');

const emptyOption = (select, fallback) => select?.dataset.emptyOptionLabel ?? fallback;
const selectedOption = (select) => select?.dataset.selectedValue ?? select?.value ?? '';

const syncRegistrationHierarchyVisibility = ({ showDistrict, showBranch }) => {
  if (districtField) {
    districtField.classList.toggle('hidden', !showDistrict);
  }

  if (districtSelect) {
    districtSelect.disabled = !showDistrict;
  }

  if (branchField) {
    branchField.classList.toggle('hidden', !showBranch);
  }

  if (branchSelect) {
    branchSelect.disabled = !showBranch;
  }
};

const roleGuidance = {
  super_admin: {
    title: 'Super Admin access',
    copy: 'Use this only for the few people who oversee the full platform, create branches, manage users, and keep the whole system aligned.',
    scope: 'Still assign a home branch below so the account has a clear church location, even though the role can work across the full platform.',
  },
  regional_admin: {
    title: 'Regional Admin access',
    copy: 'Choose this for someone who should monitor districts, branches, and activity across one region without becoming a full platform administrator.',
    scope: 'Set the home branch inside the same region so reporting, announcements, and leadership scope stay aligned.',
  },
  district_admin: {
    title: 'District Admin access',
    copy: 'Choose this when the person coordinates several branches inside one district and needs district-wide visibility.',
    scope: 'Keep the selected branch inside the same district because this account still needs a home church location.',
  },
  branch_admin: {
    title: 'Branch Admin access',
    copy: 'Use branch admin for the main local coordinator who manages branch users, records, announcements, and day-to-day branch activity.',
    scope: 'The branch selected below becomes the person’s operational home for records, communication, and branch books.',
  },
  bishop: {
    title: 'Bishop access',
    copy: 'Use bishop when the person needs branch-facing oversight and communication access without becoming the branch records administrator.',
    scope: 'Choose the branch they primarily serve so announcements and branch communication stay tied to the right church location.',
  },
  pastor: {
    title: 'Pastor access',
    copy: 'Use pastor for ministers who need branch communication, notices, and branch-facing workspace access.',
    scope: 'Choose the branch they serve so the account opens into the correct branch environment.',
  },
  accountant: {
    title: 'Accountant access',
    copy: 'Use accountant for finance staff who should work with offerings, expenses, payment follow-up, and branch finance activity.',
    scope: 'Choose the branch whose books they maintain so finance records stay attached to the correct church location.',
  },
  member: {
    title: 'Member access',
    copy: 'Use member for regular church users who should receive branch updates, giving access, and normal sign-in without leadership controls.',
    scope: 'The region, district, and branch below still matter because every account keeps a home branch, even when the role is later promoted into district or regional leadership.',
  },
};

const syncRoleGuidance = (value) => {
  if (!roleSelect || !roleGuidancePanel) return;

  const titleNode = roleGuidancePanel.querySelector('[data-role-guidance-title]');
  const copyNode = roleGuidancePanel.querySelector('[data-role-guidance-copy]');
  const scopeNode = roleGuidancePanel.querySelector('[data-role-scope-copy]');
  const guidance = roleGuidance[value] ?? roleGuidance.member;

  if (titleNode) titleNode.textContent = guidance.title;
  if (copyNode) copyNode.textContent = guidance.copy;
  if (scopeNode) scopeNode.textContent = guidance.scope;
};

async function loadDistricts(regionId, selectedDistrictId = '') {
  if (!districtSelect) return;
  districtSelect.innerHTML = `<option value="">${emptyOption(districtSelect, 'Select district')}</option>`;
  if (branchSelect) {
    branchSelect.innerHTML = `<option value="">${emptyOption(branchSelect, 'Select branch')}</option>`;
  }
  syncRegistrationHierarchyVisibility({ showDistrict: Boolean(regionId), showBranch: false });
  if (!regionId) return;

  const response = await fetch(`/api/districts?region_id=${regionId}`);
  const districts = await response.json();

  districts.forEach((district) => {
    const option = document.createElement('option');
    option.value = district.id;
    option.textContent = district.name;
    if (String(selectedDistrictId) === String(district.id)) {
      option.selected = true;
    }
    districtSelect.appendChild(option);
  });
}

async function loadBranches(districtId, selectedBranchId = '') {
  if (!branchSelect) return;
  branchSelect.innerHTML = `<option value="">${emptyOption(branchSelect, 'Select branch')}</option>`;
  syncRegistrationHierarchyVisibility({
    showDistrict: Boolean(selectedOption(regionSelect)),
    showBranch: Boolean(districtId),
  });
  if (!districtId) return;

  const response = await fetch(`/api/branches?district_id=${districtId}`);
  const branches = await response.json();

  branches.forEach((branch) => {
    const option = document.createElement('option');
    option.value = branch.id;
    option.textContent = branch.name;
    if (String(selectedBranchId) === String(branch.id)) {
      option.selected = true;
    }
    branchSelect.appendChild(option);
  });
}

if (regionSelect) {
  regionSelect.addEventListener('change', async (event) => {
    await loadDistricts(event.target.value);
  });
}

if (districtSelect) {
  districtSelect.addEventListener('change', async (event) => {
    await loadBranches(event.target.value);
  });
}

if (regionSelect && districtSelect) {
  const initialRegionId = selectedOption(regionSelect);
  const initialDistrictId = selectedOption(districtSelect);
  const initialBranchId = selectedOption(branchSelect);

  syncRegistrationHierarchyVisibility({
    showDistrict: Boolean(initialRegionId),
    showBranch: Boolean(initialDistrictId),
  });

  if (initialRegionId) {
    loadDistricts(initialRegionId, initialDistrictId)
      .then(() => {
        if (initialDistrictId && branchSelect) {
          return loadBranches(initialDistrictId, initialBranchId);
        }

        return null;
      })
      .catch(() => {
        // Keep forms usable even if dependent lookups fail temporarily.
      });
  }
}

if (roleSelect) {
  syncRoleGuidance(roleSelect.value);

  roleSelect.addEventListener('change', (event) => {
    syncRoleGuidance(event.target.value);
  });
}

if (csrfRefreshOnRestore) {
  window.addEventListener('pageshow', (event) => {
    const navigationEntry = window.performance?.getEntriesByType?.('navigation')?.[0];
    const restoredFromCache = event.persisted || navigationEntry?.type === 'back_forward';

    if (restoredFromCache) {
      window.location.reload();
    }
  });
}

if (csrfRefreshOnRestore && authForms.length > 0) {
  const csrfEndpoint = csrfRefreshOnRestore.dataset.csrfRefreshEndpoint;
  const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');

  authForms.forEach((form) => {
    form.addEventListener('submit', async (event) => {
      if (form.dataset.csrfReady === 'true' || !csrfEndpoint) {
        form.dataset.csrfReady = 'false';
        return;
      }

      event.preventDefault();

      try {
        const response = await fetch(csrfEndpoint, {
          credentials: 'same-origin',
          cache: 'no-store',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
        });

        if (response.ok) {
          const payload = await response.json();
          const freshToken = payload?.token;
          const csrfField = form.querySelector('input[name="_token"]');

          if (freshToken && csrfField) {
            csrfField.value = freshToken;

            if (csrfMetaTag) {
              csrfMetaTag.setAttribute('content', freshToken);
            }
          }
        }
      } catch (_error) {
        // If the refresh request fails temporarily, continue with the current token.
      }

      form.dataset.csrfReady = 'true';
      form.requestSubmit();
    });
  });
}

if (menuToggle && mobileMenu) {
  const menuLabel = menuToggle.querySelector('[data-menu-label]');
  const menuAnnounce = menuToggle.querySelector('[data-menu-announce]');
  const openLabel = menuToggle.dataset.openLabel ?? 'Menu';
  const closeLabel = menuToggle.dataset.closeLabel ?? 'Close';

  const syncMenuState = (isOpen) => {
    menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    mobileMenu.classList.toggle('is-open', isOpen);
    document.body.classList.toggle('menu-open', isOpen);

    if (menuLabel) {
      menuLabel.textContent = isOpen ? closeLabel : openLabel;
    }

    if (menuAnnounce) {
      menuAnnounce.textContent = isOpen ? closeLabel : openLabel;
    }
  };

  syncMenuState(false);

  menuToggle.addEventListener('click', () => {
    const nextState = menuToggle.getAttribute('aria-expanded') !== 'true';
    syncMenuState(nextState);
  });

  mobileMenu.querySelectorAll('a, button').forEach((item) => {
    item.addEventListener('click', () => {
      if (window.innerWidth < 768) {
        syncMenuState(false);
      }
    });
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      syncMenuState(false);
    }
  });
}

if (siteHeader) {
  let lastScrollY = window.scrollY;
  let ticking = false;

  const syncHeaderState = () => {
    const currentScrollY = window.scrollY;
    const delta = currentScrollY - lastScrollY;
    const menuIsOpen = mobileMenu?.classList.contains('is-open') ?? false;

    siteHeader.classList.toggle('is-compact', currentScrollY > 14);

    if (menuIsOpen || currentScrollY <= 12) {
      siteHeader.classList.remove('is-hidden');
      lastScrollY = currentScrollY;
      ticking = false;
      return;
    }

    if (delta > 10 && currentScrollY > 96) {
      siteHeader.classList.add('is-hidden');
    } else if (delta < -8) {
      siteHeader.classList.remove('is-hidden');
    }

    lastScrollY = currentScrollY;
    ticking = false;
  };

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(syncHeaderState);
      ticking = true;
    }
  }, { passive: true });
}

const slider = document.querySelector('[data-hero-slider]');

if (slider) {
  const slides = Array.from(slider.querySelectorAll('[data-slide]'));
  const dots = Array.from(slider.querySelectorAll('[data-slide-dot]'));
  let activeIndex = 0;

  const activateSlide = (index) => {
    slides.forEach((slide, slideIndex) => {
      slide.classList.toggle('is-active', slideIndex === index);
    });

    dots.forEach((dot, dotIndex) => {
      dot.classList.toggle('is-active', dotIndex === index);
    });

    activeIndex = index;
  };

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => activateSlide(index));
  });

  if (slides.length > 1) {
    setInterval(() => {
      const nextIndex = (activeIndex + 1) % slides.length;
      activateSlide(nextIndex);
    }, 5200);
  }
}

const announcementImageInput = document.querySelector('[data-announcement-image-input]');
const announcementPreview = document.querySelector('[data-announcement-preview]');
const announcementPreviewImage = document.querySelector('[data-announcement-preview-image]');
const announcementPreviewName = document.querySelector('[data-announcement-preview-name]');
let announcementPreviewUrl = null;

if (announcementImageInput && announcementPreview && announcementPreviewImage) {
  const clearAnnouncementPreview = () => {
    announcementPreview.classList.add('hidden');
    announcementPreviewImage.removeAttribute('src');

    if (announcementPreviewName) {
      announcementPreviewName.textContent = announcementPreview.dataset.emptyLabel ?? 'Selected image';
    }

    if (announcementPreviewUrl) {
      URL.revokeObjectURL(announcementPreviewUrl);
      announcementPreviewUrl = null;
    }
  };

  announcementImageInput.addEventListener('change', (event) => {
    const [file] = event.target.files ?? [];

    if (!file) {
      clearAnnouncementPreview();
      return;
    }

    if (announcementPreviewUrl) {
      URL.revokeObjectURL(announcementPreviewUrl);
    }

    announcementPreviewUrl = URL.createObjectURL(file);
    announcementPreviewImage.src = announcementPreviewUrl;
    announcementPreview.classList.remove('hidden');

    if (announcementPreviewName) {
      announcementPreviewName.textContent = file.name;
    }
  });

  window.addEventListener('beforeunload', () => {
    if (announcementPreviewUrl) {
      URL.revokeObjectURL(announcementPreviewUrl);
    }
  });
}

const announcementLightbox = document.querySelector('[data-announcement-lightbox]');

if (announcementLightbox) {
  const lightboxImage = announcementLightbox.querySelector('[data-announcement-lightbox-image]');
  const lightboxTitle = announcementLightbox.querySelector('[data-announcement-lightbox-title]');
  const lightboxTriggers = Array.from(document.querySelectorAll('[data-announcement-lightbox-trigger]'));
  const closeLightbox = () => {
    announcementLightbox.hidden = true;
    announcementLightbox.classList.remove('is-open');
    document.body.classList.remove('lightbox-open');
    if (lightboxImage) {
      lightboxImage.removeAttribute('src');
      lightboxImage.removeAttribute('alt');
    }
  };

  lightboxTriggers.forEach((trigger) => {
    trigger.addEventListener('click', () => {
      if (lightboxImage) {
        lightboxImage.src = trigger.dataset.imageSrc ?? '';
        lightboxImage.alt = trigger.dataset.imageAlt ?? '';
      }

      if (lightboxTitle) {
        lightboxTitle.textContent = trigger.dataset.imageTitle ?? 'Announcement';
      }

      announcementLightbox.hidden = false;
      announcementLightbox.classList.add('is-open');
      document.body.classList.add('lightbox-open');
    });
  });

  announcementLightbox.querySelectorAll('[data-announcement-lightbox-close]').forEach((element) => {
    element.addEventListener('click', closeLightbox);
  });

  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && announcementLightbox.classList.contains('is-open')) {
      closeLightbox();
    }
  });
}

const pwaInstallPrompt = document.querySelector('[data-pwa-install-prompt]');
const pwaInstallButtons = Array.from(document.querySelectorAll('[data-pwa-install-trigger]'));

if (pwaInstallPrompt) {
  const installAction = pwaInstallPrompt.querySelector('[data-pwa-install-action]');
  const dismissAction = pwaInstallPrompt.querySelector('[data-pwa-install-dismiss]');
  const installTitle = pwaInstallPrompt.querySelector('[data-pwa-install-title]');
  const installMessage = pwaInstallPrompt.querySelector('[data-pwa-install-message]');
  const primaryInstallButton = pwaInstallButtons[0] ?? null;
  const installLabel = primaryInstallButton?.dataset.installLabel ?? 'Install App';
  const installingLabel = primaryInstallButton?.dataset.installingLabel ?? 'Preparing install...';
  const readyMessage = pwaInstallPrompt.dataset.readyMessage ?? 'Install this app on your device for faster access.';
  const iosMessage = pwaInstallPrompt.dataset.iosMessage ?? 'Open Share and choose Add to Home Screen.';
  const promptKey = 'rgc:pwa-install-dismissed';
  const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
  const isStandalone = window.matchMedia?.('(display-mode: standalone)').matches || window.navigator.standalone === true;
  let deferredInstallPrompt = null;

  const setInstallCopy = (message, title = installLabel) => {
    if (installTitle) {
      installTitle.textContent = title;
    }

    if (installMessage) {
      installMessage.textContent = message;
    }
  };

  const hideInstallPrompt = () => {
    pwaInstallPrompt.classList.add('hidden');
    pwaInstallButtons.forEach((button) => {
      button.classList.add('hidden');
    });
  };

  const syncActionVisibility = (showInstallAction, showDismissAction = true) => {
    installAction?.classList.toggle('hidden', !showInstallAction);
    dismissAction?.classList.toggle('hidden', !showDismissAction);

    pwaInstallButtons.forEach((button) => {
      button.classList.toggle('hidden', !showInstallAction);
    });
  };

  const showInstallPrompt = (message, title = installLabel, showInstallAction = true, showDismissAction = true) => {
    setInstallCopy(message, title);
    pwaInstallPrompt.classList.remove('hidden');
    syncActionVisibility(showInstallAction, showDismissAction);

    if (showInstallAction) {
      pwaInstallButtons.forEach((button) => {
        button.textContent = button.dataset.installLabel ?? title;
      });
    }
  };

  const markDismissed = () => {
    window.localStorage.setItem(promptKey, '1');
  };

  const clearDismissed = () => {
    window.localStorage.removeItem(promptKey);
  };

  const registerServiceWorker = async () => {
    if (!('serviceWorker' in navigator)) {
      return;
    }

    try {
      const register = () => navigator.serviceWorker.register('/sw.js');

      if (document.readyState === 'complete') {
        await register();
        return;
      }

      window.addEventListener('load', () => {
        register().catch(() => {
          // Keep install prompt optional if service worker registration fails.
        });
      }, { once: true });
    } catch (_error) {
      // Keep install prompt optional if service worker registration fails.
    }
  };

  registerServiceWorker();

  if (isStandalone) {
    hideInstallPrompt();
  } else if (isIos && !window.localStorage.getItem(promptKey)) {
    showInstallPrompt(iosMessage, installLabel);
  }

  window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    clearDismissed();
    showInstallPrompt(readyMessage, installLabel);
  });

  window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    clearDismissed();
    hideInstallPrompt();
  });

  dismissAction?.addEventListener('click', () => {
    markDismissed();
    hideInstallPrompt();
  });

  const triggerInstall = async () => {
    if (isStandalone) {
      hideInstallPrompt();
      return;
    }

    if (!deferredInstallPrompt) {
      if (isIos) {
        showInstallPrompt(iosMessage, installLabel);
      }

      return;
    }

    pwaInstallButtons.forEach((button) => {
      button.textContent = button.dataset.installingLabel ?? installingLabel;
    });

    await deferredInstallPrompt.prompt();
    const choice = await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;

    pwaInstallButtons.forEach((button) => {
      button.textContent = button.dataset.installLabel ?? installLabel;
    });

    if (choice.outcome === 'accepted') {
      hideInstallPrompt();
      return;
    }

    showInstallPrompt(readyMessage, installLabel);
  };

  installAction?.addEventListener('click', triggerInstall);
  pwaInstallButtons.forEach((button) => {
    button.addEventListener('click', triggerInstall);
  });
}


const passwordInputs = Array.from(document.querySelectorAll('input[type="password"]'));

if (passwordInputs.length > 0) {
  const isSwahili = document.documentElement.lang?.toLowerCase().startsWith('sw');
  const showLabel = isSwahili ? 'Onesha' : 'Show';
  const hideLabel = isSwahili ? 'Ficha' : 'Hide';

  passwordInputs.forEach((input, index) => {
    if (input.dataset.passwordToggleReady === 'true') {
      return;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'password-toggle-wrap';

    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'password-toggle-button';
    button.dataset.passwordToggle = 'true';
    button.setAttribute('aria-controls', input.id || `password-field-${index}`);

    if (!input.id) {
      input.id = `password-field-${index}`;
    }

    const syncButton = () => {
      const showing = input.type === 'text';
      button.textContent = showing ? hideLabel : showLabel;
      button.setAttribute('aria-label', showing ? hideLabel : showLabel);
      button.setAttribute('aria-pressed', showing ? 'true' : 'false');
    };

    syncButton();

    button.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password';
      syncButton();
      input.focus({ preventScroll: true });
      const length = input.value.length;
      try {
        input.setSelectionRange(length, length);
      } catch (error) {
        // Some input types may not support explicit selection control.
      }
    });

    wrapper.appendChild(button);
    input.dataset.passwordToggleReady = 'true';
  });
}

const shareButtons = Array.from(document.querySelectorAll('[data-share-button]'));

if (shareButtons.length > 0) {
  shareButtons.forEach((button) => {
    const status = button.parentElement?.querySelector('[data-share-status]');
    const shareUrl = button.dataset.shareUrl ?? window.location.href;
    const shareTitle = button.dataset.shareTitle ?? document.title;
    const successLabel = button.dataset.shareSuccess ?? 'Link copied.';
    const failureLabel = button.dataset.shareFailure ?? 'Unable to share this item.';

    const setStatus = (message) => {
      if (status) {
        status.textContent = message;
      }
    };

    button.addEventListener('click', async () => {
      try {
        if (navigator.share) {
          await navigator.share({ title: shareTitle, url: shareUrl });
          setStatus(successLabel);
          return;
        }

        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(shareUrl);
          setStatus(successLabel);
          return;
        }

        setStatus(failureLabel);
      } catch (_error) {
        setStatus(failureLabel);
      }
    });
  });
}

const announcementScopeSelect = document.querySelector('[data-announcement-scope-select]');
const announcementDistrictShell = document.querySelector('[data-announcement-district-shell]');
const announcementDistrictSelect = document.querySelector('[data-announcement-district-select]');
const announcementBranchShell = document.querySelector('[data-announcement-branch-shell]');
const announcementBranchSelect = document.querySelector('[data-announcement-branch-select]');
const announcementSelectedBranchesShell = document.querySelector('[data-announcement-selected-branches-shell]');
const announcementSelectedBranchesSelect = document.querySelector('[data-announcement-selected-branches-select]');
const announcementDeliveryPreview = document.querySelector('[data-announcement-delivery-preview]');

async function loadAnnouncementBranches(districtId, selectedBranchId = '') {
  if (!announcementBranchSelect) return;
  announcementBranchSelect.innerHTML = `<option value="">${emptyOption(announcementBranchSelect, 'Select branch')}</option>`;
  if (!districtId) return;

  const response = await fetch(`/api/branches?district_id=${districtId}`);
  const branches = await response.json();

  branches.forEach((branch) => {
    const option = document.createElement('option');
    option.value = branch.id;
    option.textContent = branch.name;
    if (String(selectedBranchId) === String(branch.id)) {
      option.selected = true;
    }
    announcementBranchSelect.appendChild(option);
  });
}

if (announcementScopeSelect) {
  const previewMessage = () => {
    if (!announcementDeliveryPreview) return;

    const previewBody = announcementDeliveryPreview.querySelector('p');

    if (!previewBody) return;

    const scope = announcementScopeSelect.value;
    const selectedCount = announcementSelectedBranchesSelect
      ? Array.from(announcementSelectedBranchesSelect.selectedOptions).length
      : 0;

    if (scope === 'global') {
      previewBody.textContent = announcementDeliveryPreview.dataset.labelGlobal ?? 'This announcement will go to all users and all branches.';
      return;
    }

    if (scope === 'selected_branches') {
      const template = selectedCount === 1
        ? announcementDeliveryPreview.dataset.labelSelectedOne
        : announcementDeliveryPreview.dataset.labelSelectedCount;

      previewBody.textContent = (template ?? 'This announcement will go to :count selected branches.')
        .replace(':count', String(selectedCount));
      return;
    }

    if (scope === 'region') {
      previewBody.textContent = announcementDeliveryPreview.dataset.labelRegion ?? 'This announcement will go to your whole region.';
      return;
    }

    if (scope === 'district') {
      previewBody.textContent = announcementDistrictSelect
        ? (announcementDeliveryPreview.dataset.labelDistrict ?? 'This announcement will go to the selected district only.')
        : (announcementDeliveryPreview.dataset.labelDistrictFixed ?? 'This announcement will go to your whole district.');
      return;
    }

    previewBody.textContent = announcementBranchSelect
      ? (announcementDeliveryPreview.dataset.labelBranch ?? 'This announcement will go to the selected branch only.')
      : (announcementDeliveryPreview.dataset.labelBranchFixed ?? 'This announcement will stay inside your branch only.');
  };

  const syncAnnouncementScope = async () => {
    const scope = announcementScopeSelect.value;
    const showDistrict = scope === 'district' || scope === 'branch';
    const showBranch = scope === 'branch';
    const showSelectedBranches = scope === 'selected_branches';

    if (announcementSelectedBranchesShell && announcementSelectedBranchesSelect) {
      announcementSelectedBranchesShell.classList.toggle('hidden', !showSelectedBranches);
      announcementSelectedBranchesSelect.disabled = !showSelectedBranches;

      if (!showSelectedBranches) {
        Array.from(announcementSelectedBranchesSelect.options).forEach((option) => {
          option.selected = false;
        });
      }
    }

    if (announcementDistrictShell && announcementDistrictSelect) {
      announcementDistrictShell.classList.toggle('hidden', !showDistrict);
      announcementDistrictSelect.disabled = !showDistrict;

      if (!showDistrict) {
        announcementDistrictSelect.value = '';
      }
    }

    if (announcementBranchShell && announcementBranchSelect) {
      announcementBranchShell.classList.toggle('hidden', !showBranch);
      announcementBranchSelect.disabled = !showBranch;

      if (!showBranch) {
        announcementBranchSelect.innerHTML = `<option value="">${emptyOption(announcementBranchSelect, 'Select branch')}</option>`;
      }
    }

    if (showBranch && announcementDistrictSelect) {
      await loadAnnouncementBranches(announcementDistrictSelect.value, selectedOption(announcementBranchSelect));
    }

    previewMessage();
  };

  announcementScopeSelect.addEventListener('change', () => {
    syncAnnouncementScope().catch(() => {
      // Keep the form usable even if branch lookups fail temporarily.
    });
  });

  if (announcementDistrictSelect) {
    announcementDistrictSelect.addEventListener('change', async (event) => {
      if (announcementScopeSelect.value === 'branch') {
        await loadAnnouncementBranches(event.target.value);
      }

      previewMessage();
    });
  }

  if (announcementBranchSelect) {
    announcementBranchSelect.addEventListener('change', previewMessage);
  }

  if (announcementSelectedBranchesSelect) {
    announcementSelectedBranchesSelect.addEventListener('change', previewMessage);
  }

  syncAnnouncementScope().catch(() => {
    // Keep the form usable even if branch lookups fail temporarily.
  });
}

const sliderDropzone = document.querySelector('[data-slider-dropzone]');
const sliderImageInput = document.querySelector('[data-slider-image-input]');
const sliderPreview = document.querySelector('[data-slider-preview]');
const sliderPreviewImage = document.querySelector('[data-slider-preview-image]');
const sliderPreviewName = document.querySelector('[data-slider-preview-name]');
let sliderPreviewUrl = null;

if (sliderDropzone && sliderImageInput && sliderPreview && sliderPreviewImage) {
  const showSliderPreview = (file) => {
    if (!file) {
      sliderPreview.classList.add('hidden');
      sliderPreviewImage.removeAttribute('src');

      if (sliderPreviewName) {
        sliderPreviewName.textContent = sliderPreview.dataset.emptyLabel ?? 'Selected image';
      }

      if (sliderPreviewUrl) {
        URL.revokeObjectURL(sliderPreviewUrl);
        sliderPreviewUrl = null;
      }

      return;
    }

    if (sliderPreviewUrl) {
      URL.revokeObjectURL(sliderPreviewUrl);
    }

    sliderPreviewUrl = URL.createObjectURL(file);
    sliderPreviewImage.src = sliderPreviewUrl;
    sliderPreview.classList.remove('hidden');

    if (sliderPreviewName) {
      sliderPreviewName.textContent = file.name;
    }
  };

  sliderImageInput.addEventListener('change', (event) => {
    const [file] = event.target.files ?? [];
    showSliderPreview(file);
  });

  ['dragenter', 'dragover'].forEach((eventName) => {
    sliderDropzone.addEventListener(eventName, (event) => {
      event.preventDefault();
      sliderDropzone.classList.add('is-dragover');
    });
  });

  ['dragleave', 'dragend', 'drop'].forEach((eventName) => {
    sliderDropzone.addEventListener(eventName, (event) => {
      event.preventDefault();
      sliderDropzone.classList.remove('is-dragover');
    });
  });

  sliderDropzone.addEventListener('drop', (event) => {
    const files = event.dataTransfer?.files;
    const [file] = files ?? [];

    if (!file) {
      return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(file);
    sliderImageInput.files = transfer.files;
    showSliderPreview(file);
  });

  window.addEventListener('beforeunload', () => {
    if (sliderPreviewUrl) {
      URL.revokeObjectURL(sliderPreviewUrl);
    }
  });
}

document.querySelectorAll('[data-quick-amount]').forEach((button) => {
    button.addEventListener('click', () => {
        const amountInput = document.querySelector('#giving_amount');
        if (!amountInput) {
            return;
        }

        amountInput.value = button.getAttribute('data-quick-amount') || '';
        amountInput.dispatchEvent(new Event('input', { bubbles: true }));
    });
});


document.querySelectorAll('[data-copy-text]').forEach((button) => {
  button.addEventListener('click', async () => {
    const value = button.getAttribute('data-copy-text') || '';
    if (!value) {
      return;
    }

    const originalText = button.textContent;

    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(value);
      } else {
        const temp = document.createElement('input');
        temp.value = value;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        temp.remove();
      }

      button.textContent = button.dataset.copiedLabel || 'Copied';
    } catch (error) {
      button.textContent = button.dataset.failedLabel || 'Copy failed';
    }

    window.setTimeout(() => {
      button.textContent = originalText;
    }, 1800);
  });
});

document.querySelectorAll('[data-share-link]').forEach((button) => {
  button.addEventListener('click', async () => {
    const url = button.getAttribute('data-share-link') || '';
    const title = button.getAttribute('data-share-title') || document.title;

    if (!url) {
      return;
    }

    const originalText = button.textContent;

    try {
      if (navigator.share) {
        await navigator.share({ title, url });
      } else if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(url);
      }

      button.textContent = button.dataset.sharedLabel || 'Shared';
    } catch (error) {
      button.textContent = button.dataset.failedLabel || 'Share failed';
    }

    window.setTimeout(() => {
      button.textContent = originalText;
    }, 1800);
  });
});

const assistantWidget = document.querySelector('[data-assistant-widget]');

if (assistantWidget) {
  const assistantLauncher = assistantWidget.querySelector('[data-assistant-launcher]');
  const assistantPanel = assistantWidget.querySelector('[data-assistant-panel]');
  const assistantClose = assistantWidget.querySelector('[data-assistant-close]');
  const assistantForm = assistantWidget.querySelector('[data-assistant-form]');
  const assistantInput = assistantWidget.querySelector('[data-assistant-input]');
  const assistantMessages = assistantWidget.querySelector('[data-assistant-messages]');
  const assistantSuggestions = assistantWidget.querySelector('[data-assistant-suggestions]');
  const assistantSubmit = assistantWidget.querySelector('[data-assistant-submit]');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const endpoint = assistantWidget.dataset.endpoint ?? '';
  const feedbackEndpointTemplate = assistantWidget.dataset.feedbackEndpointTemplate ?? '';
  const thinkingLabel = assistantWidget.dataset.thinkingLabel ?? 'Thinking...';
  const errorLabel = assistantWidget.dataset.errorLabel ?? 'Something went wrong. Please try again in a moment.';
  const assistantName = assistantWidget.dataset.assistantName ?? 'RGC Assistant';
  const userName = assistantWidget.dataset.userName ?? 'You';
  const feedbackPrompt = assistantWidget.dataset.feedbackPrompt ?? 'Was this answer helpful?';
  const feedbackHelpful = assistantWidget.dataset.feedbackHelpful ?? 'Helpful';
  const feedbackUnhelpful = assistantWidget.dataset.feedbackUnhelpful ?? 'Not helpful';
  const feedbackSaved = assistantWidget.dataset.feedbackSaved ?? 'Feedback saved';
  const feedbackSaving = assistantWidget.dataset.feedbackSaving ?? 'Saving feedback...';
  const feedbackNoteLabel = assistantWidget.dataset.feedbackNoteLabel ?? 'Tell us what was missing (optional)';
  const feedbackNotePlaceholder = assistantWidget.dataset.feedbackNotePlaceholder ?? 'Write a short note so we can improve this answer.';
  const feedbackNoteSave = assistantWidget.dataset.feedbackNoteSave ?? 'Save feedback';
  const feedbackNoteSkip = assistantWidget.dataset.feedbackNoteSkip ?? 'Skip note';
  const feedbackNoteTitle = assistantWidget.dataset.feedbackNoteTitle ?? 'Feedback note';

  const setPanelState = (isOpen) => {
    if (!assistantPanel || !assistantLauncher) {
      return;
    }

    assistantPanel.hidden = !isOpen;
    assistantPanel.classList.toggle('is-open', isOpen);
    assistantLauncher.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    if (isOpen && assistantInput) {
      window.setTimeout(() => assistantInput.focus(), 60);
    }
  };

  const scrollMessages = () => {
    if (assistantMessages) {
      assistantMessages.scrollTop = assistantMessages.scrollHeight;
    }
  };

  const setFeedbackStatus = (shell, message, isError = false) => {
    const status = shell.querySelector('.assistant-feedback-status');
    if (!status) {
      return;
    }

    status.hidden = false;
    status.textContent = message;
    status.classList.toggle('is-error', isError);
  };

  const toggleFeedbackNoteShell = (shell, isVisible) => {
    const noteShell = shell.querySelector('[data-assistant-feedback-note-shell]');
    if (!(noteShell instanceof HTMLElement)) {
      return;
    }

    noteShell.hidden = !isVisible;

    if (isVisible) {
      const textarea = noteShell.querySelector('[data-assistant-feedback-note-input]');
      if (textarea instanceof HTMLTextAreaElement) {
        window.setTimeout(() => textarea.focus(), 30);
      }
    }
  };

  const setFeedbackBusy = (shell, isBusy) => {
    shell.querySelectorAll('button, textarea').forEach((element) => {
      if ('disabled' in element) {
        element.disabled = isBusy;
      }
    });
  };

  const setFeedbackState = (shell, selectedValue, note = '') => {
    shell.querySelectorAll('[data-assistant-feedback]').forEach((button) => {
      const isSelected = button.dataset.assistantFeedback === selectedValue;
      button.disabled = true;
      button.classList.toggle('is-selected', isSelected);
    });

    const noteShell = shell.querySelector('[data-assistant-feedback-note-shell]');
    if (noteShell instanceof HTMLElement) {
      noteShell.hidden = true;
      noteShell.querySelectorAll('button, textarea').forEach((element) => {
        if ('disabled' in element) {
          element.disabled = true;
        }
      });
    }

    let noteDisplay = shell.querySelector('.assistant-feedback-note-saved');
    if (note) {
      if (!(noteDisplay instanceof HTMLElement)) {
        noteDisplay = document.createElement('p');
        noteDisplay.className = 'assistant-feedback-note-saved';
        shell.appendChild(noteDisplay);
      }
      noteDisplay.textContent = `${feedbackNoteTitle}: ${note}`;
    } else if (noteDisplay instanceof HTMLElement) {
      noteDisplay.remove();
    }

    setFeedbackStatus(shell, feedbackSaved, false);
  };

  const sendFeedback = async (shell, helpful, note = '') => {
    const interactionId = shell.dataset.feedbackFor ?? '';
    if (!interactionId || !feedbackEndpointTemplate) {
      return;
    }

    const endpointUrl = feedbackEndpointTemplate.replace('__ID__', interactionId);
    setFeedbackBusy(shell, true);
    setFeedbackStatus(shell, feedbackSaving, false);

    try {
      const response = await fetch(endpointUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ helpful, note }),
      });

      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload.message ?? errorLabel);
      }

      setFeedbackState(shell, helpful ? '1' : '0', payload.feedback_note ?? note);
    } catch (_error) {
      setFeedbackBusy(shell, false);
      setFeedbackStatus(shell, errorLabel, true);
      if (!helpful) {
        toggleFeedbackNoteShell(shell, true);
      }
    }
  };

  const buildFeedbackControls = (interactionId) => {
    if (!interactionId || !feedbackEndpointTemplate) {
      return null;
    }

    const shell = document.createElement('div');
    shell.className = 'assistant-feedback';
    shell.dataset.feedbackFor = String(interactionId);

    const prompt = document.createElement('span');
    prompt.className = 'assistant-feedback-prompt';
    prompt.textContent = feedbackPrompt;
    shell.appendChild(prompt);

    const buttons = document.createElement('div');
    buttons.className = 'assistant-feedback-actions';

    [
      { label: feedbackHelpful, value: '1' },
      { label: feedbackUnhelpful, value: '0' },
    ].forEach((item) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'assistant-feedback-button';
      button.dataset.assistantFeedback = item.value;
      button.textContent = item.label;
      buttons.appendChild(button);
    });

    shell.appendChild(buttons);

    const noteShell = document.createElement('div');
    noteShell.className = 'assistant-feedback-note-shell';
    noteShell.hidden = true;
    noteShell.setAttribute('data-assistant-feedback-note-shell', '');

    const noteLabel = document.createElement('label');
    noteLabel.className = 'assistant-feedback-note-label';
    noteLabel.textContent = feedbackNoteLabel;
    noteShell.appendChild(noteLabel);

    const noteInput = document.createElement('textarea');
    noteInput.className = 'assistant-feedback-note-input';
    noteInput.rows = 3;
    noteInput.placeholder = feedbackNotePlaceholder;
    noteInput.setAttribute('data-assistant-feedback-note-input', '');
    noteShell.appendChild(noteInput);

    const noteActions = document.createElement('div');
    noteActions.className = 'assistant-feedback-note-actions';

    const saveButton = document.createElement('button');
    saveButton.type = 'button';
    saveButton.className = 'assistant-feedback-note-submit';
    saveButton.setAttribute('data-assistant-feedback-note-save', '');
    saveButton.textContent = feedbackNoteSave;
    noteActions.appendChild(saveButton);

    const skipButton = document.createElement('button');
    skipButton.type = 'button';
    skipButton.className = 'assistant-feedback-note-skip';
    skipButton.setAttribute('data-assistant-feedback-note-skip', '');
    skipButton.textContent = feedbackNoteSkip;
    noteActions.appendChild(skipButton);

    noteShell.appendChild(noteActions);
    shell.appendChild(noteShell);

    const status = document.createElement('span');
    status.className = 'assistant-feedback-status';
    status.hidden = true;
    shell.appendChild(status);

    return shell;
  };

  const appendMessage = (author, content, type = 'bot', options = {}) => {
    if (!assistantMessages) {
      return null;
    }

    const message = document.createElement('article');
    message.className = `assistant-message assistant-message--${type}`;

    const authorElement = document.createElement('span');
    authorElement.className = 'assistant-message-author';
    authorElement.textContent = author;

    const bodyElement = document.createElement('p');
    bodyElement.textContent = content;

    message.appendChild(authorElement);
    message.appendChild(bodyElement);

    if (type === 'bot') {
      const feedbackControls = buildFeedbackControls(options.interactionId);
      if (feedbackControls) {
        message.appendChild(feedbackControls);
      }
    }

    assistantMessages.appendChild(message);
    scrollMessages();

    return message;
  };

  const renderSuggestions = (items) => {
    if (!assistantSuggestions) {
      return;
    }

    assistantSuggestions.innerHTML = '';

    (items ?? []).slice(0, 3).forEach((item) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'assistant-suggestion';
      button.setAttribute('data-assistant-suggestion', '');
      button.textContent = item;
      assistantSuggestions.appendChild(button);
    });
  };

  assistantLauncher?.addEventListener('click', () => {
    const shouldOpen = assistantLauncher.getAttribute('aria-expanded') !== 'true';
    setPanelState(shouldOpen);
  });

  assistantClose?.addEventListener('click', () => {
    setPanelState(false);
  });

  assistantSuggestions?.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLButtonElement) || !target.hasAttribute('data-assistant-suggestion')) {
      return;
    }

    if (assistantInput) {
      assistantInput.value = target.textContent ?? '';
      assistantInput.focus();
    }
  });

  assistantMessages?.addEventListener('click', async (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) {
      return;
    }

    const shell = target.closest('.assistant-feedback');
    if (!(shell instanceof HTMLElement)) {
      return;
    }

    if (target instanceof HTMLButtonElement && target.hasAttribute('data-assistant-feedback')) {
      if ((target.dataset.assistantFeedback ?? '') === '0') {
        shell.querySelectorAll('[data-assistant-feedback]').forEach((button) => {
          button.classList.toggle('is-selected', button === target);
        });
        setFeedbackStatus(shell, feedbackNoteLabel, false);
        toggleFeedbackNoteShell(shell, true);
        return;
      }

      await sendFeedback(shell, true);
      return;
    }

    if (target instanceof HTMLButtonElement && target.hasAttribute('data-assistant-feedback-note-save')) {
      const noteInput = shell.querySelector('[data-assistant-feedback-note-input]');
      const note = noteInput instanceof HTMLTextAreaElement ? noteInput.value.trim() : '';
      await sendFeedback(shell, false, note);
      return;
    }

    if (target instanceof HTMLButtonElement && target.hasAttribute('data-assistant-feedback-note-skip')) {
      await sendFeedback(shell, false, '');
    }
  });

  assistantInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      assistantForm?.requestSubmit();
    }
  });

  assistantForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!assistantInput || !assistantSubmit || !endpoint) {
      return;
    }

    const question = assistantInput.value.trim();
    if (!question) {
      return;
    }

    appendMessage(userName, question, 'user');
    assistantInput.value = '';
    assistantSubmit.disabled = true;

    const thinkingMessage = appendMessage(assistantName, thinkingLabel, 'bot');

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ question }),
      });

      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload.message ?? errorLabel);
      }

      if (thinkingMessage) {
        thinkingMessage.remove();
      }

      appendMessage(assistantName, payload.answer ?? errorLabel, 'bot', {
        interactionId: payload.interaction_id ?? null,
      });
      renderSuggestions(payload.suggestions ?? []);
    } catch (_error) {
      if (thinkingMessage) {
        thinkingMessage.remove();
      }

      appendMessage(assistantName, errorLabel, 'bot');
    } finally {
      assistantSubmit.disabled = false;
      scrollMessages();
    }
  });
}
