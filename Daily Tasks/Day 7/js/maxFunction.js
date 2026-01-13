
const MAXBTN = document.getElementById("maxBtn");

MAXBTN.addEventListener("click", () => {
    const INPUTBOX1 = document.getElementById("input1");
    const INPUTBOX2 = document.getElementById("input2");


    let num1 = parseInt(INPUTBOX1.value);
    let num2 = parseInt(INPUTBOX2.value);

    if(isNaN(num1) || isNaN(num2)){
        document.getElementById("outputLabel").innerText = "Enter both numbers first";
    }else if (num1 > num2) {
        document.getElementById("outputLabel").innerText = num1 + " is Max";
    } else if (num1 < num2) {
        document.getElementById("outputLabel").innerText = num2 + " is Max";
    } else {
        document.getElementById("outputLabel").innerText = "Both are equal";
    }
})