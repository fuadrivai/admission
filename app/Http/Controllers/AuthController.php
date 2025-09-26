<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('login.login');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role ?? 'user');

        // gunakan guard api
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout(Request $request)
    {
        $token = session('Authorization');
        Session::where('token', $token)->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function authenticate(Request $request)
    {

        try {
            $credentials = $request->validate([

                'email' => ['required'],
                'password' => ['required'],
            ]);
            $token = Auth::attempt($credentials);
            if (isset($token)) {
                $request->session()->regenerate();
                //get JWT token login to system
                $jwtToken = Auth::guard('api')->attempt($credentials);
                if (!$jwtToken) {
                    return back()->with('LoginError', 'Incorrect username or password ');
                }
                $user = Auth::guard('api')->user();
                $sessions = Session::where('user_id', $user->id)->where('device', 'web')->get();
                if (count($sessions) >= 5) {
                    Session::where('user_id', $user->id)->where('device', 'web')->delete();
                }

                $session = new Session();
                $session->token = 'bearer ' . $jwtToken;
                $session->user_id = $user->id;
                $session->device = "web";
                $session->device_id = "";
                $session->login_date = date("Y-m-d H:i:s");
                $session->save();

                session(['Authorization' => $session->token]);
                return redirect()->intended('/');
            }
            return back()->with('LoginError', 'Email is not valid');
        } catch (\Throwable $th) {
            return back()->with('LoginError', $th->getMessage());
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('login.change-password', ['title' => "Change Password"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is wrong']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
