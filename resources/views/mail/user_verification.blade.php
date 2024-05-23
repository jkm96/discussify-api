<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Pet Diaries | Email Verification</title>
    <style>
        /* Reset some Bootstrap styles */
        body, figure, h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 50px auto; /* Center horizontally */
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h4 {
            color: #333333;
            margin-bottom: 10px;
        }

        p {
            color: #555555;
            margin-bottom: 15px;
        }

        a {
            color: #5ca15e;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #3d7e4f;
        }

        .slogan {
            font-size: 9px;
            font-weight: bolder;
            font-style: normal;
            text-align: center;
        }

        .verify-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #48bb78;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .verify-button:hover {
            background-color: #5bda90;
            color: #ffffff;
        }

        .copyright {
            margin-top: 20px;
            text-align: center;
            color: #888888;
        }
    </style>
</head>

<body>
<div class="container">
    <h4 class="h4">Hello {{ $username }},</h4>
    <p>Welcome to Pet Diaries, where every moment with your furry friends is a cherished memory!</p>
    <p>We're thrilled to have you on board. Please take a moment to verify your email by clicking the button below!.</p>

    <p style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="verify-button">Verify</a>
    </p>

    <p>If you have any questions or need assistance, feel free to reach out. Our team is here to help!</p>
    <p>Wishing you wonderful moments on Discussify!</p>

    @include('mail.slogan')

</div>
</body>

</html>
