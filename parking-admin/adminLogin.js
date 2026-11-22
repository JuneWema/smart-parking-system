const adminLogin = document.getElementById('adminLogin');
const workId = document.getElementById('workId');
const password = document.getElementById('password');

adminLogin.addEventListener('submit', e => {
    e.preventDefault();
    if (validateLogin()) {
        adminLogin.submit();
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

    const idValue = workId.value.trim();
    const passValue = password.value;

    // Student ID (exactly 6 digits)
    if (idValue === '') {
        setError(workId, 'Work ID is required.'); valid = false;
    } else if (!/^\d{6}$/.test(idValue)) {
        setError(workId, 'Work ID must be exactly 6 digits.'); valid = false;
    } else setSuccess(workId);

    // Password (only check it's not empty; strength rules are for registration)
    if (passValue === '') {
        setError(password, 'Password is required.'); valid = false;
    } else setSuccess(password);

    return valid;
};

// Show the server's error if login.php redirected back with ?error=1
if (new URLSearchParams(window.location.search).get('error') === '1') {
    document.getElementById('loginError').innerText = 'Invalid work ID or password.';
}