

const ODDEVENBTN = document.getElementById("oddEvenBtn");
ODDEVENBTN.addEventListener("click", () => {
    const INPUTBOX1 = document.getElementById("input1");


    let num = parseInt(INPUTBOX1.value);
    if (isNaN(num)) {
        document.getElementById("outputLabel").innerText = "Enter a value first";
    } else if (num % 2 == 0) {
        document.getElementById("outputLabel").innerText = num + " is Even";
    } else {
        document.getElementById("outputLabel").innerText = num + " is Odd";
    }
})