<?php
namespace App\Services;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\UserQuizAttempt;

class GradingService {
    // الگوریتم محاسبه امتیاز پیشرفته
    public function calculateAdvancedScore(Quiz $quiz, array $userAnswers, $userId) {
        $totalQuestions = count($userAnswers);
        $correctAnswers = 0;
        $partiallyCorrectAnswers = 0;
        $totalScore = 0;
        $difficultQuestionsAttempted = 0;

        foreach ($userAnswers as $questionId => $userAnswer) {
            $question = Question::findOrFail($questionId);
            
            // محاسبه امتیاز با الگوریتم پیچیده
            $scoreResult = $this->evaluateAnswer($question, $userAnswer);
            
            $totalScore += $scoreResult['score'];
            
            if ($scoreResult['is_correct']) {
                $correctAnswers++;
            }
            
            if ($scoreResult['is_partially_correct']) {
                $partiallyCorrectAnswers++;
            }
            
            // شناسایی سوالات سخت
            if ($question->difficulty_level > 7) {
                $difficultQuestionsAttempted++;
            }
        }

        // محاسبه ضریب پیشرفت
        $progressFactor = $this->calculateProgressFactor(
            $correctAnswers, 
            $partiallyCorrectAnswers, 
            $totalQuestions,
            $difficultQuestionsAttempted
        );

        // ذخیره نتیجه آزمون
        $attempt = UserQuizAttempt::create([
            'user_id' => $userId,
            'quiz_id' => $quiz->id,
            'total_score' => $totalScore,
            'progress_factor' => $progressFactor,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions
        ]);

        return [
            'total_score' => $totalScore,
            'progress_factor' => $progressFactor,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'attempt_id' => $attempt->id
        ];
    }

    // ارزیابی پاسخ با الگوریتم پیشرفته
    private function evaluateAnswer(Question $question, $userAnswer) {
        $score = 0;
        $isCorrect = false;
        $isPartiallyCorrect = false;

        switch ($question->type) {
            case 'multiple_choice':
                $score = $this->evaluateMultipleChoice($question, $userAnswer);
                break;
            
            case 'descriptive':
                $score = $this->evaluateDescriptive($question, $userAnswer);
                break;
            
            case 'matching':
                $score = $this->evaluateMatching($question, $userAnswer);
                break;
        }

        // تعیین وضعیت پاسخ
        $isCorrect = $score == $question->max_score;
        $isPartiallyCorrect = $score > 0 && $score < $question->max_score;

        return [
            'score' => $score,
            'is_correct' => $isCorrect,
            'is_partially_correct' => $isPartiallyCorrect
        ];
    }

    // محاسبه ضریب پیشرفت
    private function calculateProgressFactor(
        $correctAnswers, 
        $partiallyCorrectAnswers, 
        $totalQuestions,
        $difficultQuestionsAttempted
    ) {
        // فرمول پیچیده محاسبه ضریب پیشرفت
        $correctRatio = $correctAnswers / $totalQuestions;
        $partialCorrectRatio = $partiallyCorrectAnswers / $totalQuestions;
        $difficultQuestionFactor = $difficultQuestionsAttempted / $totalQuestions;

        $progressFactor = (
            ($correctRatio * 1) + 
            ($partialCorrectRatio * 0.5) + 
            ($difficultQuestionFactor * 0.3)
        );

        return min(max($progressFactor, 0), 1);
    }

    // ارزیابی سوالات چندگزینه‌ای
    private function evaluateMultipleChoice(Question $question, $userAnswer) {
        $correctAnswers = $question->correct_answers;
        $userSelectedAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];

        // محاسبه امتیاز با در نظر گرفتن پاسخ‌های جزئی
        $correctCount = count(array_intersect($correctAnswers, $userSelectedAnswers));
        $totalCorrectAnswers = count($correctAnswers);

        // کسر امتیاز برای پاسخ‌های اشتباه
        $incorrectCount = count(array_diff($userSelectedAnswers, $correctAnswers));
        
        $score = ($correctCount / $totalCorrectAnswers) * $question->max_score;
        $score -= ($incorrectCount * 0.25); // کسر امتیاز برای هر پاسخ اشتباه

        return max($score, 0);
    }

    // سایر متدهای ارزیابی...
}