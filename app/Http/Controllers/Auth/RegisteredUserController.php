<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
   public function store(Request $request): RedirectResponse
     {
         $request->validate([
             'name'            => ['required', 'string', 'max:255'],
             'email'           => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
             'password'        => ['required', 'confirmed', Rules\Password::defaults()],
             'reg_number'      => ['required', 'string', 'max:20', 'unique:students,reg_number'],
             'full_name'       => ['required', 'string', 'max:255'],
             'faculty'         => ['required', 'string', 'max:255'],
             'department'      => ['required', 'string', 'max:255'],
             'programme'       => ['required', 'string', 'max:255'],
             'graduation_year' => ['required', 'digits:4', 'integer', 'min:2000', 'max:2030'],
              'phone'           => ['nullable', 'string', 'max:15'],
            ]);

         $user = User::create([
             'name'      => $request->name,
             'email'     => $request->email,
             'password'  => Hash::make($request->password),
             'role'      => 'student',
             'is_active' => true,
            ]);

         // Create the linked student profile
         $user->student()->create([
             'reg_number'      => strtoupper($request->reg_number),
             'full_name'       => $request->full_name,
             'faculty'         => $request->faculty,
             'department'      => $request->department,
             'programme'       => $request->programme,
             'graduation_year' => $request->graduation_year,
             'phone'           => $request->phone,
             'status'          => 'active',
            ]);

         event(new Registered($user));
         Auth::login($user);

         return redirect(route('student.dashboard'));
    }
}
