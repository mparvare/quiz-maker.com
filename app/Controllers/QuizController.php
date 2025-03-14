<?php
namespace App\Controllers;

use App\Services\GradingService;

class QuizController {
    private $gradingService;

    public function __construct(GradingService $gradingService) {
        $this->gradingService = $gradingService;
    }

    public function submitExam(Request $request, Response $response) {
        $quizId = $request->getParam('quiz_id');
        $userAnswers = $request->getParam('answers');
        $userId = $this->getCurrentUserId();

        $quiz = Quiz::findOrFail($quizId);

        // استفاده از الگوریتم پیشرفته امتیازدهی
        $result = $this->gradingService->calculateAdvancedScore(
            $quiz, 
            $userAnswers, 
            $userId
        );

        // ارسال نتیجه به کاربر
        $response->json($result, 200);
    }
}