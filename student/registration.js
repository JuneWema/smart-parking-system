
const studentRegForm = document.getElementById('studentRegForm');
const fullName = document.getElementById('fullName');
const email = document.getElementById('email');
const identification = document.getElementById('identification');
const telephone = document.getElementById('telephone');
const regNumber = document.getElementById('regNumber');
const carType = document.getElementById('carType');
const pass1 = document.getElementById('pass1');
const pass2 = document.getElementById('pass2');

studentRegForm.addEventListener('submit', e => {
    e.preventDefault();
    if (validateInputs()) {
        studentRegForm.submit();
    }
});

const setError = (element, message) => {
    const formGroup = element.parentElement;
    formGroup.querySelector('.error-message').innerText = message;
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
};

const setSuccess = element => {
    const formGroup = element.parentElement;
    formGroup.querySelector('.error-message').innerText = '';
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
};

const isValidEmail = email => {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
};

const isStrongPassword = pw =>
    /[A-Z]/.test(pw) && /[a-z]/.test(pw) && /\d/.test(pw) && /[^A-Za-z0-9]/.test(pw);

const validateInputs = () => {
    let valid = true;

    const fullNameValue = fullName.value.trim();
    const emailValue = email.value.trim();
    const idValue = identification.value.trim();
    const telephoneValue = telephone.value.trim();
    const regNumberValue = regNumber.value.trim();
    const carTypeValue = carType.value;
    const pass1Value = pass1.value;
    const pass2Value = pass2.value;

    if (fullNameValue === '') {
        setError(fullName, 'Full Name is required.'); valid = false;
    } else setSuccess(fullName);

    if (emailValue === '') {
        setError(email, 'Email is required.'); valid = false;
    } else if (!isValidEmail(emailValue)) {
        setError(email, 'Provide a valid email address.'); valid = false;
    } else setSuccess(email);

    if (idValue === '') {
        setError(identification, 'Student ID is required.'); valid = false;
    } else if (!/^\d{6}$/.test(idValue)) {
        setError(identification, 'Student ID must be exactly 6 digits.'); valid = false;
    } else setSuccess(identification);

    if (telephoneValue === '') {
        setError(telephone, 'Phone number is required.'); valid = false;
    } else if (!/^(\+254|0)\d{9}$/.test(telephoneValue.replace(/\s+/g, ''))) {
        setError(telephone, 'Enter a valid phone number.'); valid = false;
    } else setSuccess(telephone);

    if (regNumberValue === '') {
        setError(regNumber, 'Vehicle registration number is required.'); valid = false;
    } else setSuccess(regNumber);

    if (carTypeValue === '') {
    setError(carType, 'Please select a vehicle type.'); valid = false;
    } else setSuccess(carType);

    if (pass1Value === '') {
        setError(pass1, 'Password is required.'); valid = false;
    } else if (pass1Value.length < 8) {
        setError(pass1, 'Password must be at least 8 characters.'); valid = false;
    } else if (!isStrongPassword(pass1Value)) {
        setError(pass1, 'Include upper, lower, number and special character.'); valid = false;
    } else setSuccess(pass1);

    if (pass2Value === '') {
        setError(pass2, 'Please confirm your password.'); valid = false;
    } else if (pass1Value !== pass2Value) {
        setError(pass2, 'Passwords do not match.'); valid = false;
    } else setSuccess(pass2);

    return valid;
};
/*const studentRegForm = document.getElementById('studentRegForm');
const fullName = document.getElementById('fullName');
const email = document.getElementById('email');
const telephone = document.getElementById('telephone');
const regNumber = document.getElementById('regNumber');
const pass1 = document.getElementById('pass1');
const pass2 = document.getElementById('pass2');


studentRegForm.addEventListener('submit', e => {
    e.preventDefault();

    validateInputs();
});

const setError = (element, message) => {
    const formGroup = element.parentElement;
    const errorDisplay = formGroup.querySelector('.error');

    errorDisplay.innerText = message;
    formGroup.classList.add('error');
    formGroup.classList.remove('success');

}

const setSuccess = element => {
    const formGroup = element.parentElement;
    const errorDisplay = formGroup.querySelector('.success');

    errorDisplay.innerText = "";
    formGroup.classList.add('success');
    formGroup.classList.remove('error');

}

const isValidEmail = email => {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
}

const validateInputs = () => {
    fullNameValue = fullName.value.trim();
    emailValue = email.value.trim();
    telephoneValue = telephone.value.trim();
    regNumberValue = regNumber.value.trim();
    pass1Value = pass1.value.trim();
    pass2Value = pass2.value.trim();

    if(fullNameValue === ""){
        setError(fullName, 'Full Name is Required.');
    } else {
        setSuccess(fullName);
    }

    if(emailValue === '') {
        setError(email, 'Email is required.');
    } else if(!isValidEmail(emailValue)) {
        setError(email, 'Provide a valid email address.');
    } else {
        setSuccess(email);
    }

    if(pass1Value === '') {
        setError(pass1, 'Password is required.');
    } else if(pass1Value.length < 8 ) {
        setError(pass1, 'Password MUST be at least 8 characters.');
    } else {
        setSuccess(pass1);
    }

    if(pass2Value === '' ) {
        setError(pass2, 'Please confirm your password.');
    } else if(pass1Value !== pass2Value) {
        setError(pass2, 'Passwords do not match');
    } else {
        setSuccess(pass2);
    }

};
*/
