<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working List Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe9e9;
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
            background-color: #dc3545;
            /* Red color for rejection */
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
            background-color: #ffe9e9;
            border-left: 4px solid #dc3545;
            /* Red border to match rejection theme */
            padding: 10px 15px;
            margin: 10px 0;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #fbeaea;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            color: #666666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background-color: #dc3545;
            /* Red button for rejection */
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
            <h1>Your Working List has been Rejected</h1>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <p>Hello,</p>
            <p>We regret to inform you that your working list has been rejected with the following details:</p>

            <div class="highlight">
                <p><strong>Working List:</strong> {{ $workingList->name }}</p>
                <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($workingList->deadline)->format('d/m/Y H:i') }}</p>
                <p><strong>Status:</strong> Rejected</p>
                <p><strong>Rejected By:</strong> {{ $workingList->rejecter->name }}</p>
                <p><strong>Rejection Reason:</strong> {{ $workingList->reject_reason }}</p>
            </div>

            <p>Please review the rejection reason and make the necessary adjustments before resubmitting your working list for approval.</p>

            <!-- Optional Call-to-Action Button -->
            <a href="{{ url('/working-list/' . $workingList->id) }}" class="btn">View Working List</a>
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <p>Thank you for your attention.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
