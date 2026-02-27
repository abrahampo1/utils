<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full tinos-regular">
    <div class="flex min-h-full items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm space-y-8">
            <div>
                <h2 class="text-center text-2xl tinos-bold text-black">Admin Login</h2>
            </div>

            @if($errors->any())
                <div class="border-black border border-r-3 border-b-3 p-4">
                    <p class="text-sm text-black">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm tinos-bold text-black">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                           value="{{ old('email') }}"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm tinos-bold text-black">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 border-black border accent-black">
                    <label for="remember" class="ml-2 block text-sm text-black">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold focus:outline-none hover:bg-gray-800">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</body>
</html>
