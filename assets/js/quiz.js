// assets/js/quiz.js

class QuizManager {
    constructor() {
        // المان‌های DOM
        this.questionText = document.getElementById('question-text');
        this.questionNumber = document.getElementById('question-number');
        this.optionsContainer = document.querySelector('.question-options');
        this.prevButton = document.getElementById('prev-question');
        this.nextButton = document.getElementById('next-question');
        this.submitButton = document.getElementById('submit-quiz');
        this.timerElement = document.getElementById('timer');
        this.currentQuestionIndicator = document.getElementById('current-question');
        this.totalQuestionsIndicator = document.getElementById('total-questions');
        
        // متغیرهای آزمون
        this.currentQuestionIndex = 0;
        this.questions = [];
        this.answers = [];
        this.timeRemaining = 45 * 60; // 45 دقیقه به ثانیه
        this.timer = null;

        // اجرای رویدادها
        this.initEventListeners();
        this.loadQuizData();
    }

    initEventListeners() {
        this.prevButton.addEventListener('click', () => this.navigateQuestion(-1));
        this.nextButton.addEventListener('click', () => this.navigateQuestion(1));
        this.submitButton.addEventListener('click', () => this.submitQuiz());
        
        // گوش دادن به تغییرات گزینه‌ها
        this.optionsContainer.addEventListener('change', (e) => {
            if (e.target.type === 'radio') {
                this.answers[this.currentQuestionIndex] = e.target.value;
                this.updateQuestionStatus(this.currentQuestionIndex, 'answered');
            }
        });
    }

    async loadQuizData() {
        try {
            // بارگذاری اطلاعات آزمون از API
            const response = await fetch('/api/quiz/current');
            const quizData = await response.json();

            this.questions = quizData.questions;
            this.totalQuestionsIndicator.textContent = this.questions.length;
            this.answers = new Array(this.questions.length).fill(null);

            this.renderQuestion(0);
            this.startTimer();
            this.updateNavigationButtons();
        } catch (error) {
            console.error('خطا در بارگذاری اطلاعات آزمون:', error);
        }
    }

    renderQuestion(index) {
        const question = this.questions[index];
        
        // به‌روزرسانی متن سوال
        this.questionNumber.textContent = index + 1;
        this.questionText.textContent = question.text;
        this.currentQuestionIndicator.textContent = index + 1;

        // پاک کردن گزینه‌های قبلی
        this.optionsContainer.innerHTML = '';

        // افزودن گزینه‌های جدید
        question.options.forEach((option, optionIndex) => {
            const optionElement = document.createElement('div');
            optionElement.classList.add('form-check', 'mb-3');
            optionElement.innerHTML = `
                <input class="form-check-input" type="radio" 
                       name="answer" 
                       id="option${optionIndex + 1}" 
                       value="${optionIndex + 1}"
                       ${this.answers[index] == optionIndex + 1 ? 'checked' : ''}>
                <label class="form-check-label" for="option${optionIndex + 1}">
                    ${option}
                </label>
            `;
            this.optionsContainer.appendChild(optionElement);
        });

        this.updateNavigationButtons();
    }

    navigateQuestion(direction) {
        this.currentQuestionIndex += direction;
        this.renderQuestion(this.currentQuestionIndex);
    }

    updateNavigationButtons() {
        // غیرفعال کردن دکمه سوال قبلی در اولین سوال
        this.prevButton.disabled = this.currentQuestionIndex === 0;
        
        // غیرفعال کردن دکمه سوال بعدی در آخرین سوال
        this.nextButton.disabled = this.currentQuestionIndex === this.questions.length - 1;
    }

    updateQuestionStatus(questionIndex, status) {
        const statusElement = document.querySelector(`.question-status-item[data-question="${questionIndex + 1}"]`);
        if (statusElement) {
            statusElement.classList.add(status);
        }
    }

    startTimer() {
        this.timer = setInterval(() => {
            this.timeRemaining--;

            // تبدیل ثانیه به فرمت دقیقه:ثانیه
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            
            this.timerElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // اتمام زمان
            if (this.timeRemaining <= 0) {
                this.submitQuiz();
            }
        }, 1000);
    }

    async submitQuiz() {
        // توقف تایمر
        clearInterval(this.timer);

        try {
            // ارسال پاسخ‌ها به سرور
            const response = await fetch('/api/quiz/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    quizId: this.quizId,
                    answers: this.answers
                })
            });

            const result = await response.json();

            // نمایش مدال نتیجه
            const resultModal = new bootstrap.Modal('#quizResultModal');
            document.getElementById('final-score').textContent = result.score;
            resultModal.show();
        } catch (error) {
            console.error('خطا در ثبت آزمون:', error);
        }
    }
}

// راه‌اندازی کلاس در بارگذاری صفحه
document.addEventListener('DOMContentLoaded', () => {
    new QuizManager();
});