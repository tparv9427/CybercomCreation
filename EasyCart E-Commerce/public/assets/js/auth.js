const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

const mobileSignUpBtn = document.getElementById('mobile-signUp');
const mobileSignInBtn = document.getElementById('mobile-signIn');

if (signUpButton && signInButton && container) {
    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
}

// Mobile Toggles (Manual visibility toggle for small screens)
if (mobileSignUpBtn && container) {
    mobileSignUpBtn.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });
}

if (mobileSignInBtn && container) {
    mobileSignInBtn.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
}

