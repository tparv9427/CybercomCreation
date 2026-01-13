const PRINT100BTNFOR = document.getElementById("print100BtnFor");
const PRINT100BTNWHILE = document.getElementById("print100BtnWhile");

const RESULT_100 = document.getElementById("result100");

PRINT100BTNFOR.addEventListener("click", print100For);
PRINT100BTNWHILE.addEventListener("click", print100While);

function print100For() {
    let output ="1 - 100 Using For<br>";
    for (let i = 1; i <= 100; i++) {
        output += i + "&nbsp;&nbsp;&nbsp;&nbsp;";
        if(i % 10 == 0) output += "<br>";
    }
    RESULT_100.innerHTML = output;
}

function print100While(){
    let output ="1 - 100 Using While<br>";
    let i = 1; 
    while (i <= 100) {
        output += i + "&nbsp;&nbsp;&nbsp;&nbsp;";
        if(i % 10 == 0) output += "<br>";
        i++;
    }
    RESULT_100.innerHTML = output;
}