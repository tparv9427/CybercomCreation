const NUMBER1 = parseFloat(document.getElementById("p4_num1").value);
const NUMBER2 = parseFloat(document.getElementById("p4_num2").value);
const NUMBER3 = parseFloat(document.getElementById("p4_num3").value);

const LARGESTBTN = document.getElementById("findLargest");

const LARGEST_RESULT = document.getElementById("resultLargest");

LARGESTBTN.addEventListener("click", findLargestNum);

function findLargestNum() {
    const NUMBER1 = parseFloat(document.getElementById("p4_num1").value);
    const NUMBER2 = parseFloat(document.getElementById("p4_num2").value);
    const NUMBER3 = parseFloat(document.getElementById("p4_num3").value);

    let Largest;
    if (NUMBER1 > NUMBER2) {
        if (NUMBER1 > NUMBER3) {
            Largest = NUMBER1;
        } else {
            Largest = NUMBER3;
        }
    } else {
        if (NUMBER2 > NUMBER3) {
            Largest = NUMBER2;
        } else {
            Largest = NUMBER3;
        }
    }
    LARGEST_RESULT.innerText = Largest + " is the Largest Number";
}