import './bootstrap';

const regionSelect = document.querySelector('[data-region-select]');
const districtSelect = document.querySelector('[data-district-select]');
const branchSelect = document.querySelector('[data-branch-select]');

async function loadDistricts(regionId) {
  if (!districtSelect) return;
  districtSelect.innerHTML = '<option value="">Select district</option>';
  if (branchSelect) {
    branchSelect.innerHTML = '<option value="">Select branch</option>';
  }
  if (!regionId) return;

  const response = await fetch(`/api/districts?region_id=${regionId}`);
  const districts = await response.json();

  districts.forEach((district) => {
    const option = document.createElement('option');
    option.value = district.id;
    option.textContent = district.name;
    districtSelect.appendChild(option);
  });
}

async function loadBranches(districtId) {
  if (!branchSelect) return;
  branchSelect.innerHTML = '<option value="">Select branch</option>';
  if (!districtId) return;

  const response = await fetch(`/api/branches?district_id=${districtId}`);
  const branches = await response.json();

  branches.forEach((branch) => {
    const option = document.createElement('option');
    option.value = branch.id;
    option.textContent = branch.name;
    branchSelect.appendChild(option);
  });
}

if (regionSelect) {
  regionSelect.addEventListener('change', (event) => loadDistricts(event.target.value));
}

if (districtSelect) {
  districtSelect.addEventListener('change', (event) => loadBranches(event.target.value));
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
