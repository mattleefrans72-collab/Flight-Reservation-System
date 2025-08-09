import dayjs from "https://unpkg.com/supersimpledev@8.5.0/dayjs/esm/index.js";

export function checkDatesInputs() {
  const departing = dayjs(document.querySelector('.js-departing-input').value);
  const returning = dayjs(document.querySelector('.js-returning-input').value);
  const today = dayjs().startOf('day');
  console.log (departing)
  console.log (today)

  if ((departing.isAfter(today) || departing.isSame(today)) && departing.isBefore(returning)) {
    return true;
  } else {
    const filledOut = document.querySelector('.filled-out');
    filledOut.innerHTML = `Date is incorrect`;
    filledOut.classList.remove('filled-out-none');
    return false;
  }
}