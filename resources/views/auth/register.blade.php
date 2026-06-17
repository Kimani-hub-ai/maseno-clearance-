<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <h2 class="text-xl font-bold text-center mb-6">Student Registration</h2>

        {{-- Account Details --}}
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Account Details</p>

        <div>
            <x-input-label for="name" :value="__('Display Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text"
                name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Student Details --}}
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-8 mb-3">Student Details</p>

        <div>
            <x-input-label for="reg_number" :value="__('Registration Number')" />
            <x-text-input id="reg_number" class="block mt-1 w-full" type="text"
                name="reg_number" :value="old('reg_number')" placeholder="e.g. MAS/001/2020" required />
            <x-input-error :messages="$errors->get('reg_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="full_name" :value="__('Full Name (as on ID)')" />
            <x-text-input id="full_name" class="block mt-1 w-full" type="text"
                name="full_name" :value="old('full_name')" required />
            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="faculty" :value="__('Faculty')" />
            <select id="faculty" name="faculty"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                <option value="">-- Select Faculty --</option>
                <option value="Arts & Social Sciences" {{ old('faculty') == 'Arts & Social Sciences' ? 'selected' : '' }}>Arts & Social Sciences</option>
                <option value="Science" {{ old('faculty') == 'Science' ? 'selected' : '' }}>Science</option>
                <option value="Education" {{ old('faculty') == 'Education' ? 'selected' : '' }}>Education</option>
                <option value="Commerce & Economics" {{ old('faculty') == 'Commerce & Economics' ? 'selected' : '' }}>Commerce & Economics</option>
                <option value="Health Sciences" {{ old('faculty') == 'Health Sciences' ? 'selected' : '' }}>Health Sciences</option>
                <option value="Engineering" {{ old('faculty') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                <option value="Law" {{ old('faculty') == 'Law' ? 'selected' : '' }}>Law</option>
            </select>
            <x-input-error :messages="$errors->get('faculty')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="department" :value="__('Department')" />
            <x-text-input id="department" class="block mt-1 w-full" type="text"
                name="department" :value="old('department')" placeholder="e.g. Computer Science" required />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="programme" :value="__('Programme / Course')" />
            <x-text-input id="programme" class="block mt-1 w-full" type="text"
                name="programme" :value="old('programme')" placeholder="e.g. BSc Computer Science" required />
            <x-input-error :messages="$errors->get('programme')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="graduation_year" :value="__('Expected Graduation Year')" />
            <x-text-input id="graduation_year" class="block mt-1 w-full" type="number"
                name="graduation_year" :value="old('graduation_year')" min="2000" max="2030" required />
            <x-input-error :messages="$errors->get('graduation_year')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number (optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text"
                name="phone" :value="old('phone')" placeholder="e.g. 0712345678" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>