const MULTIPLICATIONBTN = document.getElementById("printTableBtn");

const MULTIPLICATION_RESULT = document.getElementById("resultMultiplication");

MULTIPLICATIONBTN.addEventListener("click", printMultiplicationTable);

function printMultiplicationTable() {

    MULTIPLICATION_RESULT.innerHTML = "";
    
    const NUMBER = parseInt(document.getElementById("p7_num1").value);
    for (let i = 1; i <= 10; i++) {
        MULTIPLICATION_RESULT.innerHTML +=
            NUMBER + " x " + i + " = " + (NUMBER * i) + "<br>";
    }
}