import { validateInput } from './utils';

const form = document.getElementById('register');
const inputs = document.querySelectorAll('input');
const externalIdInput = document.getElementById('external-id');
const emailInput = document.getElementById('email');
const firstNameInput = document.getElementById('first-name');
const lastNameInput = document.getElementById('last-name');
const passwordInput = document.getElementById('password');
const passwordConfirmInput = document.getElementById('password_confirmation');
const submitBtn = document.getElementById('submit');
const spinner = document.getElementById('spinner');
const registerText = document.getElementById('register-text');
const successMsg = document.getElementById('success-msg');

const passwordRegex = new RegExp(passwordInput.getAttribute('data-password-rules'));

addEventListeners();

function addEventListeners() {
    form.addEventListener('submit', submitForm);
    inputs.forEach(input => input.addEventListener('input', () => submitBtn.disabled = !isFormValid()));

    if (externalIdInput) {
        externalIdInput.addEventListener('blur', (e) => validateInput(e, validateNotEmpty));
    }

    emailInput.addEventListener('blur', (e) => validateInput(e, validateEmail));

    if (firstNameInput && lastNameInput) {
        firstNameInput.addEventListener('blur', (e) => validateInput(e, validateName));
        lastNameInput.addEventListener('blur', (e) => validateInput(e, validateName));
    }

    passwordInput.addEventListener('input', (e) => validateInput(e, validatePassword));
    passwordInput.addEventListener('blur', (e) => validateInput(e, (val) => {
        return validateNotEmpty(val) && validatePassword(val);
    }));

    passwordConfirmInput.addEventListener(
        'input',
        (e) => validateInput(e, (val) => val === passwordInput.value)
    );

    passwordConfirmInput.addEventListener(
        'blur',
        (e) => validateInput(e, (val) => val === passwordInput.value)
    );
}

function validateNotEmpty(val) {
    return val !== '';
}

function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validateName(name) {
    const regex = /^[a-zA-Z]+$/;
    return regex.test(name);
}

function validatePassword(password) { return passwordRegex.test(password); }

function isFormValid() {
    const isExternalIdValid = externalIdInput ? validateNotEmpty(externalIdInput.value) : true;
    const isFirstNameValid = firstNameInput ? validateName(firstNameInput.value) : true;
    const isLastNameValid = lastNameInput ? validateName(lastNameInput.value) : true;

    return isExternalIdValid &&
           isFirstNameValid &&
           isLastNameValid &&
           validateEmail(emailInput.value) &&
           validatePassword(passwordInput.value) &&
           passwordInput.value === passwordConfirmInput.value;
}

function collectFormData(form) {
    const formData = new FormData(form);
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('activation_code')) {
        formData.append('activation_code', urlParams.get('activation_code'));
    }
    return formData;
}

function showErrors(errors) {
    let errorsList = '';
    for (const key in errors) {
        errorsList += `<li>${errors[key]}</li>`
    }

    document.getElementById('error-summary-alert').classList.remove('hidden');
    document.getElementById('error-summary-list').innerHTML = errorsList;
}

function toggleInputs() {
    inputs.forEach(input => {
        input.disabled = !input.disabled;
        if (input.disabled) {
            input.classList.add('disabled');
        } else {
            input.classList.remove('disabled');
        }
    });
}

function toggleSpinner() {
    spinner.classList.toggle('hidden');
    registerText.classList.toggle('hidden');
    submitBtn.disabled = !submitBtn.disabled;
}

function submitForm(e) {
    e.preventDefault();

    const formData = collectFormData(form);
    toggleInputs();
    toggleSpinner();

    axios.post(form.action, formData)
    .then(response => {
        if (response.data.status === 'success') {
            form.classList.add('hidden');
            successMsg.classList.remove('hidden');
        } else {
            showErrors(response.data.errors)
            toggleInputs();
            toggleSpinner();
        }
    })
    .catch(error => {
        if (error.response) {
            showErrors(error.response.data.errors)
        }
        toggleInputs();
        toggleSpinner();
    });
}