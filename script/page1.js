import { checkDatesInputs } from "../data/inputs.js";
import { debounce } from "./utils/tools.js";

const fromInput = document.querySelector('.js-from-input');
const toInput = document.querySelector('.js-to-input');

fromInput.addEventListener('input', debounce(async() => {
  console.log('funtion running');
  await renderDropdown(fromInput.value);
  fromdropdown('flying-from');
}, 700))

toInput.addEventListener('input', debounce(async() => {
  console.log('funtion running');
  await renderDropdown(toInput.value);
  fromdropdown('flying-to');
}, 700))



const flightsButton = document.querySelector('.js-flights-button');
flightsButton.addEventListener('click', () => {
  console.log('click');
  if (filledOut() && checkDatesInputs ()) {
    console.log('true')
    const departing = document.querySelector('.js-departing-input').value;
    const returning = document.querySelector('.js-returning-input').value;
    const from = document.querySelector('.js-from-input').value;
    const to = document.querySelector('.js-to-input').value;
    const adult = document.querySelector('.js-adults-input').value;
    const children = document.querySelector('.js-children-input').value;
    const travelClass = document.querySelector('.js-class-input').value;
    window.location.href = `page2?from=${from}to=${to}departing=${departing}returning=${returning}adult=${adult}children=${children}travelClass=${travelClass}.html`
  }
})



function fromdropdown (jsName) {    

  const container = document.querySelector(`.${jsName}`)  
  const dropdown = container.querySelector('.js-dropdown-menu');
  const input = container.querySelector('input');
  
  const items = container.querySelectorAll('.items')
  let show = false;

  if (input.value.length > 2) {
    const filter = input.value.toLowerCase();
    items.forEach((item) => {
      const context = item.textContent.toLowerCase();
      const matches = context.includes(filter);
      if (matches) {
        item.style.display = 'grid';
        show = true;
      } else {
        item.style.display = 'none';
      }
    })
    dropdown.classList.add('open');
  }    

  items.forEach((item) => {
    item.addEventListener('click', () => {
      const iataCode = item.dataset.iataCode;
      input.value = iataCode;
      dropdown.classList.remove('open');
    })
  })

  if (!show)  {
    dropdown.classList.remove('open');
    }

  window.addEventListener('click', (e) => {
  if (!dropdown.contains(e.target) && e.target !== input) 
    dropdown.classList.remove('open')
    });

}

class FlightParam {
  #from;
  #to;
  #departing;
  #returning;
  #adult;
  #children;
  #travelClass;

  constructor(from, to, departing, returning, adult, children, travelClass) {
    this.#from = from;
    this.#to = to;
    this.#departing = departing;
    this.#returning = returning;
    this.#adult = adult;
    this.#children = children;
    this.#travelClass = travelClass;
  }
}

async function renderDropdown(query) {

  const data = await sendSearch(query)
  let airportsHTML = '';

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
  })
  const dropdowns = document.querySelectorAll('.js-dropdown-menu');
  dropdowns.forEach((dropdown) => {
    dropdown.innerHTML = airportsHTML;
  })
}

function filledOut() {
  const inputs = document.querySelectorAll('input');
  const fillOutText = document.querySelector('.filled-out')
  let allFilled = true;

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      allFilled = false;
    }
  }) 

  if (!allFilled) {
    fillOutText.innerHTML = `All field must be filled out`;
    fillOutText.classList.remove('filled-out-none');
  }
  return allFilled;
}

async function sendSearch(query) {
  try {
    const response = await fetch(`http://localhost/Projects/php/Airline%20Reservation%20System%20php%20backend/backend/airport.php?search=${encodeURIComponent(query)}`);
    const data = await response.json();
    return data; 
  } catch (error) {
    console.error('Fetch error:', error);
    return []; 
  }
}



