<section>
    <header>
        <h2 class="text-lg sd font-medium text-gray-900">
            {{ __('Profile Picture') }}
        </h2>

        <!-- <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p> -->
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')
        
        <div class="mb-3">
            <div class="img_user">
                @if(empty($user->avatar))
                <img src="{{asset('admin/img/userEll.svg')}}" alt="">
                @else
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('uploads/profile/default-avatar.png') }}" alt="User Avatar">

                @endif
                <!-- <button type="button" class="change_avt ml-3">Change Avtar</button> -->
                <input type="file" class="form-control change_avt ml-3" name="image" />
            </div>
        </div>

        <div class="mb-3">
            <x-input-label for="name" class="form-label" :value="__('Display Name')" />
            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $user->name)" required
                autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
         <div class="mb-3">
            <x-input-label for="Mode" class="form-label" :value="__('Two Step Verification')" />
            <select name="two_step" id="Mode" class="form-control">
                <option {{ Auth::user()->two_step == 0 ? 'selected' : '' }} value="0">Disabled</option>
                <option {{ Auth::user()->two_step == 1 ? 'selected' : '' }} value="1">Enabled</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('mode')" />
        </div> 
        <div class="mb-3">
            <x-input-label for="email" class="form-label" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required
                autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (!$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary btn-sm btn_tn">{{ __('Update Profile') }}</button>
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ 'Saved' }}
                    <button type="button" class="btn btn-sm float-end float-right" data-bs-dismiss="alert"
                        aria-label="Close">&times;</button>
                </div>
            @endif
        </div>
    </form>
</section>
