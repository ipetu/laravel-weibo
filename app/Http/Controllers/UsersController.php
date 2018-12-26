<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Mail;
use Auth;
class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
           'except'=>['show','create','store','confirmEmail']
        ]);
        $this->middleware('guest',[
           'only'=>['create']
        ]);
    }

    public function index(){
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }
    //
    public function create(){
        return view('users.create');
    }

    public function show(User $user){
//        app()->setLocale('zh-HK');
//        echo trans('hb.user_regiest_success');
        $this->authorize('update',$user);
        return view('users.show',compact('user'));
    }

    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);
        $this->sendEmailConfirmationTo($user);
//        Auth::login($user);
        session()->flash('success','验证邮箱已发送到你注册的邮箱中，请注册查收');
        return redirect('/');
    }

    public function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'andy@exmaple.com';
        $name = 'andy';
        $to = $user->email;
        $subject = '感谢注册weibo应用!请确认你的邮箱';
        Mail::send($view,$data,function($message) use ($from,$name,$to,$subject){
           $message->from($from,$name)->to($to)->subject($subject);
        });
    }

    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }

    public function update(User $user,Request $request){
        $this->authorize('update',$user);
        $this->validate($request,[
           'name'=>'required|max:50',
//           'password'=>'required|confirmed|min:6',
            'password'=>'nullable|confirmed|min:6',
        ]);
        $data = [];
        $data['name'] = $request->name;
        if ($request->passwrod){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
//        $user->update([
//           'name'=>$request->name,
//           'password'=>bcrypt($request->password),
//        ]);
        session()->flash('success','个人资料更新成功');
        return redirect()->route('users.show',$user);
    }

    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        Auth::login($user);
        session()->flash('success','恭喜你,激活成功');
        return redirect()->route('users.show',[$user]);
    }
}
