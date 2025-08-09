import { debounce } from "./tools.js";

const homeButton = document.querySelector('.js-home');
homeButton.addEventListener('click', debounce(() => {
  console.log('click');
}, 2000))