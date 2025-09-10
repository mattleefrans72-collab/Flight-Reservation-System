const openBtns = document.querySelectorAll('.open-details-js');
const closeBtns = document.querySelectorAll('.close-details-js');
const overlays = document.querySelectorAll('.overlay-js');
const modals = document.querySelectorAll('.side-modal-js');

openBtns.forEach((btn) => {
  btn.onclick = () => {
    const modalId = btn.dataset.modal; 

    const modal = document.querySelector(`.side-modal-js[data-modal="${modalId}"]`);
    const overlay = document.querySelector(`.overlay-js[data-modal="${modalId}"]`);

    modal.classList.remove('hidden');
    overlay.classList.remove('hidden');
    modal.classList.add('active');
  };
});

// Close logic
closeBtns.forEach((btn) => {
  btn.onclick = () => {
    console.log('close');
    const modalId = btn.dataset.modal;

    const modal = document.querySelector(`.side-modal-js[data-modal="${modalId}"]`);
    const overlay = document.querySelector(`.overlay-js[data-modal="${modalId}"]`);

    modal.classList.remove('active');
    setTimeout(() => {
      modal.classList.add('hidden');
      overlay.classList.add('hidden');
    }, 300);
  };
});

// Overlay click = close modal
overlays.forEach((overlay) => {
  overlay.onclick = () => {
    const modalId = overlay.dataset.modal;
    const modal = document.querySelector(`.side-modal-js[data-modal="${modalId}"]`);

    modal.classList.remove('active');
    setTimeout(() => {
      modal.classList.add('hidden');
      overlay.classList.add('hidden');
    }, 300);
  };
});
