CREATE DATABASE quiz_maker;

USE quiz_maker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('student', 'teacher', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    total_score DECIMAL(10,2),
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    type ENUM('multiple_choice', 'true_false', 'descriptive'),
    content TEXT NOT NULL,
    options JSON,
    correct_answer TEXT,
    score DECIMAL(5,2),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);