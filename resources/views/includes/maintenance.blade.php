<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            overflow: hidden;
        }

        .container {
            text-align: center;
            animation: fadeIn 2s ease-in-out;
        }

        .logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px auto;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .logo img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        p {
            font-size: 1.2rem;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
    <script>
        setTimeout(() => {
            window.location.reload();
        }, 10000); // Refresh every 10 seconds
    </script>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{asset('wb-logo2.webp')}}" alt="Logo">
        </div>
        <h1>We'll Be Right Back!</h1>
        <p>Our site is currently under maintenance. Please check back shortly.</p>
    </div>
</body>
</html>
