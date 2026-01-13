const FACTORIALBTN = document.getElementById("factorialBtn");

FACTORIALBTN.addEventListener("click", () => {
    const INPUTBOX1 = document.getElementById("input1");
    
    let num = parseInt(INPUTBOX1.value);
    if(isNaN(num)) {
        document.getElementById("outputLabel").innerText = "Enter a value first";
    } else {
        let fact = 1;
        while (num > 0) {
            fact *= num--;
        }
            document.getElementById("outputLabel").innerText = "Factorial of " + parseInt(INPUTBOX1.value) + " = " + fact;

    }
})