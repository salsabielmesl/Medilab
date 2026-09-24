<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediLab Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, indigo, indigo 20%, indigo 40%, indigo 60%, indigo 80%, indigo 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .glass-form {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 40px;
      width: 300px;
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .title {
      font-family: 'Pacifico', cursive;
      font-size: 32px;
      text-align: center;
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .glass-form input {
      width: 100%;
      padding: 10px 15px;
      margin-bottom: 15px;
      border: none;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      font-size: 14px;
    }

    .glass-form input::placeholder {
      color: #eee;
    }

    .glass-form button {
      width: 100%;
      padding: 10px;
      border: none;
      background-color: white;
      color: indigo;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    .glass-form button:hover {
      background-color: #e0e0ff;
    }
  </style>
</head>
<body>
  <form class="glass-form" action="loginprocess.php" method="POST">
    <div class="title">
      <i class="bi bi-plus-circle-fill"></i>
      MediLab
    </div>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>
</body>
</html>
