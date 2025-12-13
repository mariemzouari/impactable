document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');

    form.setAttribute('novalidate', ''); // Disable browser's default HTML5 validation

    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Reset previous error messages
        const errorMessages = form.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
        const invalidInputs = form.querySelectorAll('.input.invalid');
        invalidInputs.forEach(input => input.classList.remove('invalid'));







        if (!isValid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });

    function displayError(inputElement, message) {
        inputElement.classList.add('invalid');
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('error-message');
        errorMessage.style.color = 'red';
        errorMessage.style.fontSize = '0.8em';
        errorMessage.style.marginTop = '5px';
        errorMessage.textContent = message;
        inputElement.parentNode.insertBefore(errorMessage, inputElement.nextSibling);
    }
});