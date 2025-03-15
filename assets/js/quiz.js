document.addEventListener('DOMContentLoaded', () => {
    const quizzesList = document.getElementById('quizzes-list');

    async function loadQuizzes() {
        try {
            const response = await fetch('/api/v1/quizzes');
            const quizzes = await response.json();

            quizzesList.innerHTML = quizzes.map(quiz => `
                <div class="quiz-card">
                    <h3>${quiz.title}</h3>
                    <p>${quiz.description}</p>
                    <a href="/quiz/${quiz.id}" class="btn btn-primary">شروع آزمون</a>
                </div>
            `).join('');
        } catch (error) {
            console.error('Failed to load quizzes:', error);
        }
    }

    loadQuizzes();
});