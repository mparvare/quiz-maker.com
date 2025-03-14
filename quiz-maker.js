quiz-maker/
├── app/
│   ├── Controllers/
│   │   ├── UserController.php
│   │   ├── QuizController.php
│   │   └── QuestionController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Quiz.php
│   │   ├── Question.php
│   │   └── QuizAttempt.php
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── QuizRepository.php
│   │   └── QuestionRepository.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── GradingService.php
│   │   └── EmailService.php
│   └── Middleware/
│       └── AuthMiddleware.php
├── config/
│   ├── database.php
│   ├── app.php
│   └── routes.php
├── core/
│   ├── Database.php
│   ├── Request.php
│   ├── Response.php
│   └── Router.php
├── public/
│   └── index.php
├── routes/
│   └── api.php
├── storage/
│   ├── logs/
│   └── uploads/
├── tests/
├── vendor/
├── .env
├── .htaccess
├── composer.json
└── README.md