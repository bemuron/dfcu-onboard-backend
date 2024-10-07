<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\DatabaseHandler;
use App\Helpers\DbOperation;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUser;
use App\Mail\NewUpdate;

class AuthController extends Controller
{

        //validate the user's email
        public function validateEmail(Request $request){
            $email = request()->input('email');
            
            //if the candidate's email exists, go ahead and generate the 10 digit code 
            if (self::isUserExist($email)) {
                $validatedData = $request->validate([
                    'email' => 'required|string|email',
                ]);

                //generate the 10 digit code
                $authCode = rand(1000000000,9999999999);
                    
                //update the user's table with code(password), created and expiry time
                //Build the SQL query
                $sql = 'CALL save_auth_code(:email, :password, :codeCreatedAt, :createdAt)';
        
                // Build the parameters array
                $params = array (
                    ':email' => $validatedData['email'],
                    ':password' => Hash::make($authCode),
                    ':codeCreatedAt' => date('Y-m-d H:i:s'),
                    //':codeExpiresAt' => date('Y-m-d H:i:s',strtotime("+5 minutes")),
                    ':createdAt' => date('Y-m-d H:i:s'));   
                    
                $response = DatabaseHandler::Execute($sql, $params);

                if($response){
                    //send code user via email
                    Mail::to($validatedData['email'])->send(new NewUser($authCode));

                    //generate access token for this user, required during every subsquent request
                    $user = User::where('email', $email)->firstOrFail();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'error' => false,
                        'message' => 'Authorization code sent',
                        'access_token' => $token,
                        'email' => $email
                    ]);
                }else{
                    return response()->json([
                        'error' => true,
                        'message' => 'Some error occurred. Email verification failed'
                    ]);
                }

            }
            
            return response()->json([
                'error' => true,
                'message' => 'Your Email does not exist, contact the administrators',
            ]);
            
        }

        //validate the code recieved from the user
        public function validateAuthCode(Request $request){
            $email = request()->input('email');
            $authCode = request()->input('authCode');

            //TODO: check if the code has already expired

            $user = User::where('email', $email)->firstOrFail();
            if(Hash::check($authCode, $user['password'])){
                //user can proceed to register or go to home screen
                //add flag in db table called is_registered to track if the user
                //has registrerd or not, the app will check for this flag to dtermine if
                //user should be sent to registration screen or to home screen

                //time token was issued plus 5 minutes
                if(strtotime($user['token_issued_on']) + 300 > time()){
                    $userDetails = self::getUserByEmailAndPassword($email);

                    return response()->json([
                        'error' => false,
                        'user' => $userDetails,
                        'message' => "Code verified successfully"
                    ]);
                }

                return response()->json([
                    'error' => true,
                    'message' => "Code expired: Request new code and try again"
                ]);
                
            }else{
                return response()->json([
                    'error' => true,
                    'message' => "Invalid code provided Please try again."
                ]);
            }

        }

        //register the api user
    public function RegisterUser(Request $request){
        $email = request()->input('email');
        
        $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'date_of_birth' => 'required|date_format:Y-m-d',
        ]);

        $user = User::where('email', $email)->firstOrFail();

        //generate employee id
        $names = explode( " ", $validatedData['name']);
        $initials = strtoupper($names[1][0]."".$names[0][0]);
        $empCode = $user['user_id']."".$initials;
        $empId = str_pad($empCode, 5, "0", STR_PAD_LEFT);
            
        //Build the SQL query
        $sql = 'CALL update_user(:name, :emp_id, :date_of_birth, :email, :createdAt)';

        // Build the parameters array
        $params = array (':name' => $validatedData['name'], 
            ':emp_id' => $empId, 
            ':date_of_birth' => $validatedData['date_of_birth'], 
            ':email' => $email,
            ':createdAt' => date('Y-m-d H:i:s'));       
                    
        // Execute the query and return the results
        $response = DatabaseHandler::Execute($sql, $params);

        //user details updated succesfully
        if($response){
            
            //save the ID images
            if (request()->hasFile('file')) {
                foreach (request()->file as $file) {
                    $file_name = $file->getClientOriginalName();
                    //move the file to the right folder
                    if($file->move(base_path('public/assets/images/ids/'), $file_name)){
                        //save the image names
                        // Build SQL query
                        $sql = 'CALL save_id_img_name(:img_name, :user_id, :added_on)';
                        
                        // Build the parameters array
                        $params = array (':user_id' => $user['user_id'], ':img_name' => $file_name,
                        ':added_on' => date('Y-m-d H:i:s'));
                        
                        // Execute the query and return the results
                        DatabaseHandler::Execute($sql, $params);
                    }
                }
            }else{
                return response()->json([
                    'error' => true,
                    'message' => 'ID images are required',
                ]);
            }
            
            //log data
            //0 - pending, 1 - successful, 2 - failed
            $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
            $params = array (':api_name' => "Staff Registration", ':request_status' => 1,
                ':date' => date('Y-m-d H:i:s'));
            DatabaseHandler::GetRow($sql, $params);
        
            $userDetails = self::getUserByEmailAndPassword($email);

            return response()->json([
                'error' => false,
                'user' => $userDetails,
                'message' => 'Registered successfully',
            ]);
        }else{
                //log data
                //0 - pending, 1 - successful, 2 - failed
                $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
                $params = array (':api_name' => "Staff Registration", ':request_status' => 2,
                    ':date' => date('Y-m-d H:i:s'));
                DatabaseHandler::GetRow($sql, $params);
                
                return response()->json([
                'error' => true,
                'message' => 'Some error occurred. Registration failed'
            ]);
        }
        
    }
    
    //update user details
    public function UpdateUser(Request $request){
        $user_id = request()->input('user_id');
        $dob = request()->input('date_of_birth');
        $role = request()->input('role');

        //Build the SQL query
        $sql = 'CALL update_user_two(:user_id, :date_of_birth, :role, :updatedAt)'; 

        // Build the parameters array
        $params = array (':user_id' => $user_id,
            ':date_of_birth' => $dob, 
            ':role' => $role,
            ':updatedAt' => date('Y-m-d H:i:s'));       
                    
        // Execute the query and return the results
        $response = DatabaseHandler::Execute($sql, $params);

        //user details updated succesfully
        if($response){
            
            //save the ID images
            if (request()->hasFile('file')) {
                foreach (request()->file as $file) {
                    $file_name = $file->getClientOriginalName();
                    //move the file to the right folder
                    if($file->move(base_path('public/assets/images/ids/'), $file_name)){
                        //save the image names
                        // Build SQL query
                        $sql = 'CALL save_id_img_name(:img_name, :user_id, :added_on)';
                        
                        // Build the parameters array
                        $params = array (':user_id' => $user_id, ':img_name' => $file_name,
                        ':added_on' => date('Y-m-d H:i:s'));
                        
                        // Execute the query and return the results
                        DatabaseHandler::Execute($sql, $params);
                    }
                }
            }else{
                return response()->json([
                    'error' => true,
                    'message' => 'ID images are required',
                ]);
            }

            //log data
            //0 - pending, 1 - successful, 2 - failed
            $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
            $params = array (':api_name' => "Staff Update", ':request_status' => 1,
                ':date' => date('Y-m-d H:i:s'));
            DatabaseHandler::GetRow($sql, $params);

            return response()->json([
                'error' => false,
                'message' => 'Updated successfully',
            ]);
        }else{
            //log data
            //0 - pending, 1 - successful, 2 - failed
            $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
            $params = array (':api_name' => "Staff Update", ':request_status' => 2,
                ':date' => date('Y-m-d H:i:s'));
            DatabaseHandler::GetRow($sql, $params);

                return response()->json([
                'error' => true,
                'message' => 'Some error occurred. Updated failed'
            ]);
        }
        
    }
    
    //add user details
    public function AddUser(Request $request){
        $email = request()->input('email');
        $role = request()->input('role');
        
        if (!self::isUserExist($email)) {
            $validatedData = $request->validate([
                'email' => 'required|string|email|max:255|unique:users'
            ]);
            
            //Build the SQL query
            $sql = 'CALL add_user(:email, :role, :createdAt)'; 
    
            // Build the parameters array
            $params = array (
                ':email' => $validatedData['email'], 
                ':role' => $role,
                ':createdAt' => date('Y-m-d H:i:s'));       
                        
            // Execute the query and return the results
            $response = DatabaseHandler::Execute($sql, $params);
            
            return response()->json([
                    'error' => false,
                    'message' => 'Added successfully',
                ]);
        
        }
        
        return response()->json([
            'error' => true,
            'message' => 'This email already exists',
        ]);

    }

    //get api logs based on the date
    public function getApiLog(Request $request){
        $date = request()->input('date');
            
        //Build the SQL query
        $sql = 'CALL get_api_log(:date)';

        // Build the parameters array
        $params = array (':date' => $date);       
                    
        // Execute the query and return the results
        $response = DatabaseHandler::GetRow($sql, $params);

        if($response){

            return response()->json([
                'error' => false,
                'current_log' => $response,
                'message' => 'Got API log successfully',
            ]);
            
        }else{
                return response()->json([
                'error' => true,
                'message' => 'Error occurred. Could not get log'
            ]);
        }
        
    }
    
    //user has clicked on log out in the app
    //log out the user
    public function LogoutUser() {
        $response = array();
        $user_id = request()->input('user_id');
        
        $result = DbOperation::logOutUser($user_id);
        
        if($result){
            $response["error"] = false;
            $response["message"] = 'Logged out';
        }else{
            $response["error"] = true;
            $response["message"] = "Could not log out now";
        }
        
        return response()->json($response);
    }
    
    //retrieve the user
    public function getUser(Request $request)
    {
        return $request->user();
    }
    
    //check if user with this email already exists
    public function isUserExist($email)
    {
		
        // Build the SQL query
        $sql = 'CALL is_user_exist(:email)';

        // Build the parameters array
        $params = array (':email' => $email);

        // Execute the query and return the results
        return DatabaseHandler::GetRow($sql, $params) > 0;
    }
    
    //Method to get user by email and password during login attempt
    public static function getUserByEmailAndPassword($email)
    {
	// Build the SQL query
        $sql = 'CALL get_user(:email)';

        // Build the parameters array
        $params = array (':email' => $email);

        // Execute the query and return the results
        $user_data = DatabaseHandler::GetRow($sql, $params);
		
	$user = array();
        // user authentication details are correct
        $user['user_id'] = $user_data['user_id'];
        $user['emp_id'] = $user_data['emp_id'];
        $user['name'] = $user_data['name'];
        $user['date_of_birth'] = $user_data['date_of_birth'];
        $user['email'] = $user_data['email'];
        $user['role'] = $user_data['role'];
        $user['profile_pic'] = $user_data['profile_pic'];
        $user['is_registered'] = $user_data['is_registered'];
        $user['updated_at'] = $user_data['updated_at'];
        $user['created_at'] = $user_data['created_at'];
        
        return $user;
    }
    
    
}