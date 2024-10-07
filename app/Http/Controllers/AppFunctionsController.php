<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DatabaseHandler;
use Illuminate\Support\Facades\File;
use App\Helpers\DbOperation;
use FCM;
use Push;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfferNotification;
use Exception;
//use libs\fcm\fcm;
//use libs\fcm\push;
//include fcm files
require_once base_path('libs/fcm/fcm.php');
require_once base_path('libs/fcm/push.php');

class AppFunctionsController extends Controller
{
    //get the categories in the app
    public function GetCategories() {
    	// Execute the query and return the results
    	$mCategories = DbOperation::GetCategories();
            
        $response = array();
        if($mCategories){
            return response()->json(array("categories" => $mCategories));
        }else{
            $response["error"] = true;
            $response["message"] = 'Categories not retrieved';
            return response()->json($response);
        }
    }
    
    //get the featured categories
    public function GetFeaturedContent(){

        $response = array();
            
        $result = DbOperation::GetFeaturedContent();
                
        if($result != false){
            $response['error'] = false;
            $response['featuredContentList'] = $result;

            $response['message'] = 'Got Featured content';
        }else {
            $response['error'] = true;
            $response['message'] = 'Featured content not retrieved';
        }
        return response()->json($response);
    }
    
    //get the recently posted tasks
    public function GetRecentTasks(){

        $response = array();
            
        $result = DbOperation::GetRecentTasks();
                
        if($result != false){
            $response['error'] = false;
            $response['recentTasksList'] = $result;

            $response['message'] = 'Got recent tasks';
        }else {
            $response['error'] = true;
            $response['message'] = 'Recent tasks not retrieved';
        }
        return response()->json($response);
    }
    
