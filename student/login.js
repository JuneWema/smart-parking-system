

const studentLogin = document.getElementById('studentLogin');
const studentId = document.getElementById('studentId');
const password = document.getElementById('password');

studentLogin.addEventListener('submit', e => {
    e.preventDefault();
    if (validateLogin()) {
        studentLogin.submit();
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

    const idValue = studentId.value.trim();
    const passValue = password.value;

    // Student ID (exactly 6 digits)
    if (idValue === '') {
        setError(studentId, 'Student ID is required.'); valid = false;
    } else if (!/^\d{6}$/.test(idValue)) {
        setError(studentId, 'Student ID must be exactly 6 digits.'); valid = false;
    } else setSuccess(studentId);

    // Password (only check it's not empty; strength rules are for registration)
    if (passValue === '') {
        setError(password, 'Password is required.'); valid = false;
    } else setSuccess(password);

    return valid;
};

// Show the server's error if login.php redirected back with ?error=1
if (new URLSearchParams(window.location.search).get('error') === '1') {
    document.getElementById('loginError').innerText = 'Invalid student ID or password.';
}