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