    /* * *
    * Updating user
    *  we use this url to update user's fcm registration id
    */
    // updating user FCM registration ID
    public function updateFcmID($user_id) {
        
        $fcm_registration_id = request()->input('fcm_registration_id');
        
        $result = DbOperation::updateFcmID($user_id, $fcm_registration_id);
        
        $response = array();
 
        if ($result) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'FCM registration ID updated successfully';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Failed to update FCM registration ID";
        }
        //$stmt->close();
        return response()->json($response);
 
    }
    
    //update user details
    public function updateUserDetails() {
        
        $username = request()->input('username');
        $location = request()->input('location');
        $gender = request()->input('gender');
        $email = request()->input('email');
        $dob = request()->input('dob');
        $about_user = request()->input('about_user');
        $user_id = request()->input('user_id');
        
        $result = DbOperation::updateUserDetails($user_id,
                $username, $location, $gender,
                $email, $dob, $about_user);
        
        $response = array();
 
        if ($result) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'User details updated';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Could not update details. Please try again";
        }
        //$stmt->close();
        return response()->json($response);
 
    }
    
    //save user's profile pic
    public function saveProfilePic() {
        $response = array();
        //check if file exists
        if (request()->hasFile('file')) {
            $user_id = request()->input('user_id');
        
            $img_name = request()->file('file')->getClientOriginalName();
            $file = request()->file('file');
            //move the file to the right folder
            if($file->move(base_path('public/assets/images/profile_pics/'), $file->getClientOriginalName())){
                //save the file name
                //update the profile pic name 
		$result = DbOperation::saveProfilePic($user_id, $img_name);
                
                if ($result) {
                    // User successfully updated
                    $response["error"] = false;
                    $response["message"] = 'Profile pic saved successfully';
                } else {
                    // Failed to update user
                    $response["error"] = true;
                    $response["message"] = "Something went wrong. Profile pic not saved";
                }
            }
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Image not received';
        }
        
        return response()->json($response);
       
    }
    
    //save the user's nin
    public function saveNIN($user_id) {
        $response = array();
        $nin = request()->input('nin');
        
        $result = DbOperation::saveNIN($user_id, $nin);
        
        if ($result) {
            $response["error"] = false;
            $response["message"] = 'NIN saved';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Could not save the NIN";
        }
        return response()->json($response);
    }
    
    //saving the user's nin picture 
    public function saveNinPic() {
        $response = array();
        $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/*'];
        
        //check if file exists
        if (request()->hasFile('file')) {
            $user_id = request()->input('user_id');
            
            //check if its an image
            $contentType = request()->file('file')->getClientMimeType();
            
            if(!in_array($contentType, $allowedMimeTypes) ){
                $response['error'] = true;
                $response['message'] = 'Please provide an image';
            }else{
                $img_name = request()->file('file')->getClientOriginalName();
                $file = request()->file('file');
                //move the file to the right folder
                if($file->move(base_path('public/assets/images/user_signup_pics/'), $file->getClientOriginalName())){
                    //save the file name
                    $result = DbOperation::saveNinPic($user_id, $img_name);
                    
                    if ($result) {
                        // User successfully updated
                        $response["error"] = false;
                        $response["message"] = 'Nin pic saved successfully';
                        $response['uploadPic'] = $result;
                    } else {
                        // Failed to update user
                        $response["error"] = true;
                        $response["message"] = "Something went wrong. Nin pic not saved";
                    }
                }
            }
        
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Image not received';
        }
        
        return response()->json($response);
       
    }
    
    //get the user's nin
    public function getUserNin($user_id) {
		
        //$user_id = request()->input('user_id');

        $response = array();

        $result = DbOperation::getUserNin($user_id);
 
        if ($result) {
            $response['error'] = false;
            $response['nin'] = $result['nin'];
            $response['message'] = 'Got nin';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Failed to get nin';
        }
 
        return response()->json($response);
    }
    
    //delete the nin image
    public function deleteNinPic() {
        $response = array();
        $user_id = request()->input('user_id');
        $pic_id = request()->input('pic_id');
        
        $result = DbOperation::deleteNinPic($user_id, $pic_id);
        
        if($result){
            $response["error"] = false;
            $response["message"] = 'Nin pic removed successfully';
        }else{
            $response["error"] = true;
            $response["message"] = "Could not delete the NIN image";
        }
        
        return response()->json($response);
    }
    
    //Method to save the uploaded user portfolio image's names in the db
    public function savePortfolioImageNames($user_id) {
        $response = array();
        //check if file exists
        if (request()->hasFile('file')) {
        
            $img_name = request()->file('file')->getClientOriginalName();
            $file = request()->file('file');
            //move the file to the right folder
            if($file->move(base_path('public/assets/images/user_signup_pics/'), $file->getClientOriginalName())){
                //save the file name
                //save the nin pic name 
		$result = DbOperation::savePortfolioImageNames($user_id, $img_name);
                
                if ($result) {
                    $response["error"] = false;
                    $response["message"] = 'Portfolio pic saved successfully';
                    $response['uploadPic'] = $result;
                } else {
                    // Failed to update user
                    $response["error"] = true;
                    $response["message"] = "Something went wrong. Portfolio pic not saved";
                }
            }
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Image not received';
        }
        
        return response()->json($response);
    }
    
    //delete the portfolio image
    public function deletePortfolioImages() {
        $response = array();
        $user_id = request()->input('user_id');
        $pic_id = request()->input('pic_id');
        
        $result = DbOperation::deletePortfolioImages($user_id, $pic_id);
        
        if($result){
            $response["error"] = false;
            $response["message"] = 'Portfolio pic removed successfully';
        }else{
            $response["error"] = true;
            $response["message"] = "Could not delete the portfolio image";
        }
        
        return response()->json($response);
    }
    
    //saving the user's skill
    public function saveUserSkills() {
        $response = array();
        $user_id = request()->input('user_id');
        $skill = request()->input('skill');
        
        $saveSkillsRes = DbOperation::saveUserSkills($user_id, $skill);
        
        if ($saveSkillsRes) {
            $response["error"] = false;
            $response["message"] = 'Skill saved successfully';
            $response['skill'] = $saveSkillsRes;
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Skill not saved";
        }
        return response()->json($response);
    }
    
    //saving the user's work
    public function saveUserWork() {
        $response = array();
        $user_id = request()->input('user_id');
        $work = request()->input('work');
        
        $saveWorkRes = DbOperation::saveUserWork($user_id, $work);
        
        if ($saveWorkRes) {
            $response["error"] = false;
            $response["message"] = 'Work saved successfully';
            $response['user_Work'] = $saveWorkRes;
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Work not saved";
        }
        return response()->json($response);
    }
    
    //saving the user's education
    public function saveUserEdu() {
        $response = array();
        $user_id = request()->input('user_id');
        $edu = request()->input('education');
        
        $saveEduRes = DbOperation::saveUserEdu($user_id, $edu);
        
        if ($saveEduRes) {
            $response["error"] = false;
            $response["message"] = 'Education saved successfully';
            $response['userEducation'] = $saveEduRes;
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Education not saved";
        }
        return response()->json($response);
    }
    
    //saving the user's language
    public function saveUserLang() {
        $response = array();
        $user_id = request()->input('user_id');
        $lang = request()->input('language');
        
        $saveLangRes = DbOperation::saveUserLang($user_id, $lang);
        
        if ($saveLangRes) {
            $response["error"] = false;
            $response["message"] = 'Language saved successfully';
            $response['userLanguage'] = $saveLangRes;
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Language not saved";
        }
        return response()->json($response);
    }
    
    //delete the user's skill
    public function deleteUserSkill() {
        $response = array();
        $user_id = request()->input('user_id');
        $skill_id = request()->input('skill_id');
        
        $deleteSkillsRes = DbOperation::deleteUserSkill($user_id, $skill_id);
        
        if ($deleteSkillsRes) {
            $response["error"] = false;
            $response["message"] = 'Skill deleted';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Skill not deleted";
        }
        return response()->json($response);
    }
    
    //delete the user's work
    public function deleteUserWork() {
        $response = array();
        $user_id = request()->input('user_id');
        $work_id = request()->input('work_id');
        
        $deleteWorkRes = DbOperation::deleteUserWork($user_id, $work_id);
        
        if ($deleteWorkRes) {
            $response["error"] = false;
            $response["message"] = 'Work deleted';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Work not deleted";
        }
        return response()->json($response);
    }
    
    //delete the user's education
    public function deleteUserEdu() {
        $response = array();
        $user_id = request()->input('user_id');
        $edu_id = request()->input('edu_id');
        
        $deleteEduRes = DbOperation::deleteUserEdu($user_id, $edu_id);
        
        if ($deleteEduRes) {
            $response["error"] = false;
            $response["message"] = 'Education deleted';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Education not deleted";
        }
        return response()->json($response);
    }
    
    //delete the user's language
    public function deleteUserLang() {
        $response = array();
        $user_id = request()->input('user_id');
        $lang_id = request()->input('skill_id');
        
        $deleteLangRes = DbOperation::deleteUserLang($user_id, $lang_id);
        
        if ($deleteLangRes) {
            $response["error"] = false;
            $response["message"] = 'Language deleted';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Something went wrong. Language not deleted";
        }
        return response()->json($response);
    }
    
    //get the user's skills
    public function getUserSkills($user_id) {
        $response = array();
        
        $result = DbOperation::getUserSkills($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['userSkills'] = $result;
            $response['message'] = 'Got user skills';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user skills";
        }
        return response()->json($response);
    }
    
    //get the user's work
    public function getUserWork($user_id) {
        $response = array();
        
        $result = DbOperation::getUserWork($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['userWork'] = $result;
            $response['message'] = 'Got user work';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user work";
        }
        return response()->json($response);
    }
    
    //get the user's education
    public function getUserEdu($user_id) {
        $response = array();
        
        $result = DbOperation::getUserEdu($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['userEdu'] = $result;
            $response['message'] = 'Got user education';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user education";
        }
        return response()->json($response);
    }
    
    //get the user's languages
    public function getUserLang($user_id) {
        $response = array();
        
        $result = DbOperation::getUserLang($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['userLang'] = $result;
            $response['message'] = 'Got user languages';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user languages";
        }
        return response()->json($response);
    }
    
    //get the user's portfolio images
    public function getUserPortImages($user_id) {
        $response = array();
        
        $result = DbOperation::getUserPortImages($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['portfolioPics'] = $result;
            $response['message'] = 'Got user portfolio images';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user portfolio images";
        }
        return response()->json($response);
    }
    
    //get the user's nin images
    public function getUserNinImages($user_id) {
        $response = array();
        
        $result = DbOperation::getUserNinImages($user_id);
        
        if ($result) {
            $response["error"] = false;
            $response['portfolioPics'] = $result;
            $response['message'] = 'Got user nin images';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user nin images";
        }
        return response()->json($response);
    }
    
    //get the visitor profile
    public function getVisitorDetails($user_id) {
        $response = array();
        
        $user = DbOperation::getUserById($user_id);
        
        if ($user) {
            $response["error"] = false;
            $response['user'] = $user;
            $response['message'] = 'Got user';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Cannot get user";
        }
        return response()->json($response);
    }
    
    //posting a job. This creates the job record in the db
    //we get the id and just keep updating this job with more details
    //added by the user
    public function createJob() {
        $response = array();
        $posted_by = request()->input('posted_by');
        $job_title = request()->input('job_title');
        $description = request()->input('description');
        $category_id = request()->input('category_id');
        
        //create a new task
        $job_id = DbOperation::CreateJob($posted_by, $job_title, $description, $category_id);
        
        if ($job_id) {
            $response['error'] = false;
            $response['message'] = 'Job created successfully';
            $response['job'] = $job_id;
        } else {
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Job not created';
        }
        return response()->json($response);
    }
    
    //saving the job pic
    public function uploadUserJobImage($job_id) {
        $response = array();
        //check if file exists
        if (request()->hasFile('file')) {
        
            $img_name = request()->file('file')->getClientOriginalName();
            $file = request()->file('file');
            //move the file to the right folder
            if($file->move(base_path('public/assets/images/job_pics/'), $file->getClientOriginalName())){
                //save the file name
                $result = DbOperation::saveJobPicNames($job_id, $img_name);
                
                if ($result) {
                    $response['error'] = false;
                    $response['message'] = 'Job pic saved successfully';
                    $response['uploadPic'] = $result;
                } else {
                    $responseData['error'] = true;
                    $responseData['message'] = 'Something went wrong. Job pic not saved';
                }
            }
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Image not received';
        }
        return response()->json($response);
    }
    
    //deleting the job image
    public function deleteJobPic() {
        $response = array();
        $job_id = request()->input('job_id');
        $pic_id = request()->input('pic_id');
        
        $result = DbOperation::deleteJobImage($job_id, $pic_id);
        
        if($result){
            $response["error"] = false;
            $response["message"] = 'Job pic deleted';
        }else{
            // Failed to delete record
            $response["error"] = true;
            $response["message"] = "Could not delete the Job image";
        }
        
        return response()->json($response);
    }
    
    //update job details when no image is attached
    public function updateJobWithoutImage($job_id) {
        $response = array();
        
        $posted_by = request()->input('posted_by');
        $job_title = request()->input('job_title');
        $description = request()->input('description');
        $location = request()->input('location');
        $must_have_one = request()->input('must_have_one');
        $must_have_two = request()->input('must_have_two');
        $must_have_three = request()->input('must_have_three');
        $is_job_remote = request()->input('is_job_remote');
        $category_id = request()->input('category_id');
        
        $result = DbOperation::updateJobDetailsWithoutImage($job_id, 
                $job_title, $description, $location,
                $must_have_one, $must_have_two, $must_have_three,
                $is_job_remote);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Job details updated successfully';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not update job details';
        }
        return response()->json($response);
    }
    
    //updating job date and time
    public function updateJobDateTime($job_id) {
        $response = array();
        
        $job_date = request()->input('job_date');
        $job_time = request()->input('job_time');
        
        $result = DbOperation::updateJobDate($job_id, $job_date, $job_time);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Job date time updated';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not update job time';
        }
        return response()->json($response);
    }
    
    //updating job budget
    public function updateJobBudget($job_id) {
        $response = array();
        
        $total_budget = request()->input('total_budget');
        $price_per_hr = request()->input('price_per_hr');
        $total_hrs = request()->input('total_hrs');
        $est_tot_budget = request()->input('est_tot_budget');
        $job_status = request()->input('job_status');
        
        $result = DbOperation::updateJobBudget($job_id, $total_budget, $price_per_hr, $total_hrs, $est_tot_budget, $job_status);
        
        if ($result) {
            
            //get details and send global notification
            $jobDetails = DbOperation::getJobDetails($job_id);
            
            //get job images
            $jobImages = DbOperation::getJobImages($job_id);
            
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();
            
            // optional payload
            $payload = array();
            $payload['notice_type'] = 'New Job Posted'; 
            
            //name of job poster
            $posterName = DbOperation::getUserName($jobDetails['posted_by']);
            $taskTitle = $jobDetails['name'];
     
            // notification title
            $title = $posterName . ' has posted a new task';
             
            // notification message
            $message = $taskTitle;
            
            $push->setTitle($title);
            $push->setMessage($message);
            if (count($jobImages) > 0) {
                $push->setImage('https://fixappug.com/public/assets/images/job_pics/'.$jobImages[0]['image_name']);
            } else {
                $push->setImage('');
            }
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);
            
            $json = '';
            $fcmResponse = '';
            
            //sending the fcm notification to all app users
            $json = $push->getPush();
            $fcmResponse = $fcm->sendToTopic('global', $json);
            
            logger("New task notification");
            logger($fcmResponse);
            
            //'Job posted successfully'
            $response['error'] = false;
            $response['message'] = 'Job posted successfully';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not update job budget';
        }
        
        return response()->json($response);
    }
    
    //helps users report issues they notice about the jobs posted
    public function reportJob() {
        $response = array();
        
        $job_id = request()->input('job_id');
        $reported_by = request()->input('reported_by');
        $comment = request()->input('comment');
        
        $result = DbOperation::ReportJob($job_id, $reported_by, $comment);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Job successfully reported';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not report job now';
        }
        return response()->json($response);
    }
    
    //helps a user cancel the task they posted
    public function cancelTask() {
        $response = array();
        
        $job_id = request()->input('job_id');
        $user_id = request()->input('user_id');
        $is_pay_received = request()->input('is_pay_received');
        $finalJobCost = request()->input('finalJobCost');
        
        $result = DbOperation::CancelTask($job_id, $user_id, $is_pay_received, $finalJobCost);
        
        if ($result) {
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Task canceled';

            //the fcm reg id of the poster to be notified
            $regId = $result['poster_fcm_id'];
            //email of job poster
            $posterEmail = $result['poster_email'];
            //name of job poster
            $posterName = $result['poster_name'];

            //task name
            $taskName = $result['task_name'];

            // notification title
            $title = 'Task Canceled';

            // notification message
            $message = 'You have successfully canceled your task '.$taskName.'. If you had already secured the funds for this task, it will be refunded to you as FixApp credit.';

            //send mail to job poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message.'. Please login and check it out.'));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);
            
            $response['error'] = false;
            $response['message'] = 'Task successfully canceled';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not cancel task now';
        }
        return response()->json($response);
    }
    
    //getting all the jobs by status for this user
    //0 - draft, 1 - posted, 2 - assigned, 3 - offers, 4 - in progress, 5 - completed by fixer, 6 - completed by poster, 7 - Cancelled
    public function getJobsByStatus($user_id, $status) {
        $response = array();
        
        $result = DbOperation::getJobsByStatus($user_id, $status);
        
        if($result != false){
            $response['error'] = false;
            $response['jobsListByStatus'] = $result;
            $response['profile_pic'] = DbOperation::getUserProfilePic($result[0]['posted_by']);
            $response['name'] = DbOperation::getUserName($result[0]['posted_by']);
            $response['message'] = 'Got job details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get job details';
        }
        return response()->json($response);
    }
    
    //getting id images for one employee
    public function getIdImages($user_id) {
        $response = array();
        
        $result = DbOperation::getIdImages($user_id);
        
        if($result != false){
            $response['error'] = false;
            $response['idPics'] = $result;
            $response['message'] = 'Got id images';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get id images';
        }
        return response()->json($response);
    }
    
    //getting all the ad images for an ad
    public function getAdImages($job_id) {
        $response = array();
        
        $result = DbOperation::getAdImages($job_id);
        
        if($result != false){
            $response['error'] = false;
            $response['adPics'] = $result;
            $response['message'] = 'Got ad images';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get ad images';
        }
        return response()->json($response);
    }
    
    //delete the ad image
    public function deleteAdPic() {
        $response = array();
        $pic_id = request()->input('pic_id');
        
        $result = DbOperation::deleteAdPic($pic_id);
        
        if($result){
            $response["error"] = false;
            $response["message"] = 'Ad pic removed successfully';
        }else{
            $response["error"] = true;
            $response["message"] = "Could not delete the Ad image";
        }
        
        return response()->json($response);
    }
    
    //getting the details of a job depending on the status
    public function getJobDetailsByStatus($job_id) {
        $response = array();
        
        $result = DbOperation::getJobDetailsByStatus($job_id);
        
        if($result != false){
            $response['error'] = false;
            $response['jobDetails'] = $result;
            $response['message'] = 'Got job details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get job details';
        }
        return response()->json($response);
    }
    
    //getting all the employees
    // if we have an employee id, return that employee only
    public function getAllEmployees($emp_id) {
        $response = array();

        if($emp_id){
            $result = DbOperation::getSingleEmployee($emp_id);
            if($result){
                $response['error'] = false;
                $response['employeeList'] = $result;

                //log data
                //0 - pending, 1 - successful, 2 - failed
                $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
                $params = array (':api_name' => "Staff Retrieval", ':request_status' => 1,
                    ':date' => date('Y-m-d H:i:s'));
                DatabaseHandler::GetRow($sql, $params);
            }else {
                $response['error'] = true;
                $response['message'] = 'Employee not found';

                //log data
                //0 - pending, 1 - successful, 2 - failed
                $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
                $params = array (':api_name' => "Staff Retrieval", ':request_status' => 2,
                    ':date' => date('Y-m-d H:i:s'));
                DatabaseHandler::GetRow($sql, $params);
            }

        }else{
            $result = DbOperation::getAllEmployees(); 
        
            if($result){
                $response['error'] = false;
                $response['employeeList'] = $result;

                //log data
                //0 - pending, 1 - successful, 2 - failed
                $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
                $params = array (':api_name' => "Staff Retrieval", ':request_status' => 1,
                    ':date' => date('Y-m-d H:i:s'));
                DatabaseHandler::GetRow($sql, $params);
            }else {
                $response['error'] = true;
                $response['message'] = 'Employee list not retrieved. Try again';

                //log data
                //0 - pending, 1 - successful, 2 - failed
                $sql = 'CALL add_api_log(:api_name, :request_status, :date)';
                $params = array (':api_name' => "Staff Retrieval", ':request_status' => 2,
                    ':date' => date('Y-m-d H:i:s'));
                DatabaseHandler::GetRow($sql, $params);
            }
        }
        
        
        return response()->json($response);
    }
    
    //getting the details of a job minus status
    public function getJobDetails($job_id) {
        $response = array();
        
        $result = DbOperation::getJobDetails($job_id);
        
        if($result){
            $response['error'] = false;
            $response['jobDetails'] = $result;
            $response['profile_pic'] = DbOperation::getUserProfilePic($result['posted_by']);
            $response['name'] = DbOperation::getUserName($result['posted_by']);
            $response['message'] = 'Got job details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get job details';
        }
        return response()->json($response);
    }
    
    //getting all the jobs for the user to browse
    // 0 - draft, 1 - posted, 2 - assigned, 3 - offers, 4 - in progress, 5 - completed by fixer, 6 - completed by poster
    //page - the page index to start from for the next list
    public function getJobsForBrowsing($page, $page_size) {
        $response = array();
                
        $result = DbOperation::GetAllJobsForBrowsing($page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['browsedJobsList'] = $result;
            
            $countSql = 'CALL count_all_jobs()';
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, null);
            $response['message'] = 'Got jobs';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get jobs';
        }
        return response()->json($response);
    }
    
    //getting all the jobs for the user to browse
    // 0 - draft, 1 - posted
    //page - the page index to start from for the next list
    public function getAdsForBrowsing($page, $page_size) {
        $response = array();
                
        $result = DbOperation::GetAllAdsForBrowsing($page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['browsedAdsList'] = $result;
            
            $countSql = 'CALL count_all_ads()';
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, null);
            $response['message'] = 'Got ads';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get ads';
        }
        return response()->json($response);
    }
    
    //getting the details of an advert
    public function getAdDetails($ad_id) {
        $response = array();
        
        $result = DbOperation::getAdDetails($ad_id);
        
        if($result){
            $response['error'] = false;
            $response['adDetails'] = $result;
            $response['message'] = 'Got ad details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get ad details';
        }
        return response()->json($response);
    }
    
    //post a new ad
    public function postNewAd() {
        $response = array();
        
        $user_id = request()->input('user_id');
        $ad_title = request()->input('ad_title');
        $description = request()->input('description');
        $location = request()->input('location');
        $price = request()->input('price');
        $category_id = request()->input('category_id');
        
        $advert_id = DbOperation::postNewAd($user_id, 
                $ad_title, $description, $location,
                $price, $category_id);
        
        if ($advert_id) {
            //get the images
            if (request()->hasFile('file')) {
                foreach (request()->file as $file) {
                    $file_name = $file->getClientOriginalName();
                    //move the file to the right folder
                    if($file->move(base_path('public/assets/images/ads/'), $file_name)){
                        //save the image names
                        $result = DbOperation::saveAdImageNames($advert_id, $file_name);
                        
                        if ($result) {
                    
                            //create objects of FCM and Push classes
                            $fcm = new FCM();
                            $push = new Push();
                            
                            // optional payload
                            $payload = array();
                            $payload['notice_type'] = 'New Ad Posted'; 
                            
                            //name of job poster
                            $posterName = DbOperation::getUserName($user_id);
                     
                            // notification title
                            $title = 'New Ad posted by '.$posterName ;
                             
                            // notification message
                            $message = $ad_title;
                            
                            $push->setTitle($title);
                            $push->setMessage($message);
                            $push->setIsBackground(FALSE);
                            $push->setPayload($payload);
                            
                            $json = '';
                            $fcmResponse = '';
                            
                            //sending the fcm notification to all app users
                            $json = $push->getPush();
                            $fcmResponse = $fcm->sendToTopic('global', $json);
                            
                            logger("New Ad notification");
                            logger($fcmResponse);
            
                            $response['error'] = false;
                            $response['message'] = 'Ad posted successfully';
                        } else {
                            $responseData['error'] = true;
                            $responseData['message'] = 'Failed to post the Ad';
                        }
                    }
                }
            }else{
                $responseData['error'] = true;
                $responseData['message'] = 'Image not found';
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'Failed to post the Ad';
        }
        return response()->json($response);
    }
    
    //edit an ad
    public function editAd() {
        $response = array();
        
        $ad_id = request()->input('ad_id');
        $user_id = request()->input('user_id');
        $ad_title = request()->input('ad_title');
        $description = request()->input('description');
        $location = request()->input('location');
        $price = request()->input('price');
        $category_id = request()->input('category_id');
        
        $updateResult = DbOperation::editAd($ad_id, $user_id, 
                $ad_title, $description, $location,
                $price, $category_id);
        
        if ($updateResult) {
            //get the images
            if (request()->hasFile('file')) {
                foreach (request()->file as $file) {
                    $file_name = $file->getClientOriginalName();
                    //move the file to the right folder
                    if($file->move(base_path('public/assets/images/ads/'), $file_name)){
                        //save the image names
                        $result = DbOperation::saveAdImageNames($ad_id, $file_name);
                        
                        if ($result) {
                            $response['error'] = false;
                            $response['message'] = 'Ad updated successfully';
                        } else {
                            $responseData['error'] = true;
                            $responseData['message'] = 'Failed to update the Ad';
                        }
                    }
                }
            }else{
                $responseData['error'] = true;
                $responseData['message'] = 'Image not found';
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'Failed to update the Ad';
        }
        return response()->json($response);
    }
    
    //user delete an ad
    public function deleteAd() {
        $response = array();
        
        $ad_id = request()->input('ad_id');
        $user_id = request()->input('user_id');
        
        $result = DbOperation::deleteAd($ad_id, $user_id);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Ad successfully deleted';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not delete Ad now';
        }
        return response()->json($response);
    }
    
    //helps users report issues they notice about the ads posted
    public function reportAd() {
        $response = array();
        
        $ad_id = request()->input('ad_id');
        $reported_by = request()->input('reported_by');
        $comment = request()->input('comment');
        
        $result = DbOperation::ReportAd($ad_id, $reported_by, $comment);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Ad successfully reported';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not report ad now';
        }
        return response()->json($response);
    } 
    
    //toggle user like / unlike ad
    public function toggleAdLike($userId, $adId, $value) {
        
        $result = DbOperation::toggleAdLike($userId, $adId, $value);
        
        $response = array();
        
        if ($result) {
            //get the number of offers the fixer has already made
            //if 0 then no offer made yet by fixer
            $offer_count = $result['offer_made_count'];
            
            $response['error'] = false;
            
            if($value == 1){
                $response['is_ad_liked'] = true;
                $response['message'] = "Ad liked";
            }else if ($offer_count == 0){
                $response['is_ad_liked'] = false;
                $response['message'] = "Failed to like Ad";
            }
		} else {
            $response['error'] = true;
            $response['message'] = 'Error occured performing action';
        }
        return response()->json($response);
    }
    
    //get ads posted by a single user
    public function getPosterAds($poster_id, $page, $page_size) {
        $response = array();
                
        $result = DbOperation::GetPosterAdsForBrowsing($poster_id, $page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['posterAdsList'] = $result;
            
            $countSql = 'CALL count_poster_ads(:poster_id)';
            $params = array (':poster_id' => $poster_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got poster ads';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get poster ads';
        }
        return response()->json($response);
    }
    
    //getting all the pros for the user to browse
    //page - the page index to start from for the next list
    public function getProsForBrowsing($page, $page_size) {
        $response = array();
                
        $result = DbOperation::GetAllProsForBrowsing($page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['browsedProsList'] = $result;

            $countSql = 'CALL count_all_pros()';
            $response['pages_count'] = DbOperation::HowManyPagesForProsResults($countSql, null);
            $response['message'] = 'Got pros';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get pros';
        }
        return response()->json($response);
    }
    
    //getting the list of messages the user has received
    public function getAllMessages($user_id, $page, $page_size) {
        $response = array();
                
        $result = DbOperation::getMessagesForUser($user_id, $page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['messageList'] = $result;

            $countSql = 'CALL count_msgs_received_for_user(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got messages';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get messages';
        }
        return response()->json($response);
    }
    
    //getting the messages between two users
    public function getMessageDetails($to_id, $from_id, $page, $page_size) {
        $response = array();
                
        $result = DbOperation::getMessagesBetweenUsers($to_id, $from_id, $page,$page_size);
                
        if($result != false){
            $response['error'] = false;
            $response['messageList'] = $result;

            $countSql = 'CALL count_msgs_between_two_users(:to_id, :from_id)';
            $params = array (':to_id' => $to_id, ':from_id' => $from_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got message details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get message details';
        }
        return response()->json($response);
    }
    
    //save a message sent
    public function saveSentMessage() {
        $response = array();
        
        $messageText = request()->input('message');
        $from_id = request()->input('from_id');
        $to_id = request()->input('to_id');
        $ad_id = request()->input('ad_id');
        
        //post the offer made to the db
	$result = DbOperation::saveSentMessage($from_id, $to_id, $ad_id, $messageText);
        
        if($result != false){
            $messageId = $result['message_id'];
            $createdAt = $result['created_at'];

            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'New Message';

            //name of fixer who gave offer
            $senderName = $result['sender_name'];
            //the fcm reg id of the message recepient to be notified
            $regId = $result['to_fcm_id'];

            // notification title
            $title = 'Message from '.$senderName;

            // notification message
            $message = $messageText;

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['newMessage'] = $result;
            //$response['message_id'] = $messageId;
            //$response['created_at'] = $createdAt;
            $response['message'] = 'Message saved successfully';
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Message not saved';
        }
        return response()->json($response);
    }
    
    //save a user's verified payment phone
    public function saveVerifiedPayPhone($user_id) {
        $response = array();
        
        //$user_id = request()->input('user_id');
        $phone_number = request()->input('phone_number');
        
        //post the offer made to the db
	$result = DbOperation::saveVerifiedPayPhone($user_id, $phone_number);
        
        if($result != false){
            $response['error'] = false;
            $response['message'] = 'Payment phone saved successfully';
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Payment phone not saved';
        }
        return response()->json($response);
    }
    
    //search for jobs based on the search query entered by the user
    public function searchJobs($searchQuery, $page, $page_size) {
        $response = array();
                
        $result = DbOperation::SearchForJobs($searchQuery, 'off', $page,$page_size);
                
        if($result == null){
            $response['error'] = false;
            $response['jobSearchResults'] = null;
            $response['message'] = 'No results for search query';
        }else if($result != false){
            $response['error'] = false;
            $response['jobSearchResults'] = $result;
            $response['pages_count'] = DbOperation::HowManyPagesForSearchResults($searchQuery, 'off');
            $response['message'] = 'Search results available';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot search results';
        }
        return response()->json($response);
    }
    
    //return ads similar to what the user had clicked on 
    public function getSimilarAds($categoryId, $title, $page, $page_size) {
        $response = array();
                
        $result = DbOperation::getSimilarAds($categoryId, $title, 'on', $page,$page_size);
                
        if($result == null){
            $response['error'] = false;
            $response['similarAdsResults'] = null;
            $response['message'] = 'No similar ads for query';
        }else if($result != false){
            $response['error'] = false;
            $response['similarAdsResults'] = $result;
            $countSql = 'CALL count_similar_ads(:title)';
            $params = array (':title' => $title);
            $response['pages_count'] = DbOperation::HowManyPagesForSimilarAds($title, $countSql, $params);
            $response['message'] = 'Similar ads available';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get similar ads';
        }
        return response()->json($response);
    }
    
    //saving an offer a fixr has made
    public function saveOffer() {
        $response = array();
        
    $amount_offered = request()->input('amount_offered');
        $offer_message = request()->input('offer_message');
        $user_id = request()->input('user_id');
        $job_id = request()->input('job_id');
        
        //post the offer made to the db
	$result = DbOperation::postOfferMade($amount_offered, $offer_message,$user_id, $job_id);
        
        if($result != false){

            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Offer recevied';

            //name of fixer who gave offer
            $fixerName = $result['name'];
            //the fcm reg id of the fixer to be notified
            $regId = $result['poster_fcm_id'];
            //email of job poster
            $posterEmail = $result['poster_email'];
            //name of job poster
            $posterName = $result['poster_name'];

            // notification title
            $title = 'Offer Received';

            // notification message
            $message = $fixerName . ' has made an offer to a job you posted';
            
            try{
                //send mail to job poster
                Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message.'. Please login and check it out.'));
            }catch(Exception $ex){
                logger($ex->getMessage());
            }
            
            //send mail to job poster
            //Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message.'. Please login and check it out.'));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['message'] = 'Offer posted successfully';
        }else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong. Offer not posted';
        }
        return response()->json($response);
    }
    
    //update an offer a tasker had made
    public function updateOffer($offer_id) {
        $response = array();
        
        $amount_offered = request()->input('amount_offered');
        $offer_message = request()->input('offer_message');
        $edit_count = request()->input('edit_count');
		            
        //post the offer made to the db
	$result = DbOperation::UpdateOfferMade($offer_id, $amount_offered, $offer_message, $edit_count);
	if($result != false){
            
        //create objects of FCM and Push classes
        $fcm = new FCM();
        $push = new Push();
        
        // optional payload
        $payload = array();
        $payload['notice_type'] = 'Fixer Update Offer';
        
        //the fcm reg id of the fixer to be notified
        //$regId = $result['fcm_registration_id'];
        
        //name of fixer who updated offer
        $fixerName = $result['name'];
        //the fcm reg id of the fixer to be notified
        $regId = $result['poster_fcm_id'];
        //email of job poster
        $posterEmail = $result['poster_email'];
        //name of job poster
        $posterName = $result['poster_name'];
 
        // notification title
        $title = 'Fixer updated offer';
         
        // notification message
        $message = $fixerName . ' has updated an offer previously made for your job';
        
        //send mail to job poster
        Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message.'. Please login and check it out.'));
        
        $push->setTitle($title);
        $push->setMessage($message);
        $push->setIsBackground(FALSE);
        $push->setPayload($payload);
        
        $json = '';
        $fcmResponse = '';
        
        //sending the fcm notification to the fixers phone
        $json = $push->getPush();
        $fcmResponse = $fcm->send($regId, $json);
        logger($fcmResponse);
            
        $response['error'] = false;
        $response['message'] = 'Offer updated successfully';
    }else{
        $response['error'] = true;
        $response['message'] = 'Something went wrong. Offer not updated';
    }
        return response()->json($response);
        
    }
    
    //getting all the jobs a fixer has made an offer for
    public function getOffersMade($user_id, $page, $page_size) {
        
        $result = DbOperation::getOffersMade($user_id, $page, $page_size);
        
        $response = array();
        
        if($result == null){
            $response['error'] = false;
            $response['offersMadeList'] = null;
            $response['message'] = 'Empty offers made by fixer list';
        }else if($result != false){
            $response['error'] = false;
            $response['offersMadeList'] = $result;
            
            $countSql = 'CALL count_fixer_offers_made(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            //$response['profile_pic'] = DbOperation::getUserProfilePic($result['posted_by']);
            //$response['name'] = DbOperation::getUserName($result['posted_by']);
            $response['message'] = 'Got offers made by fixer';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get offers made';
        }
        return response()->json($response);
    }
    
    //getting all the job offers accepted by posters for jobs this fixer made offers to
    public function getOffersAcceptedForFixer($user_id, $page, $page_size) {
        
        $result = DbOperation::getOffersAcceptedForFixer($user_id, $page, $page_size);
        
        $response = array();
        
        if($result == null){
            $response['error'] = false;
            $response['fixerOffersAcceptedList'] = null;
            $response['message'] = 'Empty offers accepted by poster list';
        }else if($result != false){
            $response['error'] = false;
            $response['fixerOffersAcceptedList'] = $result;
            
            $countSql = 'CALL count_fixer_offers_accepted(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got offers accepted by poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get offers accepted';
        }
        return response()->json($response);
    }
    
    //getting all the job offers accepted by this poster
    public function getOffersAcceptedForPoster($user_id, $page, $page_size) {
        
        $result = DbOperation::getOffersAcceptedForPoster($user_id, $page, $page_size);
        
        $response = array();
        
        if($result == null){
            $response['error'] = false;
            $response['offersAcceptedList'] = null;
            $response['message'] = 'Empty offers accepted by poster';
        }else if($result != false){
            $response['error'] = false;
            $response['offersAcceptedList'] = $result;
            $countSql = 'CALL count_poster_offers_accepted(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got offers accepted by poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get offers accepted';
        }
        return response()->json($response);
    }
    
    //getting all the jobs by this user/poster which have offers made
    public function getOffersForJobs($user_id, $page, $page_size) {
        
        $result = DbOperation::getOffersForJobs($user_id, $page, $page_size);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['offersReceived'] = $result;
            $countSql = 'CALL count_poster_offers_received(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got offers received for jobs';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get offers received';
        }
        return response()->json($response);
    }
    
    //getting all the offers a job by this user/poster has received
    public function getOffersForSingleJob($user_id, $job_id, $page, $page_size) {
        
        $result = DbOperation::getOffersForSingleJob($user_id, $job_id, $page, $page_size);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['offersReceived'] = $result;
            $countSql = 'CALL count_offers_received_for_job(:job_id)';
            $params = array (':job_id' => $job_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got offers made for jobs';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get offers made';
        }
        return response()->json($response);
    }
    
    //getting the details of an offer for the poster
    public function getOfferDetailsForPoster($offer_id) {
        
        $result = DbOperation::getOfferDetailsForPoster($offer_id);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['offerDetails'] = $result;
            $response['poster_profile_pic'] = DbOperation::getUserProfilePic($result['posted_by']);
            $response['fixer_profile_pic'] = DbOperation::getUserProfilePic($result['offered_by']);
            $response['poster_user_name'] = DbOperation::getUserName($result['posted_by']);
            $response['fixer_user_name'] = DbOperation::getUserName($result['offered_by']);
            $response['message'] = 'Got offer details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get offer details';
        }
        return response()->json($response);
    }
    
    //getting the details of an offer for the fixer
    public function getOfferDetailsForFixer($offer_id) {
        
        $result = DbOperation::getOfferDetailsForFixer($offer_id);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['offerDetails'] = $result;
            $response['poster_profile_pic'] = DbOperation::getUserProfilePic($result['posted_by']);
            $response['fixer_profile_pic'] = DbOperation::getUserProfilePic($result['offered_by']);
            $response['poster_user_name'] = DbOperation::getUserName($result['posted_by']);
            $response['fixer_user_name'] = DbOperation::getUserName($result['offered_by']);
            $response['message'] = 'Got offer details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get offer details';
        }
        return response()->json($response);
    }
    
    //getting the details of a job in progress for the fixer
    public function getJIPDetails($offer_id) {
        
        $result = DbOperation::getJIPDetails($offer_id);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['offerDetails'] = $result;
            $response['poster_profile_pic'] = DbOperation::getUserProfilePic($result['posted_by']);
            $response['fixer_profile_pic'] = DbOperation::getUserProfilePic($result['offered_by']);
            $response['poster_user_name'] = DbOperation::getUserName($result['posted_by']);
            $response['fixer_user_name'] = DbOperation::getUserName($result['offered_by']);
            $response['message'] = 'Got JIP details';
        }else {
            $response['error'] = true;
            $response['message'] = 'Cannot get JIP details';
        }
        return response()->json($response);
    }
    
    //updating offer seen status to 1 - seen by poster
    public function updateOfferSeenStatus($offer_id) {
        $status = request()->input('status');
        
        $result = DbOperation::updateOfferSeenStatus($offer_id, $status);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['message'] = 'Offer status updated to 1 - seen by poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not update offer status';
        }
        return response()->json($response);
    }
    
    //check if the user has already accepted another offer for this same job
    public function checkOfferAlreadyAccepted($jobId, $userId) {
        
        $result = DbOperation::checkOfferAlreadyAccepted($jobId, $userId);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['message'] = 'Offer already accepted';
            $response['fixer_user_name'] = $result['fixer_name'];
            $response['is_offer_accepted'] = $result['offer_accepted'];
        }else if($result == null){
            $response['error'] = false;
            $response['message'] = 'No offer already accepted';
            $response['is_offer_accepted'] = 0;
        }else{
            $response['error'] = true;
            $response['message'] = 'Could not check offer already accepted';
        }
        return response()->json($response);
    }
    
    //updating offer to 1 - accepted status
    //job status to 2 - assigned to this fixer
    public function posterAcceptOffer() {
        
        $offer_id = request()->input('offer_id');
        $job_id = request()->input('job_id');
        $job_cost = request()->input('job_cost');
        $status = request()->input('status');
        
        $result = DbOperation::posterAcceptOffer($offer_id, $job_id, $job_cost, $status);

        $response = array();
        
        if($result != false){
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Poster Accept Offer';

            //name of job poster
            $posterName = $result['name'];
            //the fcm reg id of the fixer to be notified
            $regId = $result['fixer_fcm_id'];
            //email of fixer
            $fixerEmail = $result['fixer_email'];
            //name of fixer
            $fixerName = $result['fixer_name'];

            // notification title
            $title = 'Offer Accepted';

            // notification message
            $message = $posterName. ' accepted your offer for the job';
            
            //send mail to job poster
            Mail::to($fixerEmail)->send(new OfferNotification($fixerName, $title, $message.'. Please login and get to work.'));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);
            
            $response['error'] = false;
            $response['message'] = 'Offer status updated to 1 - accepted by poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not update offer status';
        }
        return response()->json($response);
    }
    
    //delete the offer from the offers table and notify the fixer that the
    //offer is rejected
    //update the job status to posted
    public function posterRejectOffer() {
        
        $offer_id = request()->input('offer_id');
        $job_id = request()->input('job_id');
        
        $result = DbOperation::posterRejectOffer($offer_id, $job_id);

        $response = array();
        
        if($result != false){
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Poster Reject Offer';

            //name of job poster
            $posterName = $result['name'];
            //the fcm reg id of the fixer to be notified
            $regId = $result['fixer_fcm_id'];
            //email of fixer
            $fixerEmail = $result['fixer_email'];
            //name of fixer
            $fixerName = $result['fixer_name'];

            // notification title
            $title = 'Offer Rejected';

            // notification message
            $message = $posterName. ' rejected your offer for the job';
            
            //send mail to job poster
            Mail::to($fixerEmail)->send(new OfferNotification($fixerName, $title, $message));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['message'] = 'Offer status updated to 2 - rejected by poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not update offer status';
        }
        DbOperation::deleteJobOffer($offer_id);
        return response()->json($response);
    }
    
    //delete the offer from the offers table and notify the poster that the
    //fixer is no longer interested in the job
    //update the job status to posted
    public function fixerRejectOffer() {
        $offer_id = request()->input('offer_id');
        $job_id = request()->input('job_id');
        
        $response = array();
        
        $result = DbOperation::fixerRejectOffer($offer_id, $job_id);
 
        if ($result != false) {
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Fixer Reject job';

            //name of job poster
            $fixerName = $result['name'];
            //the fcm reg id of the poster to be notified
            $regId = $result['poster_fcm_id'];
            //email of job poster
            $posterEmail = $result['poster_email'];
            //name of job poster
            $posterName = $result['poster_name'];

            // notification title
            $title = 'Job Rejected';

            // notification message
            $message = $fixerName. ' is no longer interested in your job';
            
            //send mail to job poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['message'] = 'Offer status updated to 3 - rejected by fixer';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not update offer status';
        }
        DbOperation::deleteJobOffer($offer_id);
        return response()->json($response);
    }
    
    //check if fixer has already made an offer for a job
    public function checkOfferAlreadyMade($userId, $jobId) {
        
        $result = DbOperation::checkOfferAlreadyMade($userId, $jobId);
        
        $response = array();
        
        if ($result) {
            //get the number of offers the fixer has already made
            //if 0 then no offer made yet by fixer
            $offer_count = $result['offer_made_count'];
            
            $response['error'] = false;
            $response['message'] = $offer_count;
            
            if($offer_count == 1){
                $response['is_offer_already_made'] = true;
            }else if ($offer_count == 0){
                $response['is_offer_already_made'] = false;
            }
		} else {
            $response['error'] = true;
            $response['message'] = 'Could not get offer count for this fixer';
        }
        return response()->json($response);
    }
    //set the actual start date for the job
    public function setJobStartDate() {
        $job_id = request()->input('job_id');
        
        $result = DbOperation::setJobStartDate($job_id);
        
        $response = array();
        
        if ($result) {
            
	        $responseData['error'] = false;
            $responseData['message'] = 'Job start date set';
		} else {
            $responseData['error'] = true;
            $responseData['message'] = 'Could not save job start date';
        }
        return response()->json($response);
    }
    
    //set the job status to 4 - started / job in progress
    //notify poster that the job has started
    public function fixerStartJob() {
        $offer_id = request()->input('offer_id');
        $job_id = request()->input('job_id');
        
        $response = array();
        
        $result = DbOperation::fixerStartJob($offer_id, $job_id);
 
        if ($result != false) {
            
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Fixer Started job';

            //name of job poster
            $fixerName = $result['name'];
            //the fcm reg id of the poster to be notified
            $regId = $result['poster_fcm_id'];
            //email of job poster
            $posterEmail = $result['poster_email'];
            //name of job poster
            $posterName = $result['poster_name'];

            // notification title
            $title = 'Job Started';

            // notification message
            $message = $fixerName. ' has started your job';
            
            //send mail to job poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['message'] = 'Job started';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not start job';
        }
        return response()->json($response);
    }
    
    //set the job status to 5 - completed / job is finished
    //notify poster that the job is complete
    public function fixerFinishJob() {
        $offer_id = request()->input('offer_id');
        $job_id = request()->input('job_id');
        
        $response = array();
        
        $result = DbOperation::fixerFinishJob($offer_id, $job_id);
 
        if ($result != false) {
            
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Fixer completed job';

            //name of job poster
            $fixerName = $result['name'];
            //the fcm reg id of the poster to be notified
            $regId = $result['poster_fcm_id'];
            //email of job poster
            $posterEmail = $result['poster_email'];
            //name of job poster
            $posterName = $result['poster_name'];

            // notification title
            $title = 'Job completed';

            // notification message
            $message = $fixerName. ' has finished your job';
            
            //send mail to job poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message));

            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            $json = '';
            $fcmResponse = '';

            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);
            logger($fcmResponse);

            $response['error'] = false;
            $response['message'] = 'Job finished';
        } else {
            $response['error'] = true;
            $response['message'] = 'Could not finish job';
        }
        return response()->json($response);
    }
    
    //to set the task fee as received from the poster
    //notify the fixer that the their offer is accepted
    public function setPaymentReceived() {
        
        $job_id = request()->input('job_id');
        $offer_id = request()->input('offer_id');
        $poster_id = request()->input('poster_id');
        $fixer_id = request()->input('fixer_id');
        $job_cost = request()->input('job_cost');
        $amnt_fixer_gets = request()->input('amnt_fixer_gets');
        $service_fee = request()->input('service_fee');
        $pay_method = request()->input('pay_method');
        $flw_trx_fee = request()->input('flw_trx_fee');

        $response = array();

        $result = DbOperation::setPaymentReceived($job_id, $poster_id,
        $fixer_id, $offer_id, $job_cost, $service_fee, $flw_trx_fee, $amnt_fixer_gets, $pay_method);
 
        if ($result) {
            
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            $fcm2 = new FCM();
            $push2 = new Push();
            
            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Payment Received';
            
            //name of job poster
            $posterName = $result['name'];
            //the fcm reg id of the poster to be notified
            $poster_regId = $result['poster_fcm_id'];
            //email of poster
            $posterEmail = $result['poster_email'];

            //name of the posted task
            $jobName = $result['task_name'];
            //the fcm reg id of the fixer to be notified
            $regId = $result['fixer_fcm_id'];
            //email of fixer
            $fixerEmail = $result['fixer_email'];
            //name of fixer
            $fixerName = $result['fixer_name'];
     
            // notification title
            $title = 'Payment Secured';
             
            // notification message
            $message = $posterName . ' paid for the task: '.$jobName.'. Go ahead and start the work';
            $posterMessage = 'You have successfully secured the funds for the task: '.$jobName . ' to be completed by: '.$fixerName;

            //send mail to fixer
            Mail::to($fixerEmail)->send(new OfferNotification($fixerName, $title, $message));

            //send mail to the poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $posterMessage));
            
            //fixer's push notification
            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);

            //poster's push notification
            $push2->setTitle($title);
            $push2->setMessage($posterMessage);
            $push2->setIsBackground(FALSE);
            $push2->setPayload($payload);
            
            $json = '';
            $fcmResponse = '';

            $json2 = '';
            $fcmResponse2 = '';
            
            //sending the fcm notification to the fixers phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($regId, $json);

            $json2 = $push2->getPush();
            $fcmResponse2 = $fcm2->send($poster_regId, $json2);
            logger($fcmResponse);
            logger($fcmResponse2);
            
            $response['error'] = false;
            $response['message'] = 'Payment marked as receievd';
        }else {
            $response['error'] = true;
            $response['message'] = 'Failed to mark payment as receievd';
        }
        
        return response()->json($response);
    }
    
    //check if the payment has been received
    public function checkPayIsReceived() {
        $job_id = request()->input('job_id');
        
        $result = DbOperation::checkPaymentReceived($job_id);
        
        $response = array();
        
        if ($result) {
            if($result['is_payment_received'] == 1){
                $response['error'] = false;
                $response['message'] = 'yes';
            }else{
                $response['error'] = false;
                $response['message'] = 'no';
            }
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Failed to check if payment is receievd';
        }
        return response()->json($response);
    }
    
    //to set the task fee as released
    public function releaseFixerPay() {
        $job_id = request()->input('job_id');
        $offer_id = request()->input('offer_id');
        
        $result = DbOperation::releaseFixerPay($job_id, $offer_id);
        
        $response = array();
        
        //get fcm ids of both fixer and poster
        if ($result) {
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $fcm2 = new FCM();
            $push = new Push();
            $push2 = new Push();
            
            // optional payload
            $posterPayload = array();
            $posterPayload['notice_type'] = 'Payment released';
            
            $fixerPayload = array();
            $fixerPayload['notice_type'] = 'Payment released';
            
            //the fcm reg id of the poster to be notified
            $posterFcmId = $result['poster_fcm_id'];
            $posterName = $result['poster_name'];
            $posterEmail = $result['poster_email'];
            
            //the fcm reg id of the fixer to be notified
            $fixerFcmId = $result['fixer_fcm_id'];
            $fixerName = $result['fixer_name'];
            $fixerEmail = $result['fixer_email'];
            $jobName = $result['job_name'];
     
            // notification title
            $title = 'Payment released';
             
            // notification message
            $message = 'You have released funds for '.$fixerName. ' for completing the job: '.$jobName;
            
            $message2 = $posterName .' has released your payment for completing the job: '.$jobName.'. Please be patient as we process and deposit your funds to your mobile money number in 4 days';
            
            //send mail to job poster
            Mail::to($posterEmail)->send(new OfferNotification($posterName, $title, $message));

            //send mail to fixer
            Mail::to($fixerEmail)->send(new OfferNotification($fixerName, $title, $message2));
            
            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($posterPayload);
            
            $push2->setTitle($title);
            $push2->setMessage($message2);
            $push2->setIsBackground(FALSE);
            $push2->setPayload($fixerPayload);
            
            $json = '';
            $fcmResponse = '';
            
            $json2 = '';
            $fcmResponse2 = '';
            
            //sending the fcm notification to the posters phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($posterFcmId, $json);
            logger($fcmResponse);
            
            //sending the fcm notification to the fixers phone
            $json2 = $push2->getPush();
            $fcmResponse2 = $fcm2->send($fixerFcmId, $json2);
            logger($fcmResponse2);
                
    	    $response['error'] = false;
            $response['message'] = 'Job complete, Payment released';
        } else {
            $response['error'] = true;
            $response['message'] = 'Failed to mark payment as released';
        }
        return response()->json($response);
    }
    
    //record the user's new wallet balance
    public function setNewWalletBalance() {
        
        $user_id = request()->input('user_id');
        $funds_added = request()->input('funds_added');
        $new_balance = request()->input('new_balance');

        $response = array();
        
        //base_path('libs/fcm/fcm.php');

        $result = DbOperation::setNewWalletBalance($user_id, $funds_added, $new_balance);
        
        //get fcm ids of both fixer and poster
         if ($result) {
            //create objects of FCM and Push classes
            $fcm = new FCM();
            $push = new Push();

            // optional payload
            $payload = array();
            $payload['notice_type'] = 'Wallet Topup';
            
            //the fcm reg id of the user to be notified
            $userFcmId = $result['user_fcm_id'];
     
            // notification title
            $title = 'FixApp Wallet Topup';
             
            // notification message
            $message = 'Your FixApp credit wallet balance has been toped up with UGX. '.$funds_added;
            
            $push->setTitle($title);
            $push->setMessage($message);
            $push->setIsBackground(FALSE);
            $push->setPayload($payload);
            
            $json = '';
            $fcmResponse = '';
            
            //sending the fcm notification to the posters phone
            $json = $push->getPush();
            $fcmResponse = $fcm->send($userFcmId, $json);
            
                
    	    $response['error'] = false;
            $response['message'] = 'Wallet balance toped up';
        } else {
            $response['error'] = true;
            $response['message'] = 'Failed to top up wallet balance';
        }
        return response()->json($response);
        
    }
    
    //get the user's wallet balance
    public function getWalletBalance() {
		
        $user_id = request()->input('user_id');

        $response = array();

        $result = DbOperation::getWalletBalance($user_id);
 
        if ($result) {
            $response['error'] = false;
            $response['userWalletBalance'] = $result['walletBalance'];
            $response['message'] = 'Got wallet balance';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Failed to get wallet balance';
        }
 
        return response()->json($response);
    }
    
    //get the completed jobs as a poster
    public function getJobsCompletedAsPoster($user_id, $page, $page_size) {
        
        $result = DbOperation::getJobsCompletedAsPoster($user_id, $page, $page_size);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['jobsCompletedAsPoster'] = $result;
            $countSql = 'CALL count_poster_completed_tasks(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got jobs completed as poster';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get jobs as a poster';
        }
        return response()->json($response);
    }
    
    //get the completed jobs as a fixer
    public function getJobsCompletedAsFixer($user_id, $page, $page_size) {
        
        $result = DbOperation::getJobsCompletedAsFixer($user_id, $page, $page_size);
        
        $response = array();
        
        if($result != false){
            $response['error'] = false;
            $response['jobsCompletedAsFixer'] = $result;
            $countSql = 'CALL count_fixer_completed_tasks(:user_id)';
            $params = array (':user_id' => $user_id);
            $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
            $response['message'] = 'Got jobs completed as a fixer';
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get jobs as a fixer';
        }
        return response()->json($response);
    }
    
    //submiting job poster rating
    public function submitPosterRating() {
		        
        $job_id = request()->input('job_id');
        $fixer_id = request()->input('fixer_id');
        $poster_id = request()->input('poster_id');
        $rating_value = request()->input('rating_value');
        $fixer_comment = request()->input('fixer_comment');
        
        $result = DbOperation::addPosterRating($job_id, $poster_id, $fixer_id, 
                $rating_value, $fixer_comment);

        $response = array();
 
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Poster rating added successfuly';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not add poster rating';
        }
 
        return response()->json($response);
    }
    
    //submiting fixer/tasker rating
    public function submitFixerRating() {
		        
        $job_id = request()->input('job_id');
        $fixer_id = request()->input('fixer_id');
        $poster_id = request()->input('poster_id');
        $rating_value = request()->input('rating_value');
        $poster_comment = request()->input('poster_comment');
        
        $result = DbOperation::addFixerRating($job_id, $poster_id, $fixer_id, $rating_value, $poster_comment);

        $response = array();
 
        if ($result) {
            $response['error'] = false;
            $response['message'] = 'Fixer rating added successfuly';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not add fixer rating';
        }
 
        return response()->json($response);
    }
    
    //getting the fixer rating
    public function fixerRating($fixer_id) {
        
        $result = DbOperation::getFixerRating($fixer_id);

        $response = array();
 
        if ($result) {
            $response['error'] = false;
            $response['fixerRating'] = $result;
            $response['message'] = 'Got Fixer rating';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get fixer rating';
        }
 
        return response()->json($response);
    }
    
    //getting the poster rating
    public function posterRating($poster_id) {
        
        $result = DbOperation::getPosterRating($poster_id);

        $response = array();
 
        if ($result) {
            $response['error'] = false;
            $response['posterRating'] = $result;
            $response['message'] = 'Got Poster rating';
            
        }else {
            $response['error'] = true;
            $response['message'] = 'Could not get poster rating';
        }
 
        return response()->json($response);
    }
    
    //getting all the reviews for a user
    public function getUserReviews($page, $page_size, $user_id, $user_role) {
        
        if($user_role == 'poster'){
            $result = DbOperation::GetUserReviewsAsPoster($page,$page_size,$user_id);
        
            $response = array();
            
            if($result != false){
                $response['error'] = false;
                $response['reviewsList'] = $result;
                $countSql = 'CALL count_reviews_as_poster(:user_id)';
                $params = array (':user_id' => $user_id);
                $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
                $response['message'] = 'Got user reviews as poster';
            }else {
                $response['error'] = true;
                $response['message'] = 'Cannot get user reviews as poster';
            }
            return response()->json($response);
            
        }elseif($user_role == 'fixer'){
            $result = DbOperation::GetUserReviewsAsFixer($page,$page_size,$user_id);
        
            $response = array();
            
            if($result != false){
                $response['error'] = false;
                $response['reviewsList'] = $result;
                $countSql = 'CALL count_reviews_as_fixer(:user_id)';
                $params = array (':user_id' => $user_id);
                $response['pages_count'] = DbOperation::HowManyPagesForResults($countSql, $params);
                $response['message'] = 'Got user reviews as fixer';
            }else {
                $response['error'] = true;
                $response['message'] = 'Cannot get user reviews as fixer';
            }
            return response()->json($response);
        }
    }
    
    //function to check parameters
    function isTheseParametersAvailable($required_fields)
    {
        $error = false;
        $error_fields = "";
        $request_params = $_REQUEST;

        // Handling PUT request params
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            //$app = \Slim\Slim::getInstance();
            parse_str($app->request()->getBody(), $request_params);
        }

        foreach ($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $response = array();
            $response["error"] = true;
            $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
            echo json_encode($response);
            return false;
        }
        return true;
    }
}