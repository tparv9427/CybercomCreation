const PALINDROMEBTM = document.getElementById("palindromeBtn");

const RESULT_PALINDROME = document.getElementById("resultPalindrome");

PALINDROMEBTM.addEventListener("click", palindromeCheck);

function palindromeCheck() {
    const NUMBER = parseInt(document.getElementById("p11_num1").value);

    let temp = NUMBER;
    let reverse_num = "";

    while (temp > 0) {
        reverse_num += (temp % 10);
        temp = Math.floor(temp / 10);
    }

    if (NUMBER == reverse_num) {
        RESULT_PALINDROME.innerText = "Number is Palindrome";
    } else {
        RESULT_PALINDROME.innerText = "Number is Not Palindrome";

    }
}