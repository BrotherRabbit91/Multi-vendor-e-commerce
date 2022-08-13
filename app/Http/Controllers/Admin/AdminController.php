<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth; // Check the Admin.php model and 12:47 in https://www.youtube.com/watch?v=_vBCl-77GYc&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=11
use Symfony\Component\VarDumper\VarDumper;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin/dashboard'); // is the same as:    return view('admin.dashboard');
    }

    public function login(Request $request) { // Logging in using our 'admin' guard we created in auth.php
        // Hashing Passwords: https://laravel.com/docs/9.x/hashing#hashing-passwords
        // echo $password = \Illuminate\Support\Facades\Hash::make('123456');
        // die;


        // https://www.youtube.com/watch?v=_vBCl-77GYc&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=11
        // HTTP Requests: https://laravel.com/docs/9.x/requests
        // Retrieving The Request Method: https://laravel.com/docs/9.x/requests#retrieving-the-request-method
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);


            // Laravel Server-Side Validation: https://www.youtube.com/watch?v=IiyqoBUrkZA&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=12
            // https://laravel.com/docs/9.x/validation
            /*
            $validated = $request->validate([
                // Available Validation Rules: https://laravel.com/docs/9.x/validation#available-validation-rules
                'email'    => 'required|email|max:255',
                'password' => 'required',
            ]);
            */

            // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Check 9:24 in https://www.youtube.com/watch?v=IiyqoBUrkZA&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=12
            // https://laravel.com/docs/9.x/validation#manual-customizing-the-error-messages
            $rules = [
                'email'    => 'required|email|max:255',
                'password' => 'required',
            ];
            $customMessages = [
                'email.required'    => 'Email Address is required!',
                'email.email'       => 'Valid Email Address is required',
                'password.required' => 'Password is required!',
            ];
            $this->validate($request, $rules, $customMessages);

            // Logging in using our 'admin' guard we created in auth.php    // Check 5:44 in https://www.youtube.com/watch?v=_vBCl-77GYc&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=11
            // Manually Authenticating Users (using attempt() method()): https://laravel.com/docs/9.x/authentication#authenticating-users
            // if (\Illuminate\Support\Facades\Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password'], 'status' => 1])) { // Check the Admin.php model and 12:47 in https://www.youtube.com/watch?v=_vBCl-77GYc&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=11
            if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password'], 'status' => 1])) { // Check the Admin.php model and 12:47 in https://www.youtube.com/watch?v=_vBCl-77GYc&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=11
                return redirect('/admin/dashboard'); // Let him LOGIN!
            } else { // If login credentials are incorrect
                // Redirecting With Flashed Session Data: https://laravel.com/docs/9.x/responses#redirecting-with-flashed-session-data
                // return back()->with('error_message', 'Invalid Email or Password');
                return redirect()->back()->with('error_message', 'Invalid Email or Password');
            }
        }
        return view('admin/login'); // is the same as:    return view('admin.login');
    }

    public function logout() {
        Auth::guard('admin')->logout(); // Logging out using our 'admin' guard that we created in auth.php
        return redirect('admin/login');
    }

    public function updateAdminPassword(Request $request) {
        // Handling the update admin password <form> submission (POST request) in update_admin_password.blade.php    // Check https://www.youtube.com/watch?v=oAZKXYrkcr4&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=17
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            // Check first if the entered admin current password is corret
            if (\Illuminate\Support\Facades\Hash::check($data['current_password'], Auth::guard('admin')->user()->password)) { // ['current_password'] comes from the AJAX call in custom.js page from the data object inside $.ajax() method
                // Check if the new password is matching with confirm password
                if ($data['confirm_password'] == $data['new_password']) {
                    // dd(\App\Models\Admin::where('id', Auth::guard('admin')->user()->id));
                    // echo '<pre>', var_dump(\App\Models\Admin::where('id', Auth::guard('admin')->user()->id)), '</pre>';
                    \App\Models\Admin::where('id', Auth::guard('admin')->user()->id)->update(['password' => bcrypt($data['new_password'])]); // we persist (update) the hashed password (not the password itself)
                    return redirect()->back()->with('success_message', 'Admin Password has been updated successfully!');
                } else { // If new password and confirm password are not matching each other
                    return redirect()->back()->with('error_message', 'New Password and Confirm Password does not match!');
                }
            } else {
                return redirect()->back()->with('error_message', 'Your current admin password is Incorrect!');
            }
        }


        // Get data from 'admin' Authentication Guard to be able to show them in the <form> of update_admin_password.blade.php page: Check 19:10 in https://www.youtube.com/watch?v=b4ISE_polCo&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=15
        // dd(Auth::guard('admin'));
        // dd(Auth::guard('admin')->user());
        // echo '<pre>', var_dump(\App\Models\Admin::where('email', Auth::guard('admin')->user()->email)), '</pre>';
        // echo '<pre>', var_dump(\App\Models\Admin::where('email', Auth::guard('admin')->user()->email)->first()), '</pre>';
        // echo '<pre>', var_dump(\App\Models\Admin::where('email', Auth::guard('admin')->user()->email)->first()->toArray()), '</pre>';
        // exit;
        // dd(Auth::guard('admin')->user()->email); // https://laravel.com/docs/9.x/eloquent#examining-attribute-changes
        // dd(Auth::guard('admin')->user()->email)->first(); // https://laravel.com/docs/9.x/eloquent#examining-attribute-changes
        $adminDetails = \App\Models\Admin::where('email', Auth::guard('admin')->user()->email)->first()->toArray(); // 'Admin' is the Admin.php model    // Auth::guard('admin') is the authenticated user using the 'admin' guard we created in auth.php    // https://laravel.com/docs/9.x/eloquent#retrieving-models
        return view('admin/settings/update_admin_password')->with(compact('adminDetails')); // Passing Data To Views: https://laravel.com/docs/9.x/views#sharing-data-with-all-views
    }

    public function checkAdminPassword(Request $request) { // This method is called from the AJAX call in custom.js page 
        $data = $request->all();
        // dd($data); // THIS DOESN'T WORK WITH AJAX - SHOWS AN ERROR!!
        // echo '<pre>', var_dump($data), '</pre>';

        // Check 15:06 in https://www.youtube.com/watch?v=maEXuJNzE8M&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=17
        // Hashing Passwords: https://laravel.com/docs/9.x/hashing#hashing-passwords
        if (\Illuminate\Support\Facades\Hash::check($data['current_password'], Auth::guard('admin')->user()->password)) { // ['current_password'] comes from the AJAX call in custom.js page from the data object inside $.ajax() method
            return 'true';
        } else {
            return 'false';
        }
    }
}
