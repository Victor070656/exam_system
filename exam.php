<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam: <?php echo htmlspecialchars($exam['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-card {
            margin-bottom: 2rem;
        }

        .timer {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .warning {
            color: #dc3545;
        }

        #autoSaveStatus {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><?php echo htmlspecialchars($exam['title']); ?></h3>
                        <div>
                            <span class="badge bg-primary timer" id="timer"></span>
                            <small id="autoSaveStatus" class="ms-2 text-muted"></small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="examForm" method="POST" action="submit_exam.php">
                            <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div id="questions">
                                <?php foreach ($exam['questions'] as $index => $question): ?>
                                    <div class="question-card card">
                                        <div class="card-body">
                                            <h5 class="card-title">Question <?php echo $index + 1; ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($question['question_text']); ?></p>

                                            <?php
                                            $options = json_decode($question['options'], true);
                                            foreach ($options as $key => $option):
                                            ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="answers[<?php echo $question['id']; ?>]"
                                                        id="q<?php echo $question['id']; ?>_<?php echo $key; ?>"
                                                        value="<?php echo $key; ?>"
                                                        data-question-id="<?php echo $question['id']; ?>">
                                                    <label class="form-check-label"
                                                        for="q<?php echo $question['id']; ?>_<?php echo $key; ?>">
                                                        <?php echo htmlspecialchars($option); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="button" class="btn btn-secondary" id="prevQuestion">Previous</button>
                                <div>
                                    <span id="questionProgress"></span>
                                </div>
                                <button type="button" class="btn btn-primary" id="nextQuestion">Next</button>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger" id="submitExam">Submit Exam</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Question Navigator</h5>
                    </div>
                    <div class="card-body">
                        <div id="questionNav" class="d-flex flex-wrap gap-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="submitConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to submit your exam?</p>
                    <div id="unansweredWarning" class="alert alert-warning d-none">
                        You have <span id="unansweredCount"></span> unanswered questions.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Exam</button>
                    <button type="button" class="btn btn-danger" id="confirmSubmit">Submit Exam</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const remainingTime = <?php echo $remainingTime; ?>;
            const totalQuestions = <?php echo count($exam['questions']); ?>;
            let currentQuestion = 0;
            let answers = new Map();
            let autoSaveTimeout;

            const modal = new bootstrap.Modal(document.getElementById('submitConfirmModal'));

            // Initialize timer
            function updateTimer(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                const timerElement = document.getElementById('timer');
                timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

                if (seconds <= 300) { // 5 minutes warning
                    timerElement.classList.add('warning');
                }

                if (seconds <= 0) {
                    autoSubmitExam();
                }
            }

            let timeLeft = remainingTime;
            updateTimer(timeLeft);
            const timerInterval = setInterval(() => {
                timeLeft--;
                updateTimer(timeLeft);
            }, 1000);

            // Auto-save functionality
            function autoSave() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    const formData = new FormData(document.getElementById('examForm'));
                    fetch('auto_save.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            const statusElement = document.getElementById('autoSaveStatus');
                            statusElement.textContent = 'Saved';
                            setTimeout(() => {
                                statusElement.textContent = '';
                            }, 2000);
                        });
                }, 1000);
            }

            // Question navigation
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', () => {
                    const questionId = input.dataset.questionId;
                    answers.set(questionId, input.value);
                    updateNavigator();
                    autoSave();
                });
            });

            function updateNavigator() {
                const nav = document.getElementById('questionNav');
                nav.innerHTML = '';

                for (let i = 0; i < totalQuestions; i++) {
                    const btn = document.createElement('button');
                    btn.className = `btn btn-sm ${i === currentQuestion ? 'btn-primary' : 'btn-outline-secondary'}`;
                    btn.textContent = i + 1;
                    btn.onclick = () => showQuestion(i);
                    nav.appendChild(btn);
                }

                updateProgress();
            }

            function updateProgress() {
                const answered = answers.size;
                document.getElementById('questionProgress').textContent =
                    `Question ${currentQuestion + 1} of ${totalQuestions} (${answered} answered)`;
            }

            function showQuestion(index) {
                if (index >= 0 && index < totalQuestions) {
                    document.querySelectorAll('.question-card').forEach((card, i) => {
                        card.style.display = i === index ? 'block' : 'none';
                    });
                    currentQuestion = index;
                    updateNavigator();
                }
            }

            // Navigation buttons
            document.getElementById('prevQuestion').addEventListener('click', () => {
                showQuestion(currentQuestion - 1);
            });

            document.getElementById('nextQuestion').addEventListener('click', () => {
                showQuestion(currentQuestion + 1);
            });

            // Submit handling
            document.getElementById('submitExam').addEventListener('click', (e) => {
                e.preventDefault();
                const unanswered = totalQuestions - answers.size;
                const unansweredWarning = document.getElementById('unansweredWarning');
                const unansweredCount = document.getElementById('unansweredCount');

                if (unanswered > 0) {
                    unansweredWarning.classList.remove('d-none');
                    unansweredCount.textContent = unanswered;
                } else {
                    unansweredWarning.classList.add('d-none');
                }

                modal.show();
            });

            document.getElementById('confirmSubmit').addEventListener('click', () => {
                document.getElementById('examForm').submit();
            });

            // Initialize first question
            showQuestion(0);
            updateNavigator();

            // Prevent accidental navigation
            window.addEventListener('beforeunload', (e) => {
                e.preventDefault();
                e.returnValue = '';
            });

            // Auto-submit when time expires
            function autoSubmitExam() {
                clearInterval(timerInterval);
                document.getElementById('examForm').submit();
            }
        });
    </script>
</body>

</html>