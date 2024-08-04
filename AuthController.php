<?php

namespace App\Http\Controllers;

use Hash;
use Exception;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginView(Request $request)
    {
        try {
            $service = null;
            if($request->service)
                $service = $request->service;

            return view('auth.login',compact('service'));
        }catch (\Exception $e) {
            abort(403);
        }
    }

    public function creadentialValidation(Request $request)
    {
        try {
            $request->validate([
                "username" => 'required|string',
                "password" => 'required|string',
            ]);

            $service = $request->service;
            if (Auth::check()) {
                return redirect("/?service=${service}");
            }
            $credentials = $request->only('username', 'password');
            $userCheck = User::where('username',$credentials["username"])->where("password",Hash::check($credentials["password"]))->first();

            if($userCheck){
                auth()->login($userCheck);
                return redirect("/?service=${service}");
            }else{
                return back();
            }
        }catch (ValidationException $e) {
            $errorMessages = $e->errors();
            return $errorMessages;
        }catch (Exception $e) {
            abort(503);
        }
    }
}
