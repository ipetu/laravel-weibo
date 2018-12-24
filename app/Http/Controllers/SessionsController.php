<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class SessionsController extends Controller
{
    //
    public function create(){
        return view('sessions.create');
    }

    public function store(Request $request){
        $credentials = $this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required|min:6'
        ]);
        if (Auth::attempt($credentials,$request->has('remember'))){
            //登陆成功之后的数据
            session()->flash('success',trans('hb.user_login_success'));
            return redirect()->route('users.show',[Auth::user()]);
        }else{
            //登陆失败之后的相关操作
            session()->flash('danger',trans('hb.user_login_fail'));
            return redirect()->back()->withInput();
        }
        return;
    }

    public function destory(){
        Auth::logout();
        session()->flash('success','您已经成功退出');
        return redirect('login');
    }
}
