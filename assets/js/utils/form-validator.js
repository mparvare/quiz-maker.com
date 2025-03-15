class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.initValidation();
    }

    initValidation() {
        const inputs = this.form.querySelectorAll('input');
        
        inputs.forEach(input => {
            input.addEventListener('input', () => this.validateInput(input));
            input.addEventListener('blur', () => this.validateInput(input));
        });

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validateForm()) {
                this.form.dispatchEvent(new Event('valid-submit'));
            }
        });
    }

    validateInput(input) {
        const errorElement = document.getElementById(`${input.id}-error`);
        
        // Reset previous errors
        errorElement.textContent = '';
        input.classList.remove('input-error');

        // Validate required
        if (input.required && !input.value.trim()) {
            this.showError(input, 'این فیلد اجباری است');
            return false;
        }

        // Email validation
        if (input.type === 'email') {
            const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
            if (!emailRegex.test(input.value)) {
                this.showError(input, 'ایمیل نامعتبر است');
                return false;
            }
        }

        // Password strength
        if (input.type === 'password') {
            if (input.minLength && input.value.length < input.minLength) {
                this.showError(input, `حداقل ${input.minLength} کاراکتر نیاز است`);
                return false;
            }

            // Password confirmation
            if (input.id === 'register-password-confirm') {
                const passwordInput = document.getElementById('register-password');
                if (input.value !== passwordInput.value) {
                    this.showError(input, 'رمز عبور مطابقت ندارد');
                    return false;
                }
            }
        }

        return true;
    }

    validateForm() {
        const inputs = this.form.querySelectorAll('input');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateInput(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showError(input, message) {
        const errorElement = document.getElementById(`${input.id}-error`);
        errorElement.textContent = message;
        input.classList.add('input-error');
    }
}