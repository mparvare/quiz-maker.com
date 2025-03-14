<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Services\QuizService;
use App\Models\Quiz;

class QuizTest extends TestCase
{
    private $quizService;
    private $quizModel;

    protected function setUp(): void
    {
        $this->quizService = new QuizService();
        $this->quizModel = new Quiz();
    }

    public function testCreateQuiz()
    {
        $quizData = [
            'title' => 'Test Quiz',
            'description' => 'A sample quiz for testing',
            'duration' => 30,
            'passing_score' => 70
        ];

        $userId = 1; // Test user ID

        $quiz = $this->quizService->createQuiz($quizData, $userId);

        $this->assertIsArray($quiz);
        $this->assertArrayHasKey('id', $quiz);
        $this->assertEquals($quizData['title'], $quiz['title']);
    }

    public function testAddQuestionsToQuiz()
    {
        $quizId = 1; // Existing quiz ID
        $questions = [
            [
                'type' => 'multiple_choice',
                'content' => 'What is PHP?',
                'points' => 10,
                'options' => [
                    ['content' => 'Programming Language', 'is_correct' => true],
                    ['content' => 'Database', 'is_correct' => false]
                ]
            ]
        ];

        $addedQuestions = $this->quizService->addQuestionsToQuiz($quizId, $questions);

        $this->assertIsArray($addedQuestions);
        $this->assertCount(1, $addedQuestions);
        $this->assertEquals($questions[0]['content'], $addedQuestions[0]['content']);
    }
}