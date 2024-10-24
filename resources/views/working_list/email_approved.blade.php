<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working List Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f3ff;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background-color: #28a745; /* Green color for approval */
            color: white;
            padding: 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-body p {
            margin: 15px 0;
        }
        .email-body .highlight {
            background-color: #f1f7ff;
            border-left: 4px solid #28a745; /* Green border to match approval theme */
            padding: 10px 15px;
            margin: 10px 0;
        }
        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #f1f7ff;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            color: #666666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #28a745; /* Green button for approval */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <h1>Your Working List has been Approved!</h1>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <p>Hello,</p>
            <p>We are pleased to inform you that your working list has been approved with the following details:</p>

            <div class="highlight">
                <p><strong>Working List:</strong> {{ $workingList->name }}</p>
                <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($workingList->deadline)->format('d/m/Y H:i') }}</p>
                <p><strong>Status:</strong> Approved</p>
                <p><strong>Approved By:</strong> {{ $workingList->approver->name }}</p>
            </div>

            <p>You can now proceed with the necessary steps to complete the approved working list.</p>

            <!-- Optional Call-to-Action Button -->
            <a href="" class="btn">View Working List</a>
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <p>Thank you for your attention.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>