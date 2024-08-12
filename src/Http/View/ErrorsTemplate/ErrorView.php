<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #f00;
        }

        .details {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .details p {
            margin: 5px 0;
        }

        .details pre {
            white-space: pre-wrap;
            background-color: #fff;
            border: none;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Error <?= htmlspecialchars($statusCode ?? '') ?></h1>
    <p><?= htmlspecialchars($reasonPhrase ?? '') ?></p>
    <p><?= htmlspecialchars($xdebugTag ?? '') ?></p>
    <?php if ($environmentMode === 'development'): ?>
        <div class="details">
            <p><strong>File:</strong> <?= htmlspecialchars($file ?? '') ?></p>
            <p><strong>Line:</strong> <?= htmlspecialchars($line ?? '') ?></p>
            <pre><?= isset($stackTrace) && is_array($stackTrace) ? implode(PHP_EOL, $stackTrace) : '' ?></pre>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
