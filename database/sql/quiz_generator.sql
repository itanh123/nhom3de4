-- ============================================================
-- QUIZ GENERATOR – Database Schema
-- Engine: MySQL 8.0+
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS
    exam_answers, exam_questions, exam_results, exams,
    answers, questions, documents, topics, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. USERS
-- ------------------------------------------------------------
CREATE TABLE users (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    password      VARCHAR(255)        NOT NULL,
    role          ENUM('admin','teacher','student') NOT NULL DEFAULT 'student',
    avatar        VARCHAR(255)        NULL,
    is_active     TINYINT(1)          NOT NULL DEFAULT 1,
    created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 2. TOPICS  (chủ đề học tập)
-- ------------------------------------------------------------
CREATE TABLE topics (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by    BIGINT UNSIGNED     NOT NULL,
    name          VARCHAR(200)        NOT NULL,
    description   TEXT                NULL,
    is_public     TINYINT(1)          NOT NULL DEFAULT 1,
    created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_topics_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 3. DOCUMENTS  (file tài liệu đính kèm cho topic)
-- ------------------------------------------------------------
CREATE TABLE documents (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id      BIGINT UNSIGNED     NOT NULL,
    uploaded_by   BIGINT UNSIGNED     NOT NULL,
    file_name     VARCHAR(255)        NOT NULL,
    file_path     VARCHAR(500)        NOT NULL,
    file_size     INT UNSIGNED        NULL COMMENT 'bytes',
    mime_type     VARCHAR(100)        NULL,
    created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_documents_topic FOREIGN KEY (topic_id)    REFERENCES topics(id) ON DELETE CASCADE,
    CONSTRAINT fk_documents_user  FOREIGN KEY (uploaded_by) REFERENCES users(id)  ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 4. QUESTIONS
-- ------------------------------------------------------------
CREATE TABLE questions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id        BIGINT UNSIGNED     NOT NULL,
    created_by      BIGINT UNSIGNED     NOT NULL,
    -- Loại câu hỏi
    type            ENUM(
                        'single_choice',   -- trắc nghiệm 1 đáp án đúng
                        'multiple_choice', -- trắc nghiệm nhiều đáp án đúng
                        'fill_in_blank'    -- điền đáp án đúng
                    ) NOT NULL DEFAULT 'single_choice',
    difficulty      ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    content         TEXT                NOT NULL COMMENT 'Nội dung câu hỏi',
    explanation     TEXT                NULL     COMMENT 'Giải thích đáp án (AI hoặc thủ công)',
    -- AI generation metadata
    ai_generated    TINYINT(1)          NOT NULL DEFAULT 0,
    source_document BIGINT UNSIGNED     NULL     COMMENT 'Sinh từ document nào',
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_questions_topic    FOREIGN KEY (topic_id)        REFERENCES topics(id)    ON DELETE CASCADE,
    CONSTRAINT fk_questions_user     FOREIGN KEY (created_by)      REFERENCES users(id)     ON DELETE CASCADE,
    CONSTRAINT fk_questions_document FOREIGN KEY (source_document) REFERENCES documents(id) ON DELETE SET NULL,

    INDEX idx_questions_topic      (topic_id),
    INDEX idx_questions_difficulty (difficulty),
    INDEX idx_questions_type       (type)
);

-- ------------------------------------------------------------
-- 5. ANSWERS  (đáp án của câu hỏi)
--    fill_in_blank: is_correct = 1, option_text = đáp án chuẩn
-- ------------------------------------------------------------
CREATE TABLE answers (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id   BIGINT UNSIGNED     NOT NULL,
    option_text   TEXT                NOT NULL  COMMENT 'Nội dung đáp án',
    is_correct    TINYINT(1)          NOT NULL DEFAULT 0,
    display_order TINYINT UNSIGNED    NOT NULL DEFAULT 0,

    CONSTRAINT fk_answers_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_answers_question (question_id)
);

-- ------------------------------------------------------------
-- 6. EXAMS  (lượt thi / bài kiểm tra)
-- ------------------------------------------------------------
CREATE TABLE exams (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id        BIGINT UNSIGNED     NOT NULL,
    created_by      BIGINT UNSIGNED     NOT NULL  COMMENT 'Người tạo bài thi (teacher/admin)',
    title           VARCHAR(255)        NOT NULL,
    description     TEXT                NULL,
    duration_mins   SMALLINT UNSIGNED   NULL      COMMENT 'Thời gian làm bài (phút), NULL = không giới hạn',
    pass_score      TINYINT UNSIGNED    NOT NULL DEFAULT 50 COMMENT 'Điểm pass (%)',
    shuffle_q       TINYINT(1)          NOT NULL DEFAULT 1  COMMENT 'Trộn thứ tự câu hỏi',
    shuffle_a       TINYINT(1)          NOT NULL DEFAULT 1  COMMENT 'Trộn thứ tự đáp án',
    show_explain    TINYINT(1)          NOT NULL DEFAULT 0  COMMENT 'Hiển thị giải thích sau khi thi',
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_exams_topic FOREIGN KEY (topic_id)   REFERENCES topics(id) ON DELETE CASCADE,
    CONSTRAINT fk_exams_user  FOREIGN KEY (created_by) REFERENCES users(id)  ON DELETE CASCADE,

    INDEX idx_exams_topic (topic_id)
);

-- ------------------------------------------------------------
-- 7. EXAM_QUESTIONS  (câu hỏi được chọn vào từng bài thi)
-- ------------------------------------------------------------
CREATE TABLE exam_questions (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id       BIGINT UNSIGNED     NOT NULL,
    question_id   BIGINT UNSIGNED     NOT NULL,
    display_order SMALLINT UNSIGNED   NOT NULL DEFAULT 0  COMMENT 'Thứ tự sau khi shuffle',
    point         DECIMAL(5,2)        NOT NULL DEFAULT 1.00 COMMENT 'Điểm cho câu này',

    CONSTRAINT fk_eq_exam     FOREIGN KEY (exam_id)     REFERENCES exams(id)     ON DELETE CASCADE,
    CONSTRAINT fk_eq_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY uq_eq (exam_id, question_id),
    INDEX idx_eq_exam (exam_id)
);

-- ------------------------------------------------------------
-- 8. EXAM_RESULTS  (kết quả 1 lượt làm bài của student)
-- ------------------------------------------------------------
CREATE TABLE exam_results (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id         BIGINT UNSIGNED     NOT NULL,
    student_id      BIGINT UNSIGNED     NOT NULL,
    started_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    submitted_at    TIMESTAMP           NULL,
    -- Điểm số
    total_questions SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    correct_count   SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    score_pct       DECIMAL(5,2)        NOT NULL DEFAULT 0.00 COMMENT 'Điểm phần trăm',
    passed          TINYINT(1)          NOT NULL DEFAULT 0,
    -- AI nhận xét tổng hợp
    ai_summary      TEXT                NULL COMMENT 'AI tóm tắt kết quả học tập',
    ai_suggestions  TEXT                NULL COMMENT 'AI đề xuất lộ trình cải thiện',

    CONSTRAINT fk_er_exam    FOREIGN KEY (exam_id)    REFERENCES exams(id) ON DELETE CASCADE,
    CONSTRAINT fk_er_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_er_exam    (exam_id),
    INDEX idx_er_student (student_id)
);

-- ------------------------------------------------------------
-- 9. EXAM_ANSWERS  (đáp án student chọn cho từng câu)
-- ------------------------------------------------------------
CREATE TABLE exam_answers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id       BIGINT UNSIGNED     NOT NULL,
    question_id     BIGINT UNSIGNED     NOT NULL,
    -- single/multiple choice: answer_id; fill_in_blank: text_answer
    answer_id       BIGINT UNSIGNED     NULL  COMMENT 'Đáp án chọn (choice questions)',
    text_answer     TEXT                NULL  COMMENT 'Câu trả lời điền (fill_in_blank)',
    is_correct      TINYINT(1)          NOT NULL DEFAULT 0,
    -- AI giải thích tại chỗ
    ai_explanation  TEXT                NULL  COMMENT 'AI giải thích tại sao đúng/sai',

    CONSTRAINT fk_ea_result   FOREIGN KEY (result_id)   REFERENCES exam_results(id) ON DELETE CASCADE,
    CONSTRAINT fk_ea_question FOREIGN KEY (question_id) REFERENCES questions(id)    ON DELETE CASCADE,
    CONSTRAINT fk_ea_answer   FOREIGN KEY (answer_id)   REFERENCES answers(id)      ON DELETE SET NULL,

    INDEX idx_ea_result   (result_id),
    INDEX idx_ea_question (question_id)
);

-- ============================================================
-- VIEWS tiện lợi cho báo cáo & thống kê
-- ============================================================

-- Tỉ lệ đúng/sai của từng câu hỏi
CREATE OR REPLACE VIEW vw_question_stats AS
SELECT
    q.id                                        AS question_id,
    q.topic_id,
    q.content,
    q.difficulty,
    COUNT(ea.id)                                AS attempt_count,
    SUM(ea.is_correct)                          AS correct_count,
    ROUND(SUM(ea.is_correct) / COUNT(ea.id) * 100, 2) AS correct_rate_pct
FROM questions q
LEFT JOIN exam_answers ea ON ea.question_id = q.id
GROUP BY q.id, q.topic_id, q.content, q.difficulty;

-- Thống kê tổng quan mỗi bài thi
CREATE OR REPLACE VIEW vw_exam_stats AS
SELECT
    e.id                                        AS exam_id,
    e.title,
    e.topic_id,
    COUNT(er.id)                                AS total_attempts,
    ROUND(AVG(er.score_pct), 2)                 AS avg_score_pct,
    SUM(er.passed)                              AS pass_count,
    ROUND(SUM(er.passed) / COUNT(er.id) * 100, 2) AS pass_rate_pct
FROM exams e
LEFT JOIN exam_results er ON er.exam_id = e.id
GROUP BY e.id, e.title, e.topic_id;
