<?php

/*
 * Holds the functions that make the get/insert data from/to the db 
 */

namespace App\Helpers;
use App\Helpers\DatabaseHandler;
use Illuminate\Support\Facades\File;

/**
 * Holds the functions that make the get/insert data from/to the db 
 *
 * @author bruno.emuron
 */
class DbOperation {
    function __construct()
    {
    }
	
    // Retrieves all languages
	public static function GetCategories()
	{
	// Build SQL query
	$sql = 'CALL get_categories()';
	
	// Execute the query and return the results
	return DatabaseHandler::GetAll($sql);
	}
	
	// Retrieves the featured content
	public static function GetFeaturedContent(){
        // Build SQL query
        $sql = 'CALL get_featured_content()';
        
        // Execute the query and return the results
        return DatabaseHandler::GetAll($sql);
    }
    
    // Retrieves the recent tasks
	public static function GetRecentTasks(){
        // Build SQL query
        $sql = 'CALL get_recent_tasks()';
        
        // Execute the query and return the results
        return DatabaseHandler::GetAll($sql);
    }
	
 
    //Method to create a new user
    public static function RegisterUser($name, $date_of_birth, $gender, $email, $password, $phoneNumber)
    {
        if (!self::isUserExist($email)) {
            $uuid = uniqid('', true);
            $hash = self::Hash($password);
            $encrypted_password = $hash["encrypted"]; // encrypted password
            $salt = $hash["salt"]; // salt
			
	   // Build the SQL query
           $sql = 'CALL register_user(:name, :date_of_birth, :gender, :email, :password, :phoneNumber, :salt)';

           // Build the parameters array
          $params = array (':name' => $name, ':date_of_birth' => $date_of_birth, ':gender' => $gender, ':email' => $email,
                     ':password' => $encrypted_password, 
                     ':phoneNumber' => $phoneNumber, ':salt' => $salt);       
                     
            // Execute the query and return the results
		    $userID = DatabaseHandler::GetRow($sql, $params);
		
		    $user = array();
		    
		    if($userID){
		        $user['user_id'] = $userID['user_id']; 
		        return $user;
		    }else{
		        return USER_CREATION_FAILED;
		    }
        }
        return USER_EXIST;
    }
 
    //Method for user login
    public static function LoginUser($email, $password)
    {
        //$password = md5($pass);
		$hashed_password = self::Hash($password);
		
		// Build the SQL query
           $sql = 'CALL login_user(:email, :password)';

           // Build the parameters array
          $params = array (':email' => $email,
                     ':password' => $hashed_password);
					 
        // Execute the query and return the results
        return DatabaseHandler::GetRow($sql, $params);
    }
    
