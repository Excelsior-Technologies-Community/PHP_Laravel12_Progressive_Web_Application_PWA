<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PWA</title>

    @PwaHead
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #0f0f0f, #1c1c1c);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: auto;
        }

        /* Buttons */
        .btn {
            padding: 10px 18px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: #0d6efd;
            color: #fff;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Cards */
        .card {
            background: #111;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .card h3 {
            margin: 0 0 5px;
        }

        .price {
            color: #0d6efd;
            font-weight: bold;
        }

        /* Forms */
        form {
            background: #111;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 16px;
            border-radius: 12px;
            border: none;
            background: #1f1f1f;
            color: #fff;
            outline: none;
        }

        input[type="file"] {
            background: none;
        }

        textarea {
            resize: vertical;
        }

        img {
            border-radius: 12px;
            margin-top: 10px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        /* Mobile */
        @media(max-width: 600px) {
            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <h1>Laravel 12 PWA</h1>

    <div class="container">
        @yield('content')
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>

</body>
</html>
