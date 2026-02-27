@extends('admin.layouts.app')

@section('header', 'Settings')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-8 bg-white border-black border border-r-3 border-b-3 p-6">
        @csrf @method('PUT')

        {{-- Profile --}}
        <div>
            <h2 class="text-lg tinos-bold text-black mb-4">Profile</h2>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm tinos-bold text-black">Name</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $user->name) }}"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                    @error('name') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm tinos-bold text-black">Email</label>
                    <input type="email" name="email" id="email" required
                           value="{{ old('email', $user->email) }}"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                    @error('email') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div>
            <h2 class="text-lg tinos-bold text-black mb-4">Change Password</h2>
            <p class="text-sm text-gray-600 mb-4 tinos-regular-italic">Leave blank to keep current password.</p>
            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm tinos-bold text-black">Current Password</label>
                    <input type="password" name="current_password" id="current_password"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                    @error('current_password') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm tinos-bold text-black">New Password</label>
                    <input type="password" name="new_password" id="new_password"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                    @error('new_password') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm tinos-bold text-black">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                           class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">Save Changes</button>
        </div>
    </form>
</div>
@endsection
