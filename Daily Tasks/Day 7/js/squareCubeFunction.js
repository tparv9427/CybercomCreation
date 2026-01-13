const SQUARECUBEBTN = document.getElementById("squareCubeBtn");

SQUARECUBEBTN.addEventListener("click", () => {
    const INPUTBOX1 = document.getElementById("input1");
    
    let num = parseInt(INPUTBOX1.value);
    if(isNaN(num)) {
        document.getElementById("outputLabel").innerText = "Enter a value first";
    } else {
        document.getElementById("outputLabel").innerText = "Square = " + squareCube(num).square + " and " + "Cube is " + squareCube(num).cube;
    }
})

function squareCube(num) {
    return {square: num*num , cube: num*num*num};
}