import { debounce } from "./utils/tools.js";

const fromInput = document.querySelector('.js-from-input');
const toInput = document.querySelector('.js-to-input');

fromInput.addEventListener('input', debounce(async () => {
  if (fromInput.value.length >= 3) {
    await renderDropdown(fromInput.value);
    dropdownAction('flying-from');
  }
  }, 700));

toInput.addEventListener('input', debounce(async() => {
  if (fromInput.value.length >= 3) {
    await renderDropdown(toInput.value);
    dropdownAction('flying-to');
  }
  }, 700))



function dropdownAction(jsContainer) {

  const container = document.querySelector(`.${jsContainer}`)  
  const dropdown = container.querySelector('.js-dropdown-menu');
  const input = container.querySelector('input');
  
  const items = container.querySelectorAll('.items')
  if (items.length > 0) {
    dropdown.classList.add('open');
    items.forEach((item) => {
      item.addEventListener('click', () => {
        const iataCode = item.dataset.iataCode;
        input.value = iataCode;
        dropdown.classList.remove('open');
      })
    })
  } else {
    dropdown.classList.remove('open');
  }

  window.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && e.target !== input) {
      dropdown.classList.remove('open')
    }  
  });
}

async function renderDropdown(query) {

  let airportsHTML = '';

  const res = await fetch('/search?search=' + encodeURIComponent(query))
  const data = await res.json();

  data.forEach((airport) => {
  airportsHTML += `
    <div class="items js-from-items" data-iata-code="${airport.iata_code}">
      <div class="iata-and-name">
        <span class="iata">${airport.iata_code}</span>
        <span class="name">${airport.name}</span>
      </div>
      <div class="location">
        <span class="municipality">${airport.municipality}</span>
        <span class="region">, ${airport.region}</span>
        <span class="country">, ${airport.country}</span>
      </div>           
    </div>
    `
  });
  const dropdowns = document.querySelectorAll('.js-dropdown-menu');
  dropdowns.forEach((dropdown) => {
    dropdown.innerHTML = airportsHTML;
  }); 
}


