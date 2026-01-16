const nameInput = document.getElementById("nameInput");
const greetBtn = document.getElementById("greetBtn");
const themeBtn = document.getElementById("themeBtn");
const message = document.getElementById("message");

greetBtn.addEventListener("click", function () {
    const name = nameInput.value.trim();

    if (name === "") {
        message.innerText = "Please enter your name";
        message.style.color = "red";
    } else {
        message.innerText = "Hello " + name;
        message.style.color = "green";
    }
});

themeBtn.addEventListener("click", function () {
    document.body.classList.toggle("dark");
});
