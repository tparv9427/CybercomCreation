
const SWAPBTN = document.getElementById("swapNumbersBtn");

const SWAP_RESULT = document.getElementById("resultSwap");

SWAPBTN.addEventListener("click", swapNumbers);

function swapNumbers() {
    let Number1 = parseFloat(document.getElementById("p5_num1").value);
    let Number2 = parseFloat(document.getElementById("p5_num2").value);

    [Number1, Number2] = [Number2, Number1];
    SWAP_RESULT.innerText = "After swapping Number 1 = " + Number1 + " and Number 2 = " + Number2;
}