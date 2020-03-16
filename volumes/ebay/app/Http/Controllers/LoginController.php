<?php

namespace App\Http\Controllers;



class LoginController extends Controller
{

    public function showLoginPage() {
        return view('login');
    }

    //登入處理
    public function processLogin(){

        $post = \Request::all();
        if(\Auth::attempt(['account'=>$post['account'] , 'password'=>$post['password']])) {
            return \Redirect::to('/');
        } else {
           return \Redirect::to('/login');
        }

    }
}