const visitorLogin = document.getElementById('visitorLogin');
const visitorId = document.getElementById('visitorId');
const password = document.getElementById('password');

visitorLogin.addEventListener('submit', e => {
    e.preventDefault();
    if (validateLogin()) {
        visitorLogin.submit();
    }
});

const setError = (element, message) => {
    const formGroup = element.parentElement;
    formGroup.querySelector('.error-text').innerText = message;
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
};

const setSuccess = element => {
    const formGroup = element.parentElement;
    formGroup.querySelector('.error-text').innerText = '';
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
};

const validateLogin = () => {
    let valid = true;

    const idValue = visitorId.value.trim();
    const passValue = password.value;

    // Student ID (exactly 6 digits)
    if (idValue === '') {
        setError(visitorId, 'National ID is required.'); valid = false;
    } else if (!/^\d{9}$/.test(idValue)) {
        setError(visitorId, 'National ID must be exactly 9 digits.'); valid = false;
    } else setSuccess(visitorId);

    // Password (only check it's not empty; strength rules are for registration)
    if (passValue === '') {
        setError(password, 'Password is required.'); valid = false;
    } else setSuccess(password);

    return valid;
};

// Show the server's error if login.php redirected back with ?error=1
if (new URLSearchParams(window.location.search).get('error') === '1') {
    document.getElementById('loginError').innerText = 'Invalid National ID or password.';
}