const SIGNEDNESSBTM = document.getElementById("signednessBtn");

const SIGNEDNESS_RESULT = document.getElementById("resultSignedness");

SIGNEDNESSBTM.addEventListener("click", checkSignedness);

function checkSignedness() {
    const NUMBER = parseFloat(document.getElementById("p6_num").value);
    if (NUMBER == 0) {
        SIGNEDNESS_RESULT.innerText = "Number is Zero";
    } else if (NUMBER > 0) {
        SIGNEDNESS_RESULT.innerText = "Number is Positive";
    } else {
        SIGNEDNESS_RESULT.innerText = "Number is Negative";
    }
}