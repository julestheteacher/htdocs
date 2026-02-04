<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Module Complete – 8.1 Security Risks</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }

        h1, h2 {
            text-align: center;
        }

        .score-box {
            background: #eef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 1.2rem;
            text-align: center;
        }

        .pass {
            color: green;
            font-weight: bold;
        }

        .fail {
            color: red;
            font-weight: bold;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

        .actions a {
            display: inline-block;
            padding: 12px 25px;
            margin: 10px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
        }

        .actions a:hover {
            background: #004999;
        }

        .certificate-box {
            margin-top: 25px;
            padding: 20px;
            border: 2px dashed #ccc;
            text-align: center;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Module Complete</h1>
    <h2>8.1 – Privacy and Confidentiality</h2>

    <div id="score-output" class="score-box">
        Loading your results...
    </div>

    <div id="result-message"></div>

    <div id="certificate-box" class="certificate-box" style="display:none;">
        🎉 <strong>Congratulations!</strong><br>
        You passed the module.<br><br>
        <a href="certificate-8-1.pdf">Download Certificate</a>
    </div>

    <div class="actions">
        <a href="8-1-quiz.php">Retry Quiz</a>
        <a href="../index.php">Return to Dashboard</a>
    </div>

</div>

<script>
    // Pull stored results
    const score = localStorage.getItem('finalScore');
    const percentage = localStorage.getItem('finalPercentage');

    const scoreBox = document.getElementById("score-output");
    const messageBox = document.getElementById("result-message");
    const cert = document.getElementById("certificate-box");

    if (score !== null && percentage !== null) {
        scoreBox.innerHTML = `You scored <strong>${score}</strong> out of 9 
        (${percentage}%).`;

        // PASS / FAIL SYSTEM
        if (percentage >= 70) {
            messageBox.innerHTML = 
                `<p class="pass">You passed! Great job 🎉</p>`;
            cert.style.display = "block";

        } else {
            messageBox.innerHTML = `
                <p class="fail">You scored below 70%.<br>
                Review the module and try again to unlock your certificate.</p>
            `;
        }

    } else {
        scoreBox.innerHTML = "No quiz results found.";
    }
</script>

</body>
</html>