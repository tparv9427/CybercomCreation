const ADDBTN = document.getElementById("sumDigitsBtn");
const ADD_RESULT = document.getElementById("resultSumDigits");

ADDBTN.addEventListener("click", sumDigits);

function sumDigits() {
    const NUMBER = parseInt(document.getElementById("p9_num1").value);
    let temp = NUMBER;
    let sum = 0;

    while (temp > 0) {
        sum += (temp % 10);
        temp = Math.floor(temp / 10);
    }
    ADD_RESULT.innerText = sum;
}