    //Method to log out a user when they click on the log out app button
    //deletes their fcm and api access tokens
    public static function logOutUser($user_id)
    {
		// Build SQL query
		$sql = 'CALL delete_access_tokens(:user_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    // save the user's phone number
    public static function savePhoneNumber($user_id, $phone_number) {
        
        // Build the SQL query
        $sql = 'CALL save_phone_number(:user_id, :phone_number)';

        // Build the parameters array
        $params = array (':user_id' => $user_id, ':phone_number' => $phone_number);
 
        if (DatabaseHandler::Execute($sql, $params)) {
            // phone number saved
            $response["error"] = false;
        } else {
            // Failed to save phone number
            $response["error"] = true;
        }
 
        return $response;
    }
    
    //Method to save the user's profile pic
    public static function saveProfilePic($user_id, $img_name)
    {
		// Build SQL query
		$sql = 'CALL save_profile_pic_name(:user_id, :img_name)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':img_name' => $img_name);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    // save the user's nin
    public static function saveNIN($user_id, $nin) {
        
        // Build the SQL query
        $sql = 'CALL save_nin(:user_id, :nin)';

        // Build the parameters array
        $params = array (':user_id' => $user_id, ':nin' => $nin);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to save the user's nin pic
    public static function saveNinPic($user_id, $img_name)
    {
		// Build SQL query
		$sql = 'CALL save_nin_pic_name(:user_id, :img_name)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':img_name' => $img_name);
		
		// Execute the query and return the results
		$picID = DatabaseHandler::GetRow($sql, $params);
		
		$pic = array();
		$pic['pic_id'] = $picID['pic_id'];
		
		return $pic;
    }
    
    //get the user's nin
    public static function getUserNin($user_id)
    {
        
        // Build SQL query
		$sql = 'CALL get_user_nin(:user_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the user's nin
        return $result;
    }
    
    //Method to delete the user's nin pic
    public static function deleteNinPic($user_id, $pic_id)
    {
        // Build the SQL query
        //get the name of the image to be deleted
        $sql = 'CALL get_nin_pic_name(:pic_id)';

        // Build the parameters array
        $params = array (':pic_id' => $pic_id);

        // Execute the query and return the results
        $img_name = DatabaseHandler::GetRow($sql, $params);
        
        //$image_path = SITE_ROOT . '/assets/images/user_signup_pics/' . $img_name['image_name'];
        $image_path = base_path('public/assets/images/user_signup_pics/' . $img_name['image_name']);
        
        if (unlink($image_path)) {
            //if file has been unlinked/deleted, remove the record from the db too
            
            // Build SQL query
    		$sql = 'CALL delete_nin_pic(:user_id, :pic_id)';
    		
    		// Build the parameters array
            $params = array (':user_id' => $user_id, ':pic_id' => $pic_id);
    		
    		// Execute the query and return the results
    		return DatabaseHandler::Execute($sql, $params);
        } 
        else {  
            return false;
        }
        
    }
    
    //Method to save the uploaded user portfolio image's names in the db
    public static function savePortfolioImageNames($user_id, $img_name)
    {
		// Build SQL query
		$sql = 'CALL save_port_img_name(:user_id, :img_name, :added_on)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':img_name' => $img_name,
        ':added_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$picID = DatabaseHandler::GetRow($sql, $params);
		
		$pic = array();
		$pic['pic_id'] = $picID['pic_id'];
		
		return $pic;
    }
    
    //Method to save the user skills in the db
    public static function saveUserSkills($user_id, $skill)
    {
		// Build SQL query
		$sql = 'CALL save_user_skill(:user_id, :skill, :created_on)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':skill' => $skill, ':created_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$skillID = DatabaseHandler::GetRow($sql, $params);
		
		$savedSkill = array();
		$savedSkill['skill_id'] = $skillID['skill_id'];
		
		return $savedSkill;
    }
    
    //Method to save the user language in the db
    public static function saveUserLang($user_id, $language)
    {
		// Build SQL query
		$sql = 'CALL save_user_lang(:user_id, :lang, :added_on)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':lang' => $language,':added_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$langID = DatabaseHandler::GetRow($sql, $params);
		
		$savedLang = array();
		$savedLang['lang_id'] = $langID['lang_id'];
		
		return $savedLang;
    }
    
    //Method to save the user education in the db
    public static function saveUserEdu($user_id, $edu)
    {
		// Build SQL query
		$sql = 'CALL save_user_edu(:user_id, :edu, :added_on)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':edu' => $edu,':added_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$eduID = DatabaseHandler::GetRow($sql, $params);
		
		$savedEdu = array();
		$savedEdu['edu_id'] = $eduID['edu_id'];
		
		return $savedEdu;
    }
    
    //Method to save the user work in the db
    public static function saveUserWork($user_id, $work)
    {
		// Build SQL query
		$sql = 'CALL save_user_work(:user_id, :work, :added_on)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':work' => $work,':added_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$workID = DatabaseHandler::GetRow($sql, $params);
		
		$savedWork = array();
		$savedWork['work_id'] = $workID['work_id'];
		
		return $savedWork;
    }
    
    //Method to delete the user portfolio images
    public static function deletePortfolioImages($user_id, $pic_id)
    {
	
		// Build the SQL query
        //get the name of the image to be deleted
        $sql = 'CALL get_port_pic_name(:pic_id)';

        // Build the parameters array
        $params = array (':pic_id' => $pic_id);

        // Execute the query and return the results
        $img_name = DatabaseHandler::GetRow($sql, $params);
        //SITE_ROOT
        
        $image_path = base_path('public/assets/images/user_signup_pics/' . $img_name['image_name']);
        
        if (unlink($image_path)) { 
            //if file has been unlinked/deleted, remove the record from the db too
            
            // Build SQL query
    		$sql = 'CALL delete_port_images(:user_id, :pic_id)';
    		
    		// Build the parameters array
            $params = array (':user_id' => $user_id, ':pic_id' => $pic_id);
    		
    		// Execute the query and return the results
    		return DatabaseHandler::Execute($sql, $params);
        } 
        else { 
            //echo ("$image_path has been deleted"); 
            return false;
        }
    }
    
    //Method to delete the user skill currently saved
    public static function deleteUserSkill($user_id, $skill_id)
    {
		// Build SQL query
		$sql = 'CALL delete_user_skill(:user_id, :skill_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':skill_id' => $skill_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete the user work currently saved
    public static function deleteUserWork($user_id, $work_id)
    {
		// Build SQL query
		$sql = 'CALL delete_user_work(:user_id, :work_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':work_id' => $work_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete the user edu currently saved
    public static function deleteUserEdu($user_id, $edu_id)
    {
		// Build SQL query
		$sql = 'CALL delete_user_edu(:user_id, :edu_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':edu_id' => $edu_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete the user lang currently saved
    public static function deleteUserLang($user_id, $lang_id)
    {
		// Build SQL query
		$sql = 'CALL delete_user_lang(:user_id, :lang_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':lang_id' => $lang_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //get the user skills
    public static function getUserSkills($user_id){
        //build the sql query
        $sql = 'CALL get_user_skills(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get the user work
    public static function getUserWork($user_id){
        //build the sql query
        $sql = 'CALL get_user_work(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get the user education
    public static function getUserEdu($user_id){
        //build the sql query
        $sql = 'CALL get_user_edu(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get the user languages
    public static function getUserLang($user_id){
        //build the sql query
        $sql = 'CALL get_user_lang(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get user portfolio images
    public static function getUserPortImages($user_id){
        //build the sql query
        $sql = 'CALL get_user_portfolio(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get user nin images
    public static function getUserNinImages($user_id){
        //build the sql query
        $sql = 'CALL get_nin_images(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //Method to update user details
    public static function updateUserDetails($user_id,
                $username, $location, $gender,
                $email, $dob, $about_user)
    {
        
        // Build SQL query
		$sql = 'CALL update_user_details(:user_id, :username, :location, :gender, :email, :dob, :description, :updated_at)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, 
		':username' => $username, ':location' => $location, ':gender' => $gender, ':email' => $email, ':dob' => $dob, ':description' => $about_user, ':updated_at' => date('Y-m-d H:i:s'));
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    
    //Method to post a job by the user
    public static function PostJob($posted_by, $name, $description, $location,
    $must_have_one, $must_have_two, $must_have_three, $is_job_remote, $image_1, $category_id)
    {
		// Build SQL query
		$sql = 'CALL create_job(:category_id, :posted_by, :name, :description,
		:location, :must_have_one, :must_have_two, :must_have_three, :is_job_remote, :image_1)';
		
		// Build the parameters array
        $params = array (':category_id' => $category_id, ':posted_by' => $posted_by, 
		':name' => $name, ':description' => $description, ':location' => $location, ':must_have_one' => $must_have_one, ':must_have_two' => $must_have_two, ':must_have_three' => $must_have_three, ':is_job_remote' => $is_job_remote, ':image_1' => $image_1);
		
		// Execute the query and return the results
		$jobID = DatabaseHandler::GetRow($sql, $params);
		
		$job = array();
		$job['job_id'] = $jobID['job_id'];
		
		return $job;
					 
		// Execute the query
		//if (DatabaseHandler::Execute($sql, $params)){
			//return JOB_POSTED;
			//return DatabaseHandler::GetRow($sql, $params);
		//}else{
		//return JOB_POST_FAILED;
		//}
    }
    
    //Method to create a job. only job name, user id, job desc and category 
    //are needed to create the job
    public static function CreateJob($posted_by, $name, $description, $category_id)
    {
		// Build SQL query
		$sql = 'CALL create_job(:category_id, :posted_by, :name, :description, :posted_on)';
		
		// Build the parameters array
        $params = array (':category_id' => $category_id, ':posted_by' => $posted_by, 
		':name' => $name, ':description' => $description, ':posted_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		$jobID = DatabaseHandler::GetRow($sql, $params);
		
		$job = array();
		$job['job_id'] = $jobID['job_id'];
		
		return $job;
    }
    
    
    //Method to create a job by the user without an image
    public static function CreateJobWithoutImage($posted_by, $name, $description, $location,
    $must_have_one, $must_have_two, $must_have_three, $is_job_remote, $category_id)
    {
		// Build SQL query
		$sql = 'CALL create_job_without_image(:category_id, :posted_by, :name, :description,
		:location, :must_have_one, :must_have_two, :must_have_three, :is_job_remote)';
		
		// Build the parameters array
        $params = array (':category_id' => $category_id, ':posted_by' => $posted_by, 
		':name' => $name, ':description' => $description, ':location' => $location, ':must_have_one' => $must_have_one, ':must_have_two' => $must_have_two, ':must_have_three' => $must_have_three, ':is_job_remote' => $is_job_remote);
		
		// Execute the query and return the results
		$jobID = DatabaseHandler::GetRow($sql, $params);
		
		$job = array();
		$job['job_id'] = $jobID['job_id'];
		
		return $job;
    }
    
    
    //Method to update job details with an image attached
    public static function updateJobDetails($job_id,
                $job_title, $description, $location,
                $must_have_one, $must_have_two, $must_have_three,
                $is_job_remote)
    {
        
        // Build SQL query
		$sql = 'CALL update_job_details(:job_id, :name, :description,
		:location, :must_have_one, :must_have_two, :must_have_three, :is_job_remote)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, 
		':name' => $job_title, ':description' => $description, ':location' => $location, ':must_have_one' => $must_have_one, ':must_have_two' => $must_have_two, ':must_have_three' => $must_have_three, ':is_job_remote' => $is_job_remote);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to save the uploaded job pictures's names in the db
    public static function saveJobPicNames($job_id, $img_name)
    {
		// Build SQL query
		$sql = 'CALL save_img_name(:job_id, :img_name)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':img_name' => $img_name);
		
		// Execute the query and return the results
		$picID = DatabaseHandler::GetRow($sql, $params);
		
		$pic = array();
		$pic['pic_id'] = $picID['pic_id'];
		
		return $pic;
    }
    
    //Method to save the uploaded ad images
    public static function saveAdImageNames($advert_id, $img_name)
    {
		// Build SQL query
		$sql = 'CALL save_ad_img_name(:advert_id, :img_name, :added_on)';
		
		// Build the parameters array
        $params = array (':advert_id' => $advert_id, ':img_name' => $img_name,
        ':added_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete the job pic
    public static function deleteJobImage($job_id, $pic_id)
    {
        // Build the SQL query
        //get the name of the image to be deleted
        $sql = 'CALL get_job_pic_name(:pic_id)';

        // Build the parameters array
        $params = array (':pic_id' => $pic_id);

        // Execute the query and return the results
        $img_name = DatabaseHandler::GetRow($sql, $params);
        
        //$image_path = SITE_ROOT . '/assets/images/job_pics/' . $img_name['image_name'];
        $image_path = base_path('public/assets/images/job_pics/' . $img_name['image_name']);
        
        if (unlink($image_path)) { 
            //if file has been unlinked/deleted, remove the record from the db too
            
            // Build SQL query
    		$sql = 'CALL delete_job_pic(:job_id, :pic_id)';
    		
    		// Build the parameters array
            $params = array (':job_id' => $job_id, ':pic_id' => $pic_id);
    		
    		// Execute the query and return the results
    		return DatabaseHandler::Execute($sql, $params);
        } 
        else { 
            return false;
        }
        
    }
    
    //Method to update a job by the user when an image is attached
    public static function updateJobDetailsWithoutImage($job_id, $job_title,            $description, $location,
                $must_have_one, $must_have_two, $must_have_three,
                $is_job_remote)
    {
		// Build SQL query
		$sql = 'CALL update_job_without_image(:job_id, :name, :description,
		:location, :must_have_one, :must_have_two, :must_have_three, :is_job_remote)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id,
		':name' => $job_title, ':description' => $description, ':location' => $location, ':must_have_one' => $must_have_one, ':must_have_two' => $must_have_two, ':must_have_three' => $must_have_three, ':is_job_remote' => $is_job_remote);
		
		// Execute the query and return the results
	    return DatabaseHandler::Execute($sql, $params);
    }
    
    
    //Method to update the job date and time
    public static function updateJobDate($job_id, $job_date, $job_time)
    {
		// Build SQL query
		$sql = 'CALL update_job_date_time(:job_id, :job_date, :job_time)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':job_date' => $job_date, ':job_time' => $job_time);
		
		// Execute the query and return the results
	    return DatabaseHandler::Execute($sql, $params);
    }
    
    
    //Method to update the job budget
    public static function updateJobBudget($job_id, $total_budget, $price_per_hr, $total_hrs, $est_tot_budget, $job_status)
    {
		// Build SQL query
		$sql = 'CALL update_job_budget(:job_id, :total_budget, :price_per_hr, :total_hrs, :est_tot_budget, :job_status, :posted_on)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':total_budget' => $total_budget, ':price_per_hr' => $price_per_hr, ':total_hrs' => $total_hrs, ':est_tot_budget' => $est_tot_budget, ':job_status' => $job_status, ':posted_on' => date('Y-m-d H:i:s'));
		
		// Execute the query and return the results
	    return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to report issues users notice with jobs
    public static function ReportJob($job_id, $reported_by, $comment)
    {
		// Build SQL query
		$sql = 'CALL report_job(:job_id, :reported_by, :comment)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':reported_by' => $reported_by, 
		':comment' => $comment);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to cancel the task posted by the user
    public static function CancelTask($job_id, $user_id, $isPaymentReceived, $finalJobCost)
    {
        if($isPaymentReceived == 1){
            //compute the user's new wallet balance
            $res = SELF::getWalletBalance($user_id);
            $job_cost = intval($finalJobCost);
                
            $walletBal = $res['walletBalance'];
            
            $newBal = $walletBal + $job_cost; 
            
            $trx_type = "wallet top up";
            $funds_added = $job_cost;
        
            // Build SQL query
            $walletSql = 'CALL set_wallet_balance(:user_id, :funds_added, :new_balance, :trx_type, :trx_date)';
            
            // Build the parameters array
            $param = array (':user_id' => $user_id, ':funds_added' => $funds_added, ':new_balance' => $newBal, ':trx_type' => $trx_type, ':trx_date' => date('Y-m-d H:i:s'));
            
            DatabaseHandler::GetRow($walletSql, $param);
        }
        

		// Build SQL query
		$sql = 'CALL cancel_task(:job_id, :user_id)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':user_id' => $user_id);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //get draft jobs for this user
    //jobs they are still creating
    public static function getJobsByStatus($user_id, $status){
        //build the sql query
        $sql = 'CALL get_jobs_by_status(:user_id, :status)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':status' => $status);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get id images
    public static function getIdImages($user_id){
        //build the sql query
        $sql = 'CALL get_id_images(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get ad images
    public static function getAdImages($ad_id){
        //build the sql query
        $sql = 'CALL get_ad_images(:ad_id)';
        
        //build the parameters array
        $params = array (':ad_id' => $ad_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //Method to delete the user's ad pic
    public static function deleteAdPic($pic_id)
    {
        // Build the SQL query
        //get the name of the image to be deleted
        $sql = 'CALL get_ad_pic_name(:pic_id)';

        // Build the parameters array
        $params = array (':pic_id' => $pic_id);

        // Execute the query and return the results
        $img_name = DatabaseHandler::GetRow($sql, $params);
        
        //$image_path = SITE_ROOT . '/assets/images/ads/' . $img_name['image_name'];
        $image_path = base_path('public/assets/images/ads/' . $img_name['image_name']);
        
        if (unlink($image_path)) {
            //if file has been unlinked/deleted, remove the record from the db too
            
            // Build SQL query
    		$sql = 'CALL delete_ad_pic(:pic_id)';
    		
    		// Build the parameters array
            $params = array (':pic_id' => $pic_id);
    		
    		// Execute the query and return the results
    		return DatabaseHandler::Execute($sql, $params);
        } 
        else {  
            return false;
        }
        
    }
    
    //gets details of a single advert
    public static function getAdDetails($ad_id){
        //build the sql query
        $sql = 'CALL get_ad_details(:ad_id)';
        
        //build the params array
        $params = array(':ad_id' => $ad_id);
        
        $ad_data = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the results
        return $ad_data;
    }
    
    //get draft job details for this user
    //jobs they are still creating
    //gets details of a single job
    public static function getJobDetailsByStatus($job_id){
        //build the sql query
        $sql = 'CALL get_job_details_by_status(:job_id)';
        
        //build the params array
        $params = array(':job_id' => $job_id);
        
        $job_data = DatabaseHandler::GetRow($sql, $params);
        
        $jobDetails = array();
        
        $jobDetails['category_id'] = $job_data['category_id'];
        $jobDetails['posted_by'] = $job_data['posted_by'];
	    $jobDetails['name'] = $job_data['name'];
        $jobDetails['description'] = $job_data['description'];
        $jobDetails['must_have_one'] = $job_data['must_have_one'];
        $jobDetails['must_have_two'] = $job_data['must_have_two'];
        $jobDetails['must_have_three'] = $job_data['must_have_three'];
        $jobDetails['is_job_remote'] = $job_data['is_job_remote'];
	    $jobDetails['location'] = $job_data['location'];
	    $jobDetails['image_1'] = $job_data['image_1'];
	    $jobDetails['job_date'] = $job_data['job_date'];
	    $jobDetails['job_time'] = $job_data['job_time'];
	    $jobDetails['total_budget'] = $job_data['total_budget'];
	    $jobDetails['price_per_hr'] = $job_data['price_per_hr'];
	    $jobDetails['total_hrs'] = $job_data['total_hrs'];
	    $jobDetails['est_tot_budget'] = $job_data['est_tot_budget'];
	    $jobDetails['job_status'] = $job_data['job_status'];
	    $jobDetails['completed_by'] = $job_data['completed_by'];
	    $jobDetails['posted_on'] = $job_data['posted_on'];
	    $jobDetails['completed_on'] = $job_data['completed_on'];
	    $jobDetails['is_payment_received'] = $job_data['is_payment_received'];
        
        //execute the query and return the results 
        return $jobDetails;
    }
    
    //get all employees
    public static function getAllEmployees(){
        //build the sql query
        $sql = 'CALL get_all_employees()';
        
        //build the parameters array
        $params = array ();
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }

    //get a single employee
    public static function getSingleEmployee($emp_id){
        //build the sql query
        $sql = 'CALL get_single_employee(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $emp_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get job details for this user
    //gets details of a single job
    public static function getJobDetails($job_id){
        //build the sql query
        $sql = 'CALL get_job_details(:job_id)';
        
        //build the params array
        $params = array(':job_id' => $job_id);
        
        $job_data = DatabaseHandler::GetRow($sql, $params);
        
        $jobDetails = array();
        
        $jobDetails['category_id'] = $job_data['category_id'];
        $jobDetails['job_id'] = $job_data['job_id'];
        $jobDetails['posted_by'] = $job_data['posted_by'];
	    $jobDetails['name'] = $job_data['name'];
        $jobDetails['description'] = $job_data['description'];
        $jobDetails['must_have_one'] = $job_data['must_have_one'];
        $jobDetails['must_have_two'] = $job_data['must_have_two'];
        $jobDetails['must_have_three'] = $job_data['must_have_three'];
        $jobDetails['is_job_remote'] = $job_data['is_job_remote'];
	    $jobDetails['location'] = $job_data['location'];
	    $jobDetails['image_1'] = $job_data['image_1'];
	    $jobDetails['job_date'] = $job_data['job_date'];
	    $jobDetails['job_time'] = $job_data['job_time'];
	    $jobDetails['total_budget'] = $job_data['total_budget'];
	    $jobDetails['price_per_hr'] = $job_data['price_per_hr'];
	    $jobDetails['total_hrs'] = $job_data['total_hrs'];
	    $jobDetails['est_tot_budget'] = $job_data['est_tot_budget'];
	    $jobDetails['job_status'] = $job_data['job_status'];
	    $jobDetails['completed_by'] = $job_data['completed_by'];
	    $jobDetails['posted_on'] = $job_data['posted_on'];
	    $jobDetails['completed_on'] = $job_data['completed_on'];
	    $jobDetails['offers_received'] = $job_data['offers_received'];
	    $jobDetails['is_payment_received'] = $job_data['is_payment_received'];
        
        //execute the query and return the results
        return $jobDetails;
    }
    
    
    //Method to get the name of the user's profile pic
    public static function getUserProfilePic($user_id)
    {
        //build sql query
        $sql = 'CALL get_user_profile_pic(:user_id)';
        
        //build the params array
        $params = array(':user_id' => $user_id);
        
        return DatabaseHandler::GetOne($sql, $params);
    }
    
    //Method to get the name of the user's name
    public static function getUserName($user_id)
    {
        //build sql query
        $sql = 'CALL get_user_name(:user_id)';
        
        //build the params array
        $params = array(':user_id' => $user_id);
        
        return DatabaseHandler::GetOne($sql, $params);
    }
    
    //Method to get the phone number of the user
    public static function getUserPhoneNumber($user_id)
    {
        //build sql query
        $sql = 'CALL get_user_phone(:user_id)';
        
        //build the params array
        $params = array(':user_id' => $user_id);
        
        return DatabaseHandler::GetOne($sql, $params);
    }
    
    //count all the jobs in the db
    //returns a field called all_jobs_count
    public static function CountAllJobs()
    {
    // Query that returns the number of jobs in the db
    $sql = 'CALL count_all_jobs()';
    
    return DatabaseHandler::GetOne($sql);
    }
    
    //get all jobs for browsing
    // 0 - draft, 1 - posted, 2 - assigned, 3 - offers, 4 - in progress, 5 - complete
  public static function GetAllJobsForBrowsing($pageNo, $pageSize)
  {
    // Query that returns the number of jobs in the db
    //$sql = 'CALL count_all_jobs()';

    // Calculate the number of pages required to display the products
    //$rHowManyPages = DbOperation::HowManyPages($sql, $params);
    // Calculate the start item

    $start_item = 0;
    if($pageNo == 0){
        $start_item = 0;
    }else{
        $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
    }
    
    
    // Retrieve the list of products
    $sql = 'CALL get_all_jobs_for_browsing(
                   :jobs_per_page, :start_item)';

    // Build the parameters array
    $params = array (
      ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);

    // Execute the query and return the results
    return DatabaseHandler::GetAll($sql, $params);
  }
  
  //get all ads for browsing
    // 0 - draft, 1 - posted
  public static function GetAllAdsForBrowsing($pageNo, $pageSize)
  {
    // Query that returns the number of jobs in the db
    //$sql = 'CALL count_all_jobs()';

    // Calculate the number of pages required to display the products
    //$rHowManyPages = DbOperation::HowManyPages($sql, $params);
    // Calculate the start item

    $start_item = 0;
    if($pageNo == 0){
        $start_item = 0;
    }else{
        $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
    }
    
    
    // Retrieve the list of products
    $sql = 'CALL get_all_ads_for_browsing(
                   :jobs_per_page, :start_item)';

    // Build the parameters array
    $params = array (
      ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);

    // Execute the query and return the results
    return DatabaseHandler::GetAll($sql, $params);
  }
  
  public static function GetPosterAdsForBrowsing($posterId, $pageNo, $pageSize)
  {

    $start_item = 0;
    if($pageNo == 0){
        $start_item = 0;
    }else{
        $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
    }
    
    
    // Retrieve the list of products
    $sql = 'CALL get_poster_ads_for_browsing(:poster_id,
                   :ads_per_page, :start_item)';

    // Build the parameters array
    $params = array (':poster_id' => $posterId,
      ':ads_per_page' => $pageSize,
      ':start_item' => $start_item);

    // Execute the query and return the results
    return DatabaseHandler::GetAll($sql, $params);
  }
  
    //method to post a new ad
    public static function postNewAd($user_id, $ad_title, $description, $location, $price, $category_id){
    	// Build SQL query
    	$sql = 'CALL post_new_ad(:user_id, :ad_title, :description, :location, :price, :category_id, :posted_on)';
    	
    	// Build the parameters array
        $params = array (':user_id' => $user_id, ':ad_title' => $ad_title, 
    	':description' => $description, ':location' => $location, ':price' => $price, ':category_id' => $category_id, ':posted_on' => date('Y-m-d H:i:s'));
    	
    	// Execute the query and return the results
    	$result = DatabaseHandler::GetRow($sql, $params);
    	
    	$advert = array();
    	$advert['advert_id'] = $result['advert_id'];
    	
    	return $result['advert_id'];
    }
    
    //method to edit an ad
    public static function editAd($ad_id, $user_id, $ad_title, $description, $location, $price, $category_id){
    	// Build SQL query
    	$sql = 'CALL update_ad(:ad_id, :user_id, :ad_title, :description, :location, :price, :category_id, :updated_at)';
    	
    	// Build the parameters array
        $params = array (':ad_id' => $ad_id, ':user_id' => $user_id, ':ad_title' => $ad_title, 
    	':description' => $description, ':location' => $location, ':price' => $price, ':category_id' => $category_id, ':updated_at' => date('Y-m-d H:i:s'));
    	
    	// Execute the query and return the results
    	return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete the ad by the user
    public static function deleteAd($ad_id, $user_id)
    {
        // Build the SQL query
        //get the names of the images to be deleted
        $sql = 'CALL get_ad_images(:ad_id)';

        // Build the parameters array
        $params = array (':ad_id' => $ad_id);

        // Execute the query and return the results
        $img_names = DatabaseHandler::GetAll($sql, $params);

        for ($i = 0; $i < count($img_names); $i++){
            $image_path = base_path('public/assets/images/ads/' . $img_names[$i]['image_name']);
            unlink($image_path);
        }

        // Build SQL query
        $sql = 'CALL delete_ad(:ad_id)';
        
        // Build the parameters array
        $params = array (':ad_id' => $ad_id);
        
        // Execute the query and return the results
        return DatabaseHandler::Execute($sql, $params);
        
    }
    
    //Method to report issues users notice with ads
    public static function ReportAd($ad_id, $reported_by, $comment)
    {
		// Build SQL query
		$sql = 'CALL report_ad(:ad_id, :reported_by, :comment, :reported_on)';
		
		// Build the parameters array
        $params = array (':ad_id' => $ad_id, ':reported_by' => $reported_by, 
		':comment' => $comment, ':reported_on' => date('Y-m-d H:i:s'));
		
		return DatabaseHandler::Execute($sql, $params);
    } 
    
    //toggle user like / unlike ad
    public static function toggleAdLike($userId, $adId, $value)
    {
        
        // Build SQL query
		$sql = 'CALL toggle_ad_like(:userId, :adId, :value)';
		
		// Build the parameters array
        $params = array (':userId' => $userId, ':adId' => $adId, ':value' => $value);
        
        return DatabaseHandler::Execute($sql, $params);
    }
  
  //get all pros for browsing
    public static function GetAllProsForBrowsing($pageNo, $pageSize)
    {
      // Query that returns the number of jobs in the db
      //$sql = 'CALL count_all_jobs()';
  
      // Calculate the number of pages required to display the products
      //$rHowManyPages = DbOperation::HowManyPages($sql, $params);
      // Calculate the start item
  
      $start_item = 0;
      if($pageNo == 0){
          $start_item = 0;
      }else{
          $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
      }
      
      
      // Retrieve the list of products
      $sql = 'CALL get_all_pros_for_browsing(
                     :pros_per_page, :start_item)';
  
      // Build the parameters array
      $params = array (
        ':pros_per_page' => $pageSize,
        ':start_item' => $start_item);
  
      // Execute the query and return the results
      return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get messages for the user
    public static function getMessagesForUser($user_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_messages_for_user(:user_id, :msgs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':msgs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get messages between two users
    public static function getMessagesBetweenUsers($to_id, $from_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_messages_between_users(:to_id, :from_id, :msgs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':to_id' => $to_id, ':from_id' => $from_id, ':msgs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //save a message sent
    public static function saveSentMessage($from_id, $to_id, $ad_id, $message)
    {
        
        // Build SQL query
		$sql = 'CALL save_sent_message(:from_id, :to_id, :ad_id,
			:message, :created_at)';
		
		// Build the parameters array
        $params = array (':from_id' => $from_id, 
		':to_id' => $to_id, ':ad_id' => $ad_id, ':message' => $message, ':created_at' => date('Y-m-d H:i:s'));
		
		$result = DatabaseHandler::GetRow($sql, $params);
		
        return $result;
    }
    
    //save a user's verfied payment phone
    public static function saveVerifiedPayPhone($user_id, $phone_number)
    {
        
        // Build SQL query
		$sql = 'CALL save_payment_number(:user_id, :phone_number)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, 
		':phone_number' => $phone_number);
		
		$result = DatabaseHandler::Execute($sql, $params);
		
        return $result;
    }
  
  // Search the catalog
    public static function SearchForJobs($searchString, $allWords,
    $pageNo, $pageSize)
    {
        //The search result will be an array of this form
        $search_result = array ('accepted_words' => array (),
        'ignored_words' => array (),
        'job_search_results' => array ());
        
        // Return void if the search string is void
        if (empty ($searchString))
        return $search_result;
        
        // Search string delimiters
        $delimiters = ',.; ';
        
        /* On the first call to strtok you supply the whole
        search string and the list of delimiters.
        It returns the first word of the string */
        $word = strtok($searchString, $delimiters);
        
        // Parse the string word by word until there are no more words
        while ($word)
    {
        // Short words are added to the ignored_words list from $search_result
        //FT_MIN_WORD_LEN = 4
        if (strlen($word) < env('FT_MIN_WORD_LEN'))
        $search_result['ignored_words'][] = $word;
        else
        $search_result['accepted_words'][] = $word;
        
        // Get the next word of the search string
        $word = strtok($delimiters);
    }
    // If there aren't any accepted words return the $search_result
    if (count($search_result['accepted_words']) == 0)
        return $search_result;
    
    // Build $search_string from accepted words list
    $search_string = '';
    
    // If $allWords is 'on' then we append a ' +' to each word
    if (strcmp($allWords, "on") == 0)
        $search_string = implode(" +", $search_result['accepted_words']);
    else
        $search_string = implode(" ", $search_result['accepted_words']);
    
    // Count the number of search results
    //$sql = 'CALL catalog_count_search_result(:search_string, :all_words)';
    
    //$params = array(':search_string' => $search_string,
    //':all_words' => $allWords);
    
    // Calculate the number of pages required to display the products
    //$rHowManyPages = Catalog::HowManyPages($sql, $params);
    
    // Calculate the start item
    //$start_item = ($pageNo - 1) * JOBS_PER_PAGE;
    
    // Retrieve the list of matching jobs
    $sql = 'CALL search_for_jobs(:search_string, :all_words,
    :jobs_per_page, :start_item)';
    
    // Build the parameters array
    $params = array (':search_string' => $search_string,
    ':all_words' => $allWords,
    ':jobs_per_page' => $pageSize,
    ':start_item' => $pageNo);
    
    // Execute the query
    //$search_result['job_search_results'] = DatabaseHandler::GetAll($sql, $params);
    
    // Return the results
    return DatabaseHandler::GetAll($sql, $params);
    }
    
    // return ads similar to what the user was viewing
    public static function getSimilarAds($category_id, $title, $allWords,
    $pageNo, $pageSize)
    {
        //The search result will be an array of this form
        $search_result = array ('accepted_words' => array (),
        'ignored_words' => array (),
        'same_ads_results' => array ());
        
        $start_item = 0;
          if($pageNo == 0){
              $start_item = 0;
          }else{
              $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
          }
        
        // Return void if the search string is void
        if (empty ($title))
        return $search_result;
        
        // Search string delimiters
        $delimiters = ',.; ';
        
        /* On the first call to strtok you supply the whole
        search string and the list of delimiters.
        It returns the first word of the string */
        $word = strtok($title, $delimiters);
        
        // Parse the string word by word until there are no more words
        while ($word){
            // Short words are added to the ignored_words list from $search_result
            //FT_MIN_WORD_LEN = 4
            if (strlen($word) < env('FT_MIN_WORD_LEN'))
            $search_result['ignored_words'][] = $word;
            else
            $search_result['accepted_words'][] = $word;
            
            // Get the next word of the search string
            $word = strtok($delimiters);
        }
    
    $search_string = '';
    
    // If there aren't any accepted words return the $search_result
    if (count($search_result['accepted_words']) > 0){
        // Build $search_string from accepted words list
    
        // If $allWords is 'on' then we append a ' +' to each word
        if (strcmp($allWords, "on") == 0){
            $search_string = implode(" +", $search_result['accepted_words']);
        }else{
            $search_string = implode(" ", $search_result['accepted_words']);
        }
    }else{
        return $search_string = $title;
    }
    
    // Retrieve the list of matching jobs
    $sql = 'CALL get_similar_ads(:category_id, :search_string,
    :ads_per_page, :start_item)';
    
    // Build the parameters array
    $params = array (':category_id' => $category_id, ':search_string' => $search_string, ':ads_per_page' => $pageSize, ':start_item' => $start_item);
    
    // Execute the query
    // Return the results
    return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get all jobs for browsing
    public static function GetAllJobs(){
        //build the sql query
        $sql = 'CALL get_all_jobs_for_browsing()';
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    /* Calculates how many pages of jobs could be filled by the
     number of jobs returned by the $countSql query */
  public static function HowManyPagesForResults($countSql, $countSqlParams)
  {
    // Create a hash for the sql query
    $queryHashCode = md5($countSql . var_export($countSqlParams, true));
    
    // Verify if we have the query results in cache
    if (isset ($_SESSION['last_count_hash']) &&
        isset ($_SESSION['how_many_pages']) &&
        $_SESSION['last_count_hash'] === $queryHashCode)
    {
      // Retrieve the the cached value
      $how_many_pages = $_SESSION['how_many_pages'];
    }
    else
    {
      // Execute the query
      $items_count = DatabaseHandler::GetOne($countSql, $countSqlParams);

      // Calculate the number of pages
      $how_many_pages = ceil($items_count / env('JOBS_PER_PAGE'));

      // Save the query and its count result in the session
      $_SESSION['last_count_hash'] = $queryHashCode;
      $_SESSION['how_many_pages'] = $how_many_pages;
    }

    // Return the number of pages    
    return $how_many_pages;
  }
  
  /* Calculates how many pages of pros could be filled by the
     number of pros returned by the $countSql query */
 public static function HowManyPagesForProsResults($countSql, $countSqlParams)
 {
   // Create a hash for the sql query
   $queryHashCode = md5($countSql . var_export($countSqlParams, true));
   
   // Verify if we have the query results in cache
   if (isset ($_SESSION['last_pro_count_hash']) &&
       isset ($_SESSION['how_many_pros_pages']) &&
       $_SESSION['last_pro_count_hash'] === $queryHashCode)
   {
     // Retrieve the the cached value
     $how_many_pages = $_SESSION['how_many_pros_pages'];
   }
   else
   {
     // Execute the query
     $items_count = DatabaseHandler::GetOne($countSql, $countSqlParams);

     // Calculate the number of pages
     $how_many_pages = ceil($items_count / env('JOBS_PER_PAGE'));

     // Save the query and its count result in the session
     $_SESSION['last_pro_count_hash'] = $queryHashCode;
     $_SESSION['how_many_pros_pages'] = $how_many_pages;
   }

   // Return the number of pages    
   return $how_many_pages;
 }
  
  /* Calculates how many pages of jobs could be filled by the
     number of jobs returned by the $countSql query */
  public static function HowManyPagesForSearchResults($searchString, $allWords)
  {
      // Query that returns the number of search results for jobs in the db
      //$sql = 'CALL count_search_result(:search_string, :all_words)';
      
      //$params = array(':search_string' => $search_string,
    //':all_words' => $allWords);
    
      // Execute the query
      //$items_count = DatabaseHandler::GetOne($sql, $params);
      $items_count = 10;

      // Calculate the number of pages
      $how_many_pages = ceil($items_count / env('JOBS_PER_PAGE'));
      
      // Return the number of pages    
    return $how_many_pages;
  }
  
  public static function HowManyPagesForSimilarAds($searchString, $countSql, $countSqlParams)
  {
      // Create a hash for the sql query
    $queryHashCode = md5($countSql . var_export($countSqlParams, true));
    
    // Verify if we have the query results in cache
    if (isset ($_SESSION['last_ad_count_hash']) &&
        isset ($_SESSION['how_many_similar_ads_pages']) &&
        $_SESSION['last_ad_count_hash'] === $queryHashCode)
    {
        // Retrieve the the cached value
        $how_many_pages = $_SESSION['how_many_similar_ads_pages'];
    }
    else
    {
        //The search result will be an array of this form
        $search_result = array ('accepted_words' => array (),
        'ignored_words' => array (),
        'same_ads_results' => array ());
        
        // Return void if the search string is void
        if (empty ($searchString))
        return $search_result;
        
        // Search string delimiters
        $delimiters = ',.; ';
        
        /* On the first call to strtok you supply the whole
        search string and the list of delimiters.
        It returns the first word of the string */
        $word = strtok($searchString, $delimiters);
        
        // Parse the string word by word until there are no more words
        while ($word){
            // Short words are added to the ignored_words list from $search_result
            //FT_MIN_WORD_LEN = 4
            if (strlen($word) < env('FT_MIN_WORD_LEN'))
            $search_result['ignored_words'][] = $word;
            else
            $search_result['accepted_words'][] = $word;
            
            // Get the next word of the search string
            $word = strtok($delimiters);
        }
    
        $search_string = '';
        
        // If there aren't any accepted words return the $search_result
        if (count($search_result['accepted_words']) > 0){
            // Build $search_string from accepted words list
        
            // append a ' +' to each word
            $search_string = implode(" +", $search_result['accepted_words']);
        }else{
            return $search_string = $searchString;
        }

        //$countSqlParams = array(':search_string' => $search_string);

        // Execute the query
        $items_count = DatabaseHandler::GetOne($countSql, $countSqlParams);

        // Calculate the number of pages
        $how_many_pages = ceil($items_count / env('JOBS_PER_PAGE'));

        // Save the query and its count result in the session
        $_SESSION['last_ad_count_hash'] = $queryHashCode;
        $_SESSION['how_many_similar_ads_pages'] = $how_many_pages;
    }

    // Return the number of pages    
    return $how_many_pages;
  }
    
    /* Calculates how many pages of jobs could be filled by the
     number of jobs returned by the $countSql query */
  private static function HowManyPages($countSql, $countSqlParams)
  {
    // Create a hash for the sql query 
    $queryHashCode = md5($countSql . var_export($countSqlParams, true));

    // Verify if we have the query results in cache
    if (isset ($_SESSION['last_count_hash']) &&
        isset ($_SESSION['how_many_pages']) &&
        $_SESSION['last_count_hash'] === $queryHashCode)
    {
      // Retrieve the the cached value
      $how_many_pages = $_SESSION['how_many_pages'];
    }
    else
    {
      // Execute the query
      $items_count = DatabaseHandler::GetOne($countSql, $countSqlParams);

      // Calculate the number of pages
      $how_many_pages = ceil($items_count / env('JOBS_PER_PAGE'));

      // Save the query and its count result in the session
      $_SESSION['last_count_hash'] = $queryHashCode;
      $_SESSION['how_many_pages'] = $how_many_pages;
    }

    // Return the number of pages    
    return $how_many_pages;
  }
    
    // Search the catalog
    public static function Search($searchString, $allWords,
    $pageNo, &$rHowManyPages)
    {
        //The search result will be an array of this form
        $search_result = array ('accepted_words' => array (),
        'ignored_words' => array (),
        'products' => array ());
        
        // Return void if the search string is void
        if (empty ($searchString))
        return $search_result;
        
        // Search string delimiters
        $delimiters = ',.; ';
        
        /* On the first call to strtok you supply the whole
        search string and the list of delimiters.
        It returns the first word of the string */
        $word = strtok($searchString, $delimiters);
        
        // Parse the string word by word until there are no more words
        while ($word)
    {
        // Short words are added to the ignored_words list from $search_result
        if (strlen($word) < env('FT_MIN_WORD_LEN'))
        $search_result['ignored_words'][] = $word;
        else
        $search_result['accepted_words'][] = $word;
        
        // Get the next word of the search string
        $word = strtok($delimiters);
    }
    // If there aren't any accepted words return the $search_result
    if (count($search_result['accepted_words']) == 0)
        return $search_result;
    
    // Build $search_string from accepted words list
    $search_string = '';
    
    // If $allWords is 'on' then we append a ' +' to each word
    if (strcmp($allWords, "on") == 0)
        $search_string = implode(" +", $search_result['accepted_words']);
    else
        $search_string = implode(" ", $search_result['accepted_words']);
    
    // Count the number of search results
    $sql = 'CALL catalog_count_search_result(:search_string, :all_words)';
    
    $params = array(':search_string' => $search_string,
    ':all_words' => $allWords);
    
    // Calculate the number of pages required to display the products
    $rHowManyPages = Catalog::HowManyPages($sql, $params);
    
    // Calculate the start item
    $start_item = ($pageNo - 1) * PRODUCTS_PER_PAGE;
    
    // Retrieve the list of matching products
    $sql = 'CALL catalog_search(:search_string, :all_words,
    :short_product_description_length,
    :products_per_page, :start_item)';
    
    // Build the parameters array
    $params = array (':search_string' => $search_string,
    ':all_words' => $allWords,
    ':short_product_description_length' =>
    SHORT_PRODUCT_DESCRIPTION_LENGTH,
    ':products_per_page' => PRODUCTS_PER_PAGE,
    ':start_item' => $start_item);
    
    // Execute the query
    $search_result['products'] = DatabaseHandler::GetAll($sql, $params);
    
    // Return the results
    return $search_result;
    }
    
    //Method to post the offer made for a job
    public static function postOfferMade($amount_offered, $offer_message, $user_id, $job_id)
    {
        
        // Build SQL query
		$sql = 'CALL save_offer(:amount_offered, :offer_message,
			:user_id, :job_id, :last_edited_on)';
		
		// Build the parameters array
        $params = array (':amount_offered' => $amount_offered, 
		':offer_message' => $offer_message, ':user_id' => $user_id, ':job_id' => $job_id, ':last_edited_on' => date('Y-m-d H:i:s'));
		
		$result = DatabaseHandler::GetRow($sql, $params);
		
        return $result;
    }
    
    //Method to update the offer made for a job
    public static function UpdateOfferMade($offer_id, $amount_offered, $offer_message, $edit_count)
    {
        
        // Build SQL query
		$sql = 'CALL update_offer(:offer_id, :amount_offered, :offer_message, :edit_count, :last_edited_on)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':amount_offered' => $amount_offered, 
		':offer_message' => $offer_message, ':edit_count' => $edit_count, ':last_edited_on' => date('Y-m-d H:i:s'));
		
		$result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and the name of the fixer
        return $result;
    }
    
    //get ofers made by fixer for jobs
    public static function getOffersMade($user_id, $pageNo, $pageSize){
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }

        //build the sql query
        $sql = 'CALL get_offers_made_by_fixer(:user_id, :jobs_per_page, :start_item)';

        // Build the parameters array
        $params = array (':jobs_per_page' => $pageSize,
        ':start_item' => $start_item);

        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get ofers accepted by poster for the fixer to see
    public static function getOffersAcceptedForFixer($user_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_offers_accepted_for_fixer(:user_id, :jobs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get ofers accepted by this poster for the poster to see
    public static function getOffersAcceptedForPoster($user_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_offers_accepted_by_poster(:user_id, :jobs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //getting all the jobs which have offers made for this user/poster
    public static function getOffersForJobs($user_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_offers_for_poster_jobs(:user_id, :jobs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //getting all the offers a job by this user/poster has received
    public static function getOffersForSingleJob($user_id, $job_id, $pageNo, $pageSize){
        
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        $sql = 'CALL get_offers_for_single_job(:user_id, :job_id, :jobs_per_page, :start_item)';

        //build the parameters array
        $params = array (':user_id' => $user_id, ':job_id' => $job_id, ':jobs_per_page' => $pageSize,
        ':start_item' => $start_item);

        //execute the query and return the results
    
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get offer details for this user
    //gets details of a single offer
    public static function getOfferDetailsForPoster($offer_id){
        //build the sql query
        $sql = 'CALL get_offer_details_for_poster(:offer_id)';
        
        //build the params array
        $params = array(':offer_id' => $offer_id);
        
        $offer_data = DatabaseHandler::GetRow($sql, $params);
        
        $offerDetails = array();
        
        $offerDetails['offer_id'] = $offer_data['offer_id'];
        $offerDetails['offered_by'] = $offer_data['offered_by'];
	    $offerDetails['job_id'] = $offer_data['job_id'];
        $offerDetails['offer_amount'] = $offer_data['offer_amount'];
        $offerDetails['message'] = $offer_data['message'];
        $offerDetails['last_edited_on'] = $offer_data['last_edited_on'];
        $offerDetails['seen_by_poster'] = $offer_data['seen_by_poster'];
        $offerDetails['edit_count'] = $offer_data['edit_count'];
	    $offerDetails['offer_accepted'] = $offer_data['offer_accepted'];
        $offerDetails['name'] = $offer_data['name'];
        $offerDetails['est_tot_budget'] = $offer_data['est_tot_budget'];
        $offerDetails['final_job_cost'] = $offer_data['final_job_cost'];
        $offerDetails['posted_by'] = $offer_data['posted_by'];
        $offerDetails['posted_on'] = $offer_data['posted_on'];
	    $offerDetails['job_date'] = $offer_data['job_date'];
	    $offerDetails['job_status'] = $offer_data['job_status'];
	    //$offerDetails['has_rated_fixer'] = $offer_data['has_rated_fixer'];
	    $offerDetails['fixer_phone_number'] = $offer_data['phone_number'];
	    $offerDetails['is_payment_received'] = $offer_data['is_payment_received'];
        
        //execute the query and return the results
        return $offerDetails;
    }
    
    //get offer details for the fixer
    //gets details of a single offer
    public static function getOfferDetailsForFixer($offer_id){
        //build the sql query
        $sql = 'CALL get_offer_details_for_fixer(:offer_id)';
        
        //build the params array
        $params = array(':offer_id' => $offer_id);
        
        $offer_data = DatabaseHandler::GetRow($sql, $params);
        
        $offerDetails = array();
        
        $offerDetails['offer_id'] = $offer_data['offer_id'];
        $offerDetails['offered_by'] = $offer_data['offered_by'];
	    $offerDetails['job_id'] = $offer_data['job_id'];
        $offerDetails['offer_amount'] = $offer_data['offer_amount'];
        $offerDetails['message'] = $offer_data['message'];
        $offerDetails['last_edited_on'] = $offer_data['last_edited_on'];
        $offerDetails['seen_by_poster'] = $offer_data['seen_by_poster'];
        $offerDetails['edit_count'] = $offer_data['edit_count'];
	    $offerDetails['offer_accepted'] = $offer_data['offer_accepted'];
	    $offerDetails['job_id'] = $offer_data['job_id'];
        $offerDetails['name'] = $offer_data['name'];
        //$offerDetails['has_rated_poster'] = $offer_data['has_rated_poster'];
        $offerDetails['est_tot_budget'] = $offer_data['est_tot_budget'];
        $offerDetails['final_job_cost'] = $offer_data['final_job_cost'];
        $offerDetails['posted_by'] = $offer_data['posted_by'];
        $offerDetails['posted_on'] = $offer_data['posted_on'];
	    $offerDetails['job_date'] = $offer_data['job_date'];
	    $offerDetails['job_status'] = $offer_data['job_status'];
	    $offerDetails['poster_phone_number'] = $offer_data['phone_number'];
	    $offerDetails['is_payment_received'] = $offer_data['is_payment_received'];
        
        //execute the query and return the results
        return $offerDetails;
    }
    
    //get job in progress details
    public static function getJIPDetails($offer_id){
        //build the sql query
        $sql = 'CALL get_JIP_details(:offer_id)';
        
        //build the params array
        $params = array(':offer_id' => $offer_id);
        
        $offer_data = DatabaseHandler::GetRow($sql, $params);
        
        $offerDetails = array();
        
        $offerDetails['offer_id'] = $offer_data['offer_id'];
        $offerDetails['offered_by'] = $offer_data['offered_by'];
	    $offerDetails['job_id'] = $offer_data['job_id'];
        $offerDetails['offer_amount'] = $offer_data['offer_amount'];
        $offerDetails['message'] = $offer_data['message'];
        $offerDetails['last_edited_on'] = $offer_data['last_edited_on'];
        $offerDetails['seen_by_poster'] = $offer_data['seen_by_poster'];
        $offerDetails['edit_count'] = $offer_data['edit_count'];
	    $offerDetails['offer_accepted'] = $offer_data['offer_accepted'];
	    $offerDetails['job_id'] = $offer_data['job_id'];
        $offerDetails['name'] = $offer_data['name'];
        $offerDetails['est_tot_budget'] = $offer_data['est_tot_budget'];
        $offerDetails['final_job_cost'] = $offer_data['final_job_cost'];
        $offerDetails['posted_by'] = $offer_data['posted_by'];
        $offerDetails['posted_on'] = $offer_data['posted_on'];
	    $offerDetails['job_date'] = $offer_data['job_date'];
	    $offerDetails['actual_start_date'] = $offer_data['actual_start_date'];
        
        //execute the query and return the results
        return $offerDetails;
    }
    
    //updating offer seen status to 1 - seen by poster
    public static function updateOfferSeenStatus($offer_id, $status)
    {
        
        // Build SQL query
		$sql = 'CALL update_offer_seen_status(:offer_id, :status)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':status' => $status);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //check if the user has already accepted another offer for this same job
    public static function checkOfferAlreadyAccepted($job_id, $userId)
    {
        
        // Build SQL query
		$sql = 'CALL check_offer_already_accepted(:job_id, :userId)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':userId' => $userId);
        
        return DatabaseHandler::GetRow($sql, $params);
    }
    
    //updating offer to 1 - accepted status
    public static function posterAcceptOffer($offer_id, $job_id, $job_cost, $status)
    {
        
        // Build SQL query
		$sql = 'CALL update_offer_accepted_by_poster(:offer_id, :job_id, :job_cost, :status)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':job_id' => $job_id, ':job_cost' => $job_cost, ':status' => $status);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the fixer and the name of the poster
        return $result;
    }
    
    //set the actual job start date 
    public static function setJobStartDate($job_id)
    {
        
        // Build SQL query
		$sql = 'CALL set_job_start_date(:job_id, :started_on)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':started_on' => date('Y-m-d H:i:s'));
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //delete the offer from the offers table and notify the fixer that the
//offer is rejected
//update the job status to posted
    public static function posterRejectOffer($offer_id, $job_id)
    {
        
        // Build SQL query
		$sql = 'CALL update_offer_rejected_by_poster(:offer_id, :job_id)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':job_id' => $job_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the fixer and the name of the poster
        return $result;
    }
    
    //delete the offer from the offers table and notify the poster that the
//fixer is no longer interested in the job
//update the job status to posted
    public static function fixerRejectOffer($offer_id, $job_id)
    {
        
        // Build SQL query
		$sql = 'CALL update_offer_rejected_by_fixer(:offer_id, :job_id)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':job_id' => $job_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and the name of the fixer
        return $result;
    }
    
    //Method to delete the job offer
    public static function deleteJobOffer($offer_id)
    {
		// Build SQL query
		$sql = 'CALL delete_job_offer(:offer_id)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id,);
		
		return DatabaseHandler::Execute($sql, $params);
    }
    
    //check if fixer has already made an offer for a job
    public static function checkOfferAlreadyMade($userId, $jobId)
    {
        
        // Build SQL query
		$sql = 'CALL count_offer_already_made(:userId, :jobId)';
		
		// Build the parameters array
        $params = array (':userId' => $userId, ':jobId' => $jobId);
        
        $offer_count = DatabaseHandler::GetRow($sql, $params);
        
        $offerCount = array();
        
        $offerCount['offer_made_count'] = $offer_count['offer_made_count'];
        
        //execute the query and return the number of offers made by this fixer
        //for the job
        return $offerCount;
    }
    
    //set job status to job in progress - 4
    //job has been started by the fixer
    public static function fixerStartJob($offer_id, $job_id)
    {
        
        // Build SQL query
		$sql = 'CALL update_job_started_by_fixer(:offer_id, :job_id)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':job_id' => $job_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and the name of the fixer
        return $result;
    }
    
    //set job status to job finished / completed - 5
    //job has been finished by the fixer
    public static function fixerFinishJob($offer_id, $job_id)
    {
        
        // Build SQL query
		$sql = 'CALL update_job_finished_by_fixer(:offer_id, :job_id, :date_finished)';
		
		// Build the parameters array
        $params = array (':offer_id' => $offer_id, ':job_id' => $job_id, ':date_finished' => date('Y-m-d H:i:s'));
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and the name of the fixer
        return $result;
    }
    
    //set the payment details in the db
    //this when payment was via mobile money
    public static function saveMMPaymentdetails($job_id, $poster_id,
                $fixer_id, $offer_id, $job_cost, $amnt_fixer_gets, $service_fee)
    {
        
        // Build SQL query
		$sql = 'CALL insert_mm_payment_details(:job_id, :poster_id,
                :fixer_id, :offer_id, :job_cost, :amnt_fixer_gets, :service_fee)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':poster_id' => $poster_id, 
        ':fixer_id' => $fixer_id,':offer_id' => $offer_id,':job_cost' => $job_cost,':amnt_fixer_gets' => $amnt_fixer_gets,':service_fee' => $service_fee);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and fixer, job and offer id
        return $result;
    }
    
    //mark the payment as received
    public static function setPaymentReceived($job_id,$poster_id,
                $fixer_id, $offer_id, $job_cost, $service_fee, $flw_trx_fee, $amnt_fixer_gets, $pay_method)
    {
        
        // Build SQL query
		$sql = 'CALL mark_payment_as_received(:job_id, :poster_id,
                :fixer_id, :offer_id, :job_cost, :service_fee, :flw_trx_fee,:amnt_fixer_gets, :pay_method, :pay_day)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':poster_id' => $poster_id, 
        ':fixer_id' => $fixer_id,':offer_id' => $offer_id,':job_cost' => $job_cost,':service_fee' => $service_fee, ':flw_trx_fee' => $flw_trx_fee, ':amnt_fixer_gets' => $amnt_fixer_gets,':pay_method' => $pay_method, ':pay_day' => date('Y-m-d H:i:s'));
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        if($pay_method == "wallet"){
            $res = SELF::getWalletBalance($poster_id);
            
            $walletBal = $res['walletBalance'];
            
            $newBal = $walletBal - $job_cost; 
            
            $trx_type = "pay using wallet";
            $funds_added = "0";
        
            // Build SQL query
    		$walletSql = 'CALL set_wallet_balance(:user_id, :funds_added, :new_balance, :trx_type, :trx_date)';
    		
    		// Build the parameters array
            $param = array (':user_id' => $poster_id, ':funds_added' => $funds_added, ':new_balance' => $newBal, ':trx_type' => $trx_type, ':trx_date' => date('Y-m-d H:i:s'));
            
            $balResult = DatabaseHandler::GetRow($walletSql, $param);
            
        }
        
        //execute the query and return the fcm id of the fixer
        return $result;
    }
    
    //check if the payment has been received
    public static function checkPaymentReceived($job_id)
    {
        
        // Build SQL query
		$sql = 'CALL check_payment_is_received(:job_id)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return is_payment_received(1 - yes, 0 - no)
        return $result;
    }
    
    //mark the payment as released
    public static function releaseFixerPay($job_id, $offer_id)
    {
        
        // Build SQL query
		$sql = 'CALL release_fixer_payment(:job_id, :offer_id, :release_pay_date)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':offer_id' => $offer_id, ':release_pay_date' => date('Y-m-d H:i:s'));
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and fixer, job and offer id
        return $result;
    }
    
    //record the user's new wallet balance
    public static function setNewWalletBalance($user_id, $funds_added, $new_balance)
    {
        $trx_type = "wallet top up";
        
        // Build SQL query
		$sql = 'CALL set_wallet_balance(:user_id, :funds_added, :new_balance, :trx_type,:trx_date)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id, ':funds_added' => $funds_added, ':new_balance' => $new_balance, ':trx_type' => $trx_type, ':trx_date' => date('Y-m-d H:i:s'));
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the user
        return $result;
    }
    
    //record the user's new wallet balance
    public static function getWalletBalance($user_id)
    {
        
        // Build SQL query
		$sql = 'CALL get_wallet_balance(:user_id)';
		
		// Build the parameters array
        $params = array (':user_id' => $user_id);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the user
        return $result;
    }
    
    //get the completed jobs as a poster
    public static function getJobsCompletedAsPoster($user_id, $pageNo, $pageSize){
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_jobs_completed_as_poster(:user_id, :jobs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get the completed jobs as a fixer
    public static function getJobsCompletedAsFixer($user_id, $pageNo, $pageSize){
        $start_item = 0;
        if($pageNo == 0){
            $start_item = 0;
        }else{
            $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
        }
        
        //build the sql query
        $sql = 'CALL get_jobs_completed_as_fixer(:user_id, :jobs_per_page, :start_item)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id, ':jobs_per_page' => $pageSize,
      ':start_item' => $start_item);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //set the payment details in the db
    //this when payment was via mobile money
    public static function saveCashPaymentdetails($job_id, $poster_id,
                $fixer_id, $offer_id, $job_cost, $amnt_fixer_gets, $service_fee)
    {
        
        // Build SQL query
		$sql = 'CALL insert_cash_payment_details(:job_id, :poster_id,
                :fixer_id, :offer_id, :job_cost, :amnt_fixer_gets, :service_fee)';
		
		// Build the parameters array
        $params = array (':job_id' => $job_id, ':poster_id' => $poster_id, 
        ':fixer_id' => $fixer_id,':offer_id' => $offer_id,':job_cost' => $job_cost,':amnt_fixer_gets' => $amnt_fixer_gets,':service_fee' => $service_fee);
        
        $result = DatabaseHandler::GetRow($sql, $params);
        
        //execute the query and return the fcm id of the poster and fixer, job and offer id
        return $result;
    }
    
    //Method to add poster rating
    public static function addPosterRating($job_id, $poster_id, $fixer_id, $rating_value, $fixer_comment)
    {
        //build sql query
        $sql = 'CALL add_poster_rating(:job_id, :poster_id, :fixer_id, :rating_value, :fixer_comment, :post_date)';
        
        //build the params array ':trx_date' => date('Y-m-d H:i:s')
        $params = array(':job_id' => $job_id, ':fixer_id' => $fixer_id, 
            ':poster_id' => $poster_id, ':rating_value' => $rating_value, ':fixer_comment' => $fixer_comment, ':post_date' => date('Y-m-d H:i:s'));
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to add fixer rating
    public static function addFixerRating($job_id, $poster_id, $fixer_id, $rating_value, $poster_comment)
    {
        //build sql query
        $sql = 'CALL add_fixer_rating(:job_id, :poster_id, :fixer_id, :rating_value, :poster_comment, :post_date)';
        
        //build the params array
        $params = array(':job_id' => $job_id, ':poster_id' => $poster_id, 
            ':fixer_id' => $fixer_id, ':rating_value' => $rating_value, ':poster_comment' => $poster_comment, ':post_date' => date('Y-m-d H:i:s'));
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //get the selected fixer's rating from the db
    public static function getFixerRating($fixer_id){
        //build the sql query
        $sql = 'CALL calculate_fixer_rating(:fixer_id)';
        
        //build the params array
        $params = array(':fixer_id' => $fixer_id);
        
        $fixer_rating = DatabaseHandler::GetRow($sql, $params);
        
        $ratingVal = array();
        
        $ratingVal['total_fixer_rating'] = $fixer_rating['total_fixer_rating'];
        
        return $ratingVal;
    }
    
    //get the selected poster's rating from the db
    public static function getPosterRating($poster_id){
        //build the sql query
        $sql = 'CALL calculate_poster_rating(:poster_id)';
        
        //build the params array
        $params = array(':poster_id' => $poster_id);
        
        $poster_rating = DatabaseHandler::GetRow($sql, $params);
        
        $ratingVal = array();
        
        $ratingVal['total_poster_rating'] = $poster_rating['total_poster_rating'];
        
        return $ratingVal;
    }
    
    //get the reviews and rating of this user by other users when thos user
    //posted a job
  public static function GetUserReviewsAsPoster($pageNo, $pageSize, $userId)
  {
      $start_item = 0;
    if($pageNo == 0){
        $start_item = 0;
    }else{
        $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
    }
        
    // Retrieve the list of products
    $sql = 'CALL get_user_reviews_as_poster(
                   :reviews_per_page, :start_item, :user_id)';

    // Build the parameters array
    $params = array (
      ':reviews_per_page' => $pageSize,
      ':start_item' => $start_item,
      ':user_id' => $userId);

    // Execute the query and return the results
    return DatabaseHandler::GetAll($sql, $params);
  }
  
   //get the reviews and rating of this user by other users when this user
    //fixed a job
  public static function GetUserReviewsAsFixer($pageNo, $pageSize, $userId)
  {
    $start_item = 0;
    if($pageNo == 0){
        $start_item = 0;
    }else{
        $start_item = ($pageNo - 1) * env('JOBS_PER_PAGE');
    }

    // Retrieve the list of products
    $sql = 'CALL get_user_reviews_as_fixer(
                   :reviews_per_page, :start_item, :user_id)';

    // Build the parameters array
    $params = array (
      ':reviews_per_page' => $pageSize,
      ':start_item' => $start_item,
      ':user_id' => $userId);

    // Execute the query and return the results
    return DatabaseHandler::GetAll($sql, $params);
  }
 
    //Method to send a message to another user
    function sendMessage($from, $to, $title, $message)
    {
        $stmt = $this->con->prepare("INSERT INTO messages (from_users_id, to_users_id, title, message) VALUES (?, ?, ?, ?);");
        $stmt->bind_param("iiss", $from, $to, $title, $message);
        if ($stmt->execute())
            return true;
        return false;
    }
    
    //Method to get the user fcm id
    public static function getUserFcmId($user_id)
    {
		// Build the SQL query
        $sql = 'CALL get_user_fcm_id(:user_id)';

        // Build the parameters array
        $params = array (':user_id' => $user_id);

        // Execute the query and return the results
        $user_data = DatabaseHandler::GetRow($sql, $params);
		
		$user = array();
            $user['fcm_registration_id'] = $user_data['fcm_registration_id'];
		
		return $user;
    }
    
    //search for languages
    public static function searchForLanguage($searchString){
        
        // Build the SQL query
           $sql = 'CALL language_search(:searchString)';

           // Build the parameters array
          $params = array (':searchString' => $searchString);
          
          // Execute the query and return the results
	return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get tutors for a single language
    public static function getTutorsForLanguage($language_id){
        //build the sql query
        $sql = 'CALL get_tutors_for_language(:language_id)';
        
        //build the parameters array
        $params = array (':language_id' => $language_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get a slected tutor's details from the db
    public static function getTutorDetails($tutor_id){
        //build the sql query
        $sql = 'CALL get_tutor_details(:tutor_id)';
        
        //build the params array
        $params = array(':tutor_id' => $tutor_id);
        
        $tutor_data = DatabaseHandler::GetRow($sql, $params);
        
        $tutorDetails = array();
        
        $tutorDetails['user_id'] = $tutor_data['user_id'];
        $tutorDetails['name'] = $tutor_data['name'];
	$tutorDetails['phone_number'] = $tutor_data['phone_number'];
        $tutorDetails['gender'] = $tutor_data['gender'];
        $tutorDetails['description'] = $tutor_data['description'];
        $tutorDetails['date_of_birth'] = $tutor_data['date_of_birth'];
	$tutorDetails['profile_pic'] = $tutor_data['profile_pic'];
        
        //execute the query and return the results
        return $tutorDetails;
    }
    
    //get the selected tutor's languages taught from the db
    public static function getLanguagesForTutor($tutor_id){
        //build the sql query
        $sql = 'CALL get_languages_for_tutor(:tutor_id)';
        
        //build the params array
        $params = array(':tutor_id' => $tutor_id);
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get the selected tutor's rating from the db
    public static function getTutorRating($tutor_id){
        //build the sql query
        $sql = 'CALL calculate_tutor_rating(:tutor_id)';
        
        //build the params array
        $params = array(':tutor_id' => $tutor_id);
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //Method to create a new meeting schedule
    public static function createMeetingSchedule($user_id, $tutor_id, $language_name, 
            $meeting_date, $meeting_time, $meeting_location, $modified_by)
    {
        //build sql query
        $sql = 'CALL create_meeting_schedule(:user_id, :tutor_id, :language_name, '
                . ':meeting_date, :meeting_time, :meeting_location, :last_modified_by)';
        
        //build the params array
        $params = array(':user_id' => $user_id, ':tutor_id' => $tutor_id, ':language_name' => $language_name, ':meeting_date' => $meeting_date,
            ':meeting_time' => $meeting_time, ':meeting_location' => $meeting_location, ':last_modified_by' => $modified_by);
        
        return DatabaseHandler::GetOne($sql, $params);
    }
    
    //get pending requests for a specific tutor
    public static function getPendingRequests($tutor_id){
        //build the sql query
        $sql = 'CALL get_pending_requests_for_tutor(:tutor_id)';
        
        //build the parameters array
        $params = array (':tutor_id' => $tutor_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get confirmed requests for a specific tutor
    public static function getTutorConfirmedRequests($tutor_id){
        //build the sql query
        $sql = 'CALL get_confirmed_requests_for_tutor(:tutor_id)';
        
        //build the parameters array
        $params = array (':tutor_id' => $tutor_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get pending requests for a specific user
    public static function getUserPendingRequests($user_id){
        //build the sql query
        $sql = 'CALL get_pending_requests_for_user(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get confirmed requests for a specific user
    public static function getUserConfirmedRequests($user_id){
        //build the sql query
        $sql = 'CALL get_confirmed_requests_for_user(:user_id)';
        
        //build the parameters array
        $params = array (':user_id' => $user_id);
        
        //execute the query and return the results
        
        return DatabaseHandler::GetAll($sql, $params);
    }
    
    //get a slected pending request's details from the db for the tutor
    public static function getPendingRequestDetails($meeting_id){
        //build the sql query
        $sql = 'CALL get_pending_request_details(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //get a slected pending request's details from the db for the user
    public static function getPendingRequestDetailsForUser($meeting_id){
        //build the sql query
        $sql = 'CALL get_pending_request_details_for_user(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //get a slected confirmed request's details from the db for tutor
    public static function getConfirmedRequestDetails($meeting_id){
        //build the sql query
        $sql = 'CALL get_confirmed_request_details(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //get a slected confirmed request's details from the db for the user
    public static function getConfirmedRequestDetailsForUser($meeting_id){
        //build the sql query
        $sql = 'CALL get_confirmed_request_details_for_user(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //get meeting details from the db for the user
    //this one just gets the details plus the tutor details and does not matter about
    //the meeting status e.g pending, confirmed, complete
    public static function getMeetingDetailsForUser($meeting_id){
        //build the sql query
        $sql = 'CALL get_meeting_details_for_user(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //get meeting details from the db for the tutor
    //this one just gets the details plus the user details and does not matter about
    //the meeting status e.g pending, confirmed, complete
    public static function getMeetingDetailsForTutor($meeting_id){
        //build the sql query
        $sql = 'CALL get_meeting_details_for_tutor(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        $request_data = DatabaseHandler::GetRow($sql, $params);
        
        $requestDetails = array();
        
        $requestDetails['meeting_id'] = $request_data['meeting_id'];
        $requestDetails['user_id'] = $request_data['user_id'];
        $requestDetails['tutor_id'] = $request_data['tutor_id'];
        $requestDetails['language_name'] = $request_data['language_name'];
	$requestDetails['meeting_date'] = $request_data['meeting_date'];
        $requestDetails['meeting_time'] = $request_data['meeting_time'];
        $requestDetails['meeting_location'] = $request_data['meeting_location'];
        $requestDetails['created_on'] = $request_data['created_on'];
        $requestDetails['last_modified_by'] = $request_data['last_modified_by'];
        $requestDetails['name'] = $request_data['name'];
	$requestDetails['profile_pic'] = $request_data['profile_pic'];
        $requestDetails['gender'] = $request_data['gender'];
        $requestDetails['date_of_birth'] = $request_data['date_of_birth'];
        
        //execute the query and return the results
        return $requestDetails;
    }
    
    //Method to update a new meeting schedule
    public static function updateMeetingSchedule($meeting_id, 
            $meeting_date, $meeting_time, $meeting_location, $modified_by)
    {
        //build sql query
        $sql = 'CALL update_meeting_schedule(:meeting_id, :meeting_date, :meeting_time, :meeting_location, :last_modified_by)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':meeting_date' => $meeting_date,
            ':meeting_time' => $meeting_time, ':meeting_location' => $meeting_location, ':last_modified_by' => $modified_by);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to confirm a meeting
    public static function confirmMeetingSchedule($meeting_id, $modified_by)
    {
        //build sql query
        $sql = 'CALL confirm_meeting_schedule(:meeting_id, :last_modified_by)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':last_modified_by' => $modified_by);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to start a meeting
    public static function startMeetingSession($meeting_id)
    {
        //build sql query
        $sql = 'CALL start_meeting_session(:meeting_id)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to end a meeting
    public static function endMeetingSession($meeting_id, $tutor_id, $notify_user_id, $time_taken)
    {
        //build sql query
        $sql = 'CALL end_meeting_session(:meeting_id, :tutor_id, :notify_user_id, :time_taken)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':tutor_id' => $tutor_id,
            ':notify_user_id' => $notify_user_id, ':time_taken' => $time_taken);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to update completed session with the price
    public static function updateCompletedSession($meeting_id, $session_cost)
    {
        //build sql query
        $sql = 'CALL update_completed_session(:meeting_id, :session_cost)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':session_cost' => $session_cost);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to delete a meeting
    public static function deleteMeetingSchedule($meeting_id, $modified_by)
    {
        //build sql query
        $sql = 'CALL delete_meeting_schedule(:meeting_id, :last_modified_by)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':last_modified_by' => $modified_by);
        
        return DatabaseHandler::Execute($sql, $params);
    }

    //Method to update profile of user
    public static function updateProfile($user_id, $name, $description, $date_of_birth, $gender,$phone_number)
    {
        //build sql query
        $sql = 'CALL update_user_profile(:user_id, :name, :description, :date_of_birth, :gender, :phone_number)';
        
        //build the params array
        $params = array(':user_id' => $user_id, ':name' => $name, 
            ':description' => $description, ':date_of_birth' => $date_of_birth, ':gender' => $gender, ':phone_number'=>$phone_number);
        
        return DatabaseHandler::Execute($sql, $params);
       
    }
    
    //Method to add a language to a user
    public static function addLanguageToUser($language_id, $user_id)
    {
        //build sql query
        $sql = 'CALL add_language_to_user(:language_id, :user_id)';
        
        //build the params array
        $params = array(':language_id' => $language_id, ':user_id' => $user_id);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to update role of user
    public static function updateUserRole($user_id)
    {
        //build sql query
        $sql = 'CALL update_user_role(:user_id)';
        
        //build the params array
        $params = array(':user_id' => $user_id);
        
        return DatabaseHandler::Execute($sql, $params);
       
    }
    
    //Method to add user rating
    public static function addUserRating($meeting_id, $tutor_id, $student_id, $rating_value)
    {
        //build sql query
        $sql = 'CALL add_user_rating(:meeting_id, :tutor_id, :student_id, :rating_value)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':tutor_id' => $tutor_id, 
            ':student_id' => $student_id, ':rating_value' => $rating_value);
        
        return DatabaseHandler::Execute($sql, $params);
    }
    
    //Method to add tutor rating
    public static function addTutorRating($meeting_id, $user_id, $tutor_id, $rating_value)
    {
        //build sql query
        $sql = 'CALL add_tutor_rating(:meeting_id, :user_id, :tutor_id, :rating_value)';
        
        //build the params array
        $params = array(':meeting_id' => $meeting_id, ':user_id' => $user_id, 
            ':tutor_id' => $tutor_id, ':rating_value' => $rating_value);
        
        return DatabaseHandler::Execute($sql, $params);
    }

    
    //Method to get user by email and password during login attempt
    public static function getUserByEmailAndPassword($email, $password)
    {
		// Build the SQL query
        $sql = 'CALL get_user(:email)';

        // Build the parameters array
        $params = array (':email' => $email);

        // Execute the query and return the results
        $user_data = DatabaseHandler::GetRow($sql, $params);
		
		//verifying user password
		$salt = $user_data['salt'];
        $encrypted_password = $user_data['encrypted_password'];
        $hash = self::checkhashSSHA($salt, $password);
		
		$user = array();
        // check for password equality
        if ($encrypted_password == $hash) {
            // user authentication details are correct
            $user['user_id'] = $user_data['user_id'];
		    $user['name'] = $user_data['name'];
		    $user['email'] = $user_data['email'];
            $user['gender'] = $user_data['gender'];
            $user['role'] = $user_data['role'];
            $user['description'] = $user_data['description'];
            $user['profile_pic'] = $user_data['profile_pic'];
            $user['phone_number'] = $user_data['phone_number'];
            $user['is_phone_verified'] = $user_data['is_phone_verified'];
            $user['date_of_birth'] = $user_data['date_of_birth'];
            $user['location'] = $user_data['location'];
		    $user['created_on'] = $user_data['created_on'];
		    $user['updated_on'] = $user_data['updated_on'];
	
		    return $user;
        }
    }
 
    //Method to get user by email
    public static function getUserByEmail($email)
    {
		// Build the SQL query
        $sql = 'CALL get_user(:email)';

        // Build the parameters array
        $params = array (':email' => $email);

        // Execute the query and return the results
        $user_data = DatabaseHandler::GetRow($sql, $params);
		
        $user = array();
        $user['id'] = $user_data['user_id'];
        $user['name'] = $user_data['name'];
	$user['date_of_birth'] = $user_data['date_of_birth'];
        $user['gender'] = $user_data['gender'];
	$user['email'] = $user_data['email'];
        return $user;
    }
    
    //Method to get user by user_id during update profile
    public static function getUserById($user_id)
    {
		// Build the SQL query
        $sql = 'CALL get_user_by_id(:user_id)';

        // Build the parameters array
        $params = array (':user_id' => $user_id);

        // Execute the query and return the results
        $user_data = DatabaseHandler::GetRow($sql, $params);
		
		$user = array();
        // user authentication details are correct
        $user['user_id'] = $user_data['user_id'];
		$user['name'] = $user_data['name'];
		$user['email'] = $user_data['email'];
        $user['gender'] = $user_data['gender'];
        $user['role'] = $user_data['role'];
        $user['description'] = $user_data['description'];
        $user['profile_pic'] = $user_data['profile_pic'];
        $user['phone_number'] = $user_data['phone_number'];
        $user['fcm_registration_id'] = $user_data['fcm_registration_id'];
        $user['date_of_birth'] = $user_data['date_of_birth'];
		$user['created_at'] = $user_data['created_at'];
		$user['location'] = $user_data['location'];
		$user['updated_at'] = $user_data['updated_at'];
		
		return $user;
    }
    
    // updating user FCM registration ID
    public static function updateFcmID($user_id, $fcm_registration_id) {
        
        // Build the SQL query
        $sql = 'CALL update_fcm_id(:user_id, :fcm_registration_id, :updated_on)';

        // Build the parameters array
        $params = array (':user_id' => $user_id, ':fcm_registration_id' => $fcm_registration_id, ':updated_on' => date('Y-m-d H:i:s'));
        
        //DatabaseHandler::Execute($sql, $params);
 
        if (DatabaseHandler::Execute($sql, $params)) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'FCM registration ID updated successfully';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Failed to update FCM registration ID";
        }
        //$stmt->close();
 
        return $response;
    }
 
    //Method to check if email already exist
    public static function isUserExist($email)
    {
		
		 // Build the SQL query
         $sql = 'CALL is_user_exist(:email)';

        // Build the parameters array
        $params = array (':email' => $email);

        // Execute the query and return the results
        return DatabaseHandler::GetRow($sql, $params) > 0;
	
	/*
        $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
		*/
    }
	
        //method to hash user password
	/**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
	public static function Hash($password)
	{
           $salt1 = sha1(rand());
           $salt = substr($salt1, 0, 10);
           $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
           $hashed_password = array("salt" => $salt, "encrypted" => $encrypted);
		   //$hashed_password = sha1(HASH_PREFIX . $password);
	   
		return $hashed_password;
	}
        
        /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public static function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }
}