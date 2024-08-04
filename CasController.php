<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CasController extends Controller
{
    public function ticketGeneration(Request $request)
    {
        try {
            $service = $request->service;

            if (Auth::check()) {
                $user = Auth::user();
                $salt = Str::random(20);
                $data = [
                    "user_id" => $user->id,
                    "salt" => $salt,
                ];
                $ticket = encrypt(json_encode($data));
                return redirect($request->service."&ticket=${ticket}");
            }

            $service = $request->service;
            return redirect("/login?service=${service}");
        }catch (ValidationException $e) {
            $errorMessages = $e->errors();
            return $errorMessages;
        }catch (Exception $e) {
            return $e->getMessage();
            abort(503);
        }
    }

    public function serviceValidate(Request $request)
    {
        try {
            $service = $request->service;
            if ($request->ticket) {
              try {
                $ticket = json_decode(decrypt($request->ticket));
                $user = User::find($ticket->user_id);
                $username = $user->username;
                $email = $user->email;
                $phone = $user->phone;
                $comment = $user->email;
                $name = $user->name;
                header('Content-Type: application/xml');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<cas:serviceResponse xmlns:cas="http://www.yale.edu/tp/cas">';
                echo '<cas:authenticationSuccess>';
                echo "<cas:user>${username}</cas:user>";
                echo '<cas:attributes>';
                echo "<cas:email>${email}</cas:email>";
                echo "<cas:name>${name}</cas:name>";
                echo "<cas:phone>${phone}</cas:phone>";
                echo "<cas:comment>${comment}</cas:comment>";
                echo '</cas:attributes>';
                echo '</cas:authenticationSuccess>';
                echo '</cas:serviceResponse>';
                exit();
               }catch (Exception $e) {
                    abort(503);
                }
            }

            if (Auth::check()) {
                $user = Auth::user();
                $salt = Str::random(20);
                $data = [
                    "user_id" => $user->id,
                    "salt" => $salt,
                ];
                $ticket = encrypt(json_encode($data));
                return redirect($request->service."&ticket=${ticket}");
            }

            $service = $request->service;
            return redirect("/login?service=${service}");
        }catch (ValidationException $e) {
            $errorMessages = $e->errors();
            return $errorMessages;
        }catch (Exception $e) {
            return $e->getMessage();
            abort(503);
        }
    }
}
