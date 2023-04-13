<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Response;
use Hash;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        $query =  User::select('email','name');
        if ($flag =='active'){
            $query->where('status',1);
        }
        elseif($flag=='all'){
            //
        }
        else{
            return response()->json([
                'message'=>'Invalid parameter it can be either active or all',
                'status'=>0
            ],400);
        }
        $users = $query->get();  
        if(count($users) > 0){
            $response=[
                'message'=> count($users) . ' users found',
                'status'=> 1,
                'data'=> $users
            ];
        }
        else{
        $response = [
            'message' => count($users). 'users found',
            'status' => 0
        ];
    }
    return response()->json($response,200);
       
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>['required'],
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','min:4','confirmed'],
            'password_confirmation'=>['required']
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(),400);
        }
        else{
            $data=[
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ];
            DB::beginTransaction();
            try {
                $user = User::create($data);
                $token= $user->createToken("auth_token")->accessToken;
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                p($e->getMessages());
                $user = null;
            }
            if ($user!=null) {
                return response()->json(
                    ['message'=>'User registered Successfully','token'=>$token],200
                );
            }
            else{
                return response()->json([
                    
                    'message'=>'Internal server error'
                ],500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if(is_null($user))
        {
            $response=[
                'message'=>'User not Found',
                'status'=>0
            ];
        }
        else{
            $response=[
                'message'=>'User found',
                'status'=>1,
                'data'=> $user
            ];
        }
        return response()->json($response,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                'message'=>'User not Found'
            ]);
        }
        else{
            DB::beginTransaction();
            try {
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $user = null;

            }
            if (is_null($user)){
                return response()->json(['message'=>'Internal server','error_msg'=>$e->getMessage()],500);
            }
            else{
                return response()->json(['message'=>'User Updated'],200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            $response = [
                'message'=>'User does not exist'
            ];
        $respCode = 404;
        }
        else{
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = ['message'=>"User Deleted successfully"];
                $respCode = 200;
            } catch (\Exception $e) {
                DB::rollBack();
                $response = ['message'=>'Internal Server Error'];
                $respCode = 500;
            }
        }
        return response()->json($response,$respCode);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email'=>['required','email'],
            'password'=>['required'],
        ]);

            $user = User::where(['email'=>$validatedData['email'],'password'=>$validatedData['password']])->first();
            $token= $user->createToken("auth_token")->accessToken;
            return response()->json(
                ['message'=>'Logged in Successfully','token'=>$token,'user'=>$user],200
            );

    }
}
