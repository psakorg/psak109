<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Pengembangan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin: 0;
            /* Gradasi dari biru ke ungu */
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }
        .container {
            text-align: center;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.8); /* Background semi transparan untuk konten */
            padding: 30px;
            border-radius: 10px;
        }
        .under-construction {
            font-size: 48px;
            font-weight: bold;
            color: #333;
        }
        .message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #666;
        }
        .contact-info {
            font-size: 16px;
            margin-top: 20px;
        }
        .contact-info p {
            margin: 5px 0;
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: center; align-items: center;">
            <x-application-logo />
        </div>

        <h1 class="under-construction">Sedang Dalam Pengembangan</h1>
        <p class="message">Situs web kami sedang dalam pengembangan. Kami sedang bekerja keras untuk memberikan pengalaman yang lebih baik. Nantikan segera!</p>

        <div class="contact-info">
            <p><strong>Email:</strong> <a href="mailto:cs@pramatech.id">cs@pramatech.id</a></p>
            <p><strong>Telepon:</strong> +62 21 2949 0560</p>
            <p><strong>Mobile:</strong> +62 856 151 2634</p>
        </div>
    </div>

    <!-- Bootstrap 5 JS dan dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
