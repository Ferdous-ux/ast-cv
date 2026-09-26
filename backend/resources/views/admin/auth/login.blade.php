<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>AST-CV Admin — Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0A0A0A;
            color: #F5F5F5;
            font-family: Arial, Helvetica, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: #171717;
            border: 1px solid #262626;
            border-radius: 16px;
        }

        .brand {
            margin-bottom: 32px;
        }

        .brand h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .brand p {
            margin: 0;
            color: #A3A3A3;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #262626;
            border-radius: 8px;
            background: #111111;
            color: #F5F5F5;
            outline: none;
        }

        input:focus {
            border-color: #DC2626;
        }

        .error {
            margin-top: 6px;
            color: #EF4444;
            font-size: 13px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 8px;
            background: #DC2626;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #EF4444;
        }
    </style>
</head>

<body>
    <main class="login-card">
        <div class="brand">
            <h1>AST-CV Admin</h1>
            <p>Sign in to your administration panel.</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >

                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">
                Sign In
            </button>
        </form>
    </main>
</body>
</html>
