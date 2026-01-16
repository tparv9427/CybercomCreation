const numberInput = document.getElementById("numberInput");
const sumBtn = document.getElementById("sumBtn");
const tableBtn = document.getElementById("tableBtn");
const squareBtn = document.getElementById("squareBtn");
const output = document.getElementById("output");

function sumToN(n) {
    let sum = 0;
    for (let i = 1; i <= n; i++) {
        sum += i;
    }
    return sum;
}

function multiplicationTable(n) {
    let result = "";
    for (let i = 1; i <= 10; i++) {
        result += n + " × " + i + " = " + (n * i) + "<br>";
    }
    return result;
}

const square = (n) => n * n;

sumBtn.addEventListener("click", function () {
    const num = Number(numberInput.value);
    output.innerHTML = "Sum: " + sumToN(num);
});

tableBtn.addEventListener("click", function () {
    const num = Number(numberInput.value);
    output.innerHTML = multiplicationTable(num);
});

squareBtn.addEventListener("click", function () {
    const num = Number(numberInput.value);
    output.innerText = "Square: " + square(num);
});
