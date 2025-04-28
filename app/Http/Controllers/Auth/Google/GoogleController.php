<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendSMSNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request){
        
        return Socialite::driver('google')->redirect();
    }
    public function callback(Request $request){

        $user = Socialite::driver('google')->user();
           
        $findUser = User::where('google_id', $user->id)->first();
       
        if($findUser){
            Auth::login($findUser);
        }else{
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'email_verified_at' => now(),
                'google_avatar' => $user->avatar,
                'password' => encrypt('123456dummy')
            ]);
            Auth::login($newUser);
            
        }
      
        return redirect()->route('dashboard');
    }
    public function sendCode()
    {
        //dd(config('services.twilio'));

        try {
            FacadesNotification::route('twilio', '+918155870807') // static number
            ->notify(new SendSMSNotification());
        } catch (\Exception $e) {
            return response()->json([
            'error' => 'Failed to send SMS: ' . $e->getMessage()
            ], 500);
        }
    
        return 'SMS sent successfully';
    }
    
}
