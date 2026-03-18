import './bootstrap';

const regionSelect = document.querySelector('[data-region-select]');
const districtSelect = document.querySelector('[data-district-select]');
const branchSelect = document.querySelector('[data-branch-select]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

const emptyOption = (select, fallback) => select?.dataset.emptyOptionLabel ?? fallback;
const selectedOption = (select) => select?.dataset.selectedValue ?? select?.value ?? '';

async function loadDistricts(regionId, selectedDistrictId = '') {
  if (!districtSelect) return;
  districtSelect.innerHTML = `<option value="">${emptyOption(districtSelect, 'Select district')}</option>`;
  if (branchSelect) {
    branchSelect.innerHTML = `<option value="">${emptyOption(branchSelect, 'Select branch')}</option>`;
  }
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
