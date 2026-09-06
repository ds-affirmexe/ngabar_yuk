<?php

session_start();
require_once '../config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {

        $error = 'Username dan password wajib diisi.';
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama, username, password, role, status
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$user) {

            $error = 'Username atau password tidak sesuai.';
        } elseif ($user['status'] !== 'aktif') {

            $error = 'Akun ini sedang dinonaktifkan. Hubungi Super Admin.';
        } elseif (!password_verify($password, $user['password'])) {

            $error = 'Username atau password tidak sesuai.';
        } else {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Webmaster - Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .javanese-pattern {
            background-image:
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025));
            background-position: 0 0, 0 0, 8px 14px;
            background-size: 16px 28px;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen">

    <div class="min-h-screen flex">

        <div class="hidden lg:flex lg:w-1/2 bg-[#542f1b] text-white relative overflow-hidden">

            <div class="absolute inset-0 javanese-pattern"></div>

            <div class="relative z-10 flex flex-col justify-between w-full p-12">

                <a href="../index.php" class="inline-flex items-center gap-3 w-fit">

                    <div class="w-11 h-11 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center shadow-sm">

                        <i class="fa-solid fa-mug-hot text-lg"></i>

                    </div>

                    <div class="leading-none">

                        <div class="text-xl font-black tracking-tight">

                            Ngabar
                            <span class="text-amber-400">Yuk!</span>

                        </div>

                        <div class="text-[10px] uppercase tracking-[0.18em] text-stone-300 mt-1">

                            Warta • Reriungan • Insight

                        </div>

                    </div>

                </a>

                <div class="max-w-md">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[0.18em] mb-5">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Webmaster

                    </div>

                    <h1 class="text-4xl xl:text-5xl font-black tracking-tight leading-tight">

                        Selamat datang di

                        <span class="text-amber-400">ruang kerja.</span>

                    </h1>

                    <p class="text-stone-300 leading-relaxed mt-5 text-sm">

                        Masuk untuk mengelola kabar, mengatur konten,
                        dan menjalankan kebutuhan Webmaster Ngabar Yuk!.

                    </p>

                </div>

                <p class="text-xs text-stone-500">

                    © 2026 Ngabar Yuk!

                </p>

            </div>

        </div>

        <div class="flex-1 flex items-center justify-center px-5 py-10">

            <div class="w-full max-w-md">

                <div class="lg:hidden mb-8">

                    <a href="../index.php" class="inline-flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center">

                            <i class="fa-solid fa-mug-hot"></i>

                        </div>

                        <div class="leading-none">

                            <div class="text-xl font-black tracking-tight">

                                Ngabar
                                <span class="text-amber-600">Yuk!</span>

                            </div>

                            <div class="text-[10px] uppercase tracking-[0.18em] text-stone-400 mt-1">

                                Webmaster

                            </div>

                        </div>

                    </a>

                </div>

                <div class="mb-8">

                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center mb-5">

                        <i class="fa-solid fa-right-to-bracket text-lg"></i>

                    </div>

                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-2">

                        Webmaster

                    </p>

                    <h2 class="text-3xl font-black text-stone-900 tracking-tight">

                        Masuk ke sistem

                    </h2>

                    <p class="text-sm text-stone-500 mt-2 leading-relaxed">

                        Gunakan akun Webmaster yang telah terdaftar untuk melanjutkan.

                    </p>

                </div>

                <?php if (!empty($error)): ?>

                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 flex items-start gap-3">

                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                        <p class="text-sm leading-relaxed">

                            <?= htmlspecialchars($error); ?>

                        </p>

                    </div>

                <?php endif; ?>

                <form method="POST" action="" class="space-y-5">

                    <div>

                        <label for="username" class="block text-sm font-bold text-stone-700 mb-2">

                            Username

                        </label>

                        <div class="relative">

                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">

                                <i class="fa-solid fa-user"></i>

                            </span>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                class="w-full pl-11 pr-4 py-3.5 bg-white border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition"
                                required>

                        </div>

                    </div>

                    <div>

                        <label for="password" class="block text-sm font-bold text-stone-700 mb-2">

                            Password

                        </label>

                        <div class="relative">

                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">

                                <i class="fa-solid fa-lock"></i>

                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                class="w-full pl-11 pr-12 py-3.5 bg-white border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition"
                                required>

                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-stone-400 hover:text-stone-700 transition">

                                <i id="passwordIcon" class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        name="login"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3.5 rounded-xl transition shadow-sm">

                        <i class="fa-solid fa-right-to-bracket text-xs"></i>

                        Masuk

                    </button>

                </form>

                <div class="mt-7 pt-6 border-t border-stone-200 flex items-center justify-between gap-4">

                    <a href="../index.php"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-stone-500 hover:text-[#542f1b] transition">

                        <i class="fa-solid fa-arrow-left text-[10px]"></i>

                        Kembali ke Ngabar Yuk!

                    </a>

                    <span class="text-[11px] text-stone-400">

                        Webmaster Area

                    </span>

                </div>

            </div>

        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>