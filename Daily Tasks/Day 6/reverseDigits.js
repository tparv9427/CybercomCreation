const REVERSEBTN = document.getElementById("reverseDigitsBtn");

const REVERSE_RESULT = document.getElementById("resultReverseDigits");

REVERSEBTN.addEventListener("click", reverseDigits);

function reverseDigits() {
    const NUMBER = parseInt(document.getElementById("p10_num1").value);
    let temp = NUMBER;
    let reverse_num = "";

    while (temp > 0) {
        reverse_num += (temp % 10);
        temp = Math.floor(temp / 10);
    }
    REVERSE_RESULT.innerText = reverse_num;
}