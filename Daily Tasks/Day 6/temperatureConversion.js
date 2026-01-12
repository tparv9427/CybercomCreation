const CELSIUS = document.getElementById("temperature");
const CONVERTBTN = document.getElementById("temperatureConversion");
const TEMPERATURE_RESULT = document.getElementById("resultTemp");

CONVERTBTN.addEventListener("click",convertTemperature);

function convertTemperature(){
    const temp = parseFloat(CELSIUS.value) * 1.8 +32;

    TEMPERATURE_RESULT.innerText = temp +" `F";
}