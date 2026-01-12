const BUTTON = document.getElementById("oddEvenCheck");
const NUMBERINPUT = document.getElementById("oddEvenInput");
const ODD_EVEN_RESULT = document.getElementById("oddEvenResult");


BUTTON.addEventListener("click", oddEvenCheck);

function oddEvenCheck() {
    const number = parseInt(NUMBERINPUT.value);
    if (isNaN(number)) {
        ODD_EVEN_RESULT.innerText = "Please enter a valid number.";
    } else if (number % 2 === 0) {
        ODD_EVEN_RESULT.innerText = number + " is an even number.";
    } else {
        ODD_EVEN_RESULT.innerText = number + " is an odd number.";
    }
}