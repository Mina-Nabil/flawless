<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function login()
    {

        $data['username'] = '';
        $data['first'] = true;
        return view('auth/login', $data);
    }

    public function authenticate(Request $request)
    {
        if (Auth::check()) return redirect('/home');

        $userName = $request->input('userName');
        $passWord = $request->input('passWord');

        $data['first'] = true;

        if (isset($userName)) {
            if (Auth::attempt(array('DASH_USNM' => $userName, 'password' => $passWord, 'DASH_ACTV' => 1), true)) {
                return redirect('/home');
            } else {
                $data['first'] = false;
                $data['username'] = $userName;
                return view('auth/login', $data);
            }
        } else {
            redirect("login");
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (!Auth::check()) return redirect('/login');
        $this->data['title'] = "FLAWLESS Dashboard";
        return view('home', $this->data);
    }
}
