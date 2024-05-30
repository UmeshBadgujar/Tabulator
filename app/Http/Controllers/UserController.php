<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Initialize query
        $query = User::query();

        // Filtering
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }
        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->input('phone_number') . '%');
        }
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }
        if ($request->has('gender')) {
            $query->where('gender', 'like', $request->input('gender'));
        }
        if ($request->has('favourite')) {
            $query->where('favourite', 'like', '%' . $request->input('favourite') . '%');
        }
        if ($request->has('dob')) {
            $query->where('dob', $request->input('dob'));
        }

       // Sorting
        // if ($request->has('sort_by')) {
        //     $sortBy = $request->input('sort_by');
        //     $sortOrder = $request->input('sort_order', 'asc'); // Default to ascending
        //     if (in_array($sortBy, ['name', 'email', 'phone_number', 'dob', 'location', 'gender','favourite'])) {
        //         $query->orderBy($sortBy, $sortOrder);
        //     }
        // }

        if ($request->has('sort_by')) {
            $sorts = explode(',', $request->input('sort_by'));
            foreach ($sorts as $sort) {
                list($field, $direction) = explode(':', $sort);
                if (in_array($field, ['id','name', 'email', 'phone_number', 'dob', 'location', 'gender', 'favourite']) && in_array($direction, ['asc', 'desc'])) {
                    $query->orderBy($field, $direction);
                }
            }
        }

         // Pagination with custom per_page value
         $perPage = $request->input('per_page', 200);
         $users = $query->paginate($perPage);

        return response()->json($users);
    }

    public function update_user(Request $request){
        try{
            $input = $request->all();
            $user = User::find($input['id']);

            if(!empty($user)){

                $user->name = isset($input['name']) ? $input['name']:$user->name;
                $user->email = isset($input['email']) ? $input['email']:$user->email;
                $user->phone_number = isset($input['phone_number']) ? $input['phone_number']:$user->phone_number;
                $user->location = isset($input['location']) ? $input['location']:$user->location;
                $user->gender = isset($input['gender']) ? $input['gender']:$user->gender;
                $user->favourite = isset($input['favourite']) ? $input['favourite']:$user->favourite;
                $user->dob = isset($input['dob']) ? date('y-m-d',strtotime($input['dob'])):$user->dob;
                $user->save();

                return response()->json(['success' => 1,'msg' => 'User has been updated successfully.'], 200);
            }else{
                return response()->json(['error' => "This user doesn't exist."], 201);
            }
           
        }catch(Exception $e){
            return response()->json(['error' => 'Something went wrong'], 201);
        }
    }
}
