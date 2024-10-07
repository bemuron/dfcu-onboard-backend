<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppFunctionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//route to validate user email
Route::post('/validateEmail', [AuthController::class, 'validateEmail']);

//route to validate auth code
Route::post('/validateCode', [AuthController::class, 'validateAuthCode'])->middleware('auth:sanctum');

//route to register users
Route::post('/register', [AuthController::class, 'RegisterUser'])->middleware('auth:sanctum'); 

//route to update users
Route::post('/updateUser', [AuthController::class, 'UpdateUser'])->middleware('auth:sanctum');

//route to update users
Route::post('/addUser', [AuthController::class, 'AddUser']);

//route to login users
Route::post('/login', [AuthController::class, 'LoginUser']);

//route to logout users
//deletes the user's fcm and api tokens
Route::post('/logout', [AuthController::class, 'LogoutUser']);

//retrieve user
Route::get('/getUser', [AuthController::class, 'getUser'])->middleware('auth:sanctum');

//getting all the staff
Route::get('/getAllEmployees/{user_id}', [AppFunctionsController::class, 'getAllEmployees'])->middleware('auth:sanctum'); 

//getting id images for a single employee
Route::get('/getIdImages/{user_id}', [AppFunctionsController::class, 'getIdImages']);

//getting API logs
Route::post('/getApiLog', [AuthController::class, 'getApiLog'])->middleware('auth:sanctum'); 

//get all the actegories
Route::get('/categories', [AppFunctionsController::class, 'GetCategories']);

//get the featured content
Route::get('/getFeaturedContent', [AppFunctionsController::class, 'GetFeaturedContent']);

//get the recently posted tasks
Route::get('/getRecentTasks', [AppFunctionsController::class, 'GetRecentTasks']);

/* * *
 * Updating user
 *  we use this url to update user's fcm registration id
 */
Route::post('/updateFcm/{user_id}', [AppFunctionsController::class, 'updateFcmID'])->middleware('auth:sanctum');

//update user details
Route::post('/updateUserDetails', [AppFunctionsController::class, 'updateUserDetails'])->middleware('auth:sanctum');

//saving the user's profile picture 
Route::post('/updateProfilePic', [AppFunctionsController::class, 'saveProfilePic'])->middleware('auth:sanctum');

//save the user's nin
Route::post('/saveNIN/{user_id}', [AppFunctionsController::class, 'saveNIN'])->middleware('auth:sanctum');

//saving the user's nin picture 
Route::post('/saveNinPic', [AppFunctionsController::class, 'saveNinPic'])->middleware('auth:sanctum');

//delete the user's nin pic
Route::post('/deleteNinPic', [AppFunctionsController::class, 'deleteNinPic'])->middleware('auth:sanctum');

//saving the user's portfolio picture 
Route::post('/savePortPic/{user_id}', [AppFunctionsController::class, 'savePortfolioImageNames'])->middleware('auth:sanctum');

//delete the user's portfolio pic
Route::post('/deletePortPic', [AppFunctionsController::class, 'deletePortfolioImages'])->middleware('auth:sanctum');

//saving the user's skill
Route::post('/saveUserSkill', [AppFunctionsController::class, 'saveUserSkills'])->middleware('auth:sanctum');

//saving the user's work
Route::post('/saveUserWork', [AppFunctionsController::class, 'saveUserWork'])->middleware('auth:sanctum');

//saving the user's language
Route::post('/saveUserLang', [AppFunctionsController::class, 'saveUserLang'])->middleware('auth:sanctum');

//saving the user's education
Route::post('/saveUserEdu', [AppFunctionsController::class, 'saveUserEdu'])->middleware('auth:sanctum');

//delete the user's skill
Route::post('/deleteUserSkill', [AppFunctionsController::class, 'deleteUserSkill'])->middleware('auth:sanctum');

//delete the user's work
Route::post('/deleteUserWork', [AppFunctionsController::class, 'deleteUserWork'])->middleware('auth:sanctum');

//delete the user's education
Route::post('/deleteUserEdu', [AppFunctionsController::class, 'deleteUserEdu'])->middleware('auth:sanctum');

//delete the user's language
Route::post('/deleteUserLang', [AppFunctionsController::class, 'deleteUserLang'])->middleware('auth:sanctum');

//get the user's skills
Route::get('/getUserSkills/{user_id}', [AppFunctionsController::class, 'getUserSkills'])->middleware('auth:sanctum');

//get the user's work
Route::get('/getUserWork/{user_id}', [AppFunctionsController::class, 'getUserWork'])->middleware('auth:sanctum');

//get the user's education
Route::get('/getUserEdu/{user_id}', [AppFunctionsController::class, 'getUserEdu'])->middleware('auth:sanctum');

//get the user's language
Route::get('/getUserLang/{user_id}', [AppFunctionsController::class, 'getUserLang'])->middleware('auth:sanctum');

//get the user's portfolio images
Route::get('/getUserPortfolio/{user_id}', [AppFunctionsController::class, 'getUserPortImages'])->middleware('auth:sanctum');

//get the user's nin images
Route::get('/getUserNinImages/{user_id}', [AppFunctionsController::class, 'getUserNinImages'])->middleware('auth:sanctum');

//get the user nin
Route::get('/getUserNin/{user_id}', [AppFunctionsController::class, 'getUserNin'])->middleware('auth:sanctum');

//get the visitor profile
Route::get('/getVisitorDetails/{user_id}', [AppFunctionsController::class, 'getVisitorDetails'])->middleware('auth:sanctum');

//create a job/task
Route::post('/createJob', [AppFunctionsController::class, 'CreateJob'])->middleware('auth:sanctum');

//saving the job pic
Route::post('/uploadUserJobImage/{job_id}', [AppFunctionsController::class, 'uploadUserJobImage'])->middleware('auth:sanctum');

//deleting the job image
Route::post('/deleteJobPic', [AppFunctionsController::class, 'deleteJobPic'])->middleware('auth:sanctum');

//update job details when no image is attached
Route::post('/updateJobWithoutImage/{job_id}', [AppFunctionsController::class, 'updateJobWithoutImage'])->middleware('auth:sanctum');

//updating job date and time
Route::post('/updateJobDateTime/{job_id}', [AppFunctionsController::class, 'updateJobDateTime'])->middleware('auth:sanctum');

//updating job budget
Route::post('/updateJobBudget/{job_id}', [AppFunctionsController::class, 'updateJobBudget'])->middleware('auth:sanctum');

//helps users report issues they notice about the jobs posted
Route::post('/reportJob', [AppFunctionsController::class, 'reportJob'])->middleware('auth:sanctum');

//helps a user cancel the task they posted
Route::post('/cancelTask', [AppFunctionsController::class, 'cancelTask'])->middleware('auth:sanctum');

//getting all the jobs by status for this user
//0 - draft, 1 - posted, 2 - assigned, 3 - offers, 4 - in progress, 5 - completed by fixer, 6 - completed by poster
Route::get('/getJobsByStatus/{user_id}/{status}', [AppFunctionsController::class, 'getJobsByStatus'])->middleware('auth:sanctum');

//getting all the ad images for an ad
Route::get('/getAdImages/{ad_id}', [AppFunctionsController::class, 'getAdImages']);

//delete the ad pic
Route::post('/deleteAdPic', [AppFunctionsController::class, 'deleteAdPic'])->middleware('auth:sanctum');

//getting the details of an advert
Route::get('/getAdDetails/{ad_id}', [AppFunctionsController::class, 'getAdDetails']);

//getting the details of a job depending on the status
Route::get('/getJobDetailsByStatus/{job_id}', [AppFunctionsController::class, 'getJobDetailsByStatus'])->middleware('auth:sanctum');

//getting all the jobs by and for this user
// not status based
Route::get('/getAllJobsForUser/{user_id}', [AppFunctionsController::class, 'getAllJobsForUser'])->middleware('auth:sanctum');

//getting the details of a job minus status
Route::get('/getJobDetails/{job_id}', [AppFunctionsController::class, 'getJobDetails']);

//post a new ad
Route::post('/postNewAd', [AppFunctionsController::class, 'postNewAd'])->middleware('auth:sanctum');

//edit a new ad
Route::post('/editAd', [AppFunctionsController::class, 'editAd'])->middleware('auth:sanctum');

//user delete a posted ad
Route::post('/deleteAd', [AppFunctionsController::class, 'deleteAd'])->middleware('auth:sanctum');

//helps users report issues they notice about the ads posted
Route::post('/reportAd', [AppFunctionsController::class, 'reportAd'])->middleware('auth:sanctum');

//toggle user like / unlike ad
Route::get('/toggleAdLike/{userId}/{adId}/{value}', [AppFunctionsController::class, 'toggleAdLike'])->middleware('auth:sanctum');

//getting all the jobs for the user to browse
// 0 - draft, 1 - posted, 2 - assigned, 3 - offers, 4 - in progress, 5 - completed by fixer, 6 - completed by poster 
Route::get('/getJobsForBrowsing/{page}/{page_size}', [AppFunctionsController::class, 'getJobsForBrowsing']);

//getting all the ads by a single poster for the user to browse
// 0 - draft, 1 - posted 
Route::get('/getPosterAds/{poster_id}/{page}/{page_size}', [AppFunctionsController::class, 'getPosterAds']);

//getting all the ads for the user to browse
// 0 - draft, 1 - posted 
Route::get('/getAdsForBrowsing/{page}/{page_size}', [AppFunctionsController::class, 'getAdsForBrowsing']);

//getting all the pros for the user to browse
Route::get('/getProsForBrowsing/{page}/{page_size}', [AppFunctionsController::class, 'getProsForBrowsing']); 

//getting all the messages for a user
Route::get('/getAllMessages/{user_id}/{page}/{page_size}', [AppFunctionsController::class, 'getAllMessages'])->middleware('auth:sanctum');

//getting all the messages between two users
Route::get('/getMessageDetails/{to_id}/{from_id}/{page}/{page_size}', [AppFunctionsController::class, 'getMessageDetails'])->middleware('auth:sanctum');

//save a message sent
Route::post('/saveSentMessage', [AppFunctionsController::class, 'saveSentMessage'])->middleware('auth:sanctum');

//save the user's verified payment phone
Route::post('/saveVerifiedPayPhone/{user_id}', [AppFunctionsController::class, 'saveVerifiedPayPhone'])->middleware('auth:sanctum');

//search for jobs based on the search query entered by the user
Route::get('/searchJobs/{searchQuery}/{page}/{page_size}', [AppFunctionsController::class, 'searchJobs'])->middleware('auth:sanctum');

//get the similar ads to what the user was viewing
Route::get('/getSimilarAds/{category_id}/{title}/{page}/{page_size}', [AppFunctionsController::class, 'getSimilarAds']);

//saving an offer a fixr has made
Route::post('/saveOffer', [AppFunctionsController::class, 'saveOffer'])->middleware('auth:sanctum');

//update an offer a tasker had made
Route::post('/updateOffer/{offer_id}', [AppFunctionsController::class, 'updateOffer'])->middleware('auth:sanctum');

//getting all the jobs a fixer has made an offer for
Route::get('/getOffersMade/{user_id}/{page}/{page_size}', [AppFunctionsController::class, 'getOffersMade'])->middleware('auth:sanctum');

//getting all the job offers accepted by posters for jobs this fixer made offers to
Route::get('/getOffersAcceptedForFixer/{user_id}/{page}/{page_size}', [AppFunctionsController::class, 'getOffersAcceptedForFixer'])->middleware('auth:sanctum');

//getting all the job offers accepted by this poster
Route::get('/getOffersAcceptedForPoster/{user_id}/{page}/{page_size}', [AppFunctionsController::class, 'getOffersAcceptedForPoster'])->middleware('auth:sanctum');

//getting all the jobs by this user/poster which have offers made
Route::get('/getOffersForJobs/{user_id}/{page}/{page_size}', [AppFunctionsController::class, 'getOffersForJobs'])->middleware('auth:sanctum');

//getting all the offers received for a single job
Route::get('/getOffersForSingleJob/{user_id}/{job_id}/{page}/{page_size}', [AppFunctionsController::class, 'getOffersForSingleJob'])->middleware('auth:sanctum');

//getting the details of an offer for the poster
Route::get('/getOfferDetailsForPoster/{offer_id}', [AppFunctionsController::class, 'getOfferDetailsForPoster'])->middleware('auth:sanctum');

//getting the details of an offer for the fixer
Route::get('/getOfferDetailsForFixer/{offer_id}', [AppFunctionsController::class, 'getOfferDetailsForFixer'])->middleware('auth:sanctum');

//getting the details of a job in progress for the fixer
Route::get('/getJIPDetails/{offer_id}', [AppFunctionsController::class, 'getJIPDetails'])->middleware('auth:sanctum');

//updating offer seen status to 1 - seen by poster
Route::post('/updateOfferSeenStatus/{offer_id}', [AppFunctionsController::class, 'updateOfferSeenStatus'])->middleware('auth:sanctum');

//check if the user has already accepted another offer for this same job
Route::get('/checkOfferAlreadyAccepted/{jobId}/{userId}', [AppFunctionsController::class, 'checkOfferAlreadyAccepted'])->middleware('auth:sanctum');

//updating offer to 1 - accepted status
//job status to 2 - assigned to this fixer
Route::post('/posterAcceptOffer', [AppFunctionsController::class, 'posterAcceptOffer'])->middleware('auth:sanctum');

//delete the offer from the offers table and notify the fixer that the
//offer is rejected
//update the job status to posted
Route::post('/posterRejectOffer', [AppFunctionsController::class, 'posterRejectOffer'])->middleware('auth:sanctum');

//delete the offer from the offers table and notify the poster that the
//fixer is no longer interested in the job
//update the job status to posted
Route::post('/fixerRejectOffer', [AppFunctionsController::class, 'fixerRejectOffer'])->middleware('auth:sanctum');

//check if fixer has already made an offer for a job
Route::get('/checkOfferAlreadyMade/{userId}/{jobId}', [AppFunctionsController::class, 'checkOfferAlreadyMade'])->middleware('auth:sanctum');

//set the actual start date for the job
Route::post('/setJobStartDate', [AppFunctionsController::class, 'setJobStartDate'])->middleware('auth:sanctum');

//set the job status to 4 - started / job in progress
//notify poster that the job has started
Route::post('/fixerStartJob', [AppFunctionsController::class, 'fixerStartJob'])->middleware('auth:sanctum');

//set the job status to 5 - completed / job is finished
//notify poster that the job is complete
Route::post('/fixerFinishJob', [AppFunctionsController::class, 'fixerFinishJob'])->middleware('auth:sanctum');

//to set the task fee as received from the poster
//notify the fixer that the their offer is accepted
Route::post('/setPaymentReceived', [AppFunctionsController::class, 'setPaymentReceived'])->middleware('auth:sanctum');

//check if the payment has been received
Route::post('/checkPayIsReceived', [AppFunctionsController::class, 'checkPayIsReceived'])->middleware('auth:sanctum');

//to set the task fee as released
Route::post('/releaseFixerPay', [AppFunctionsController::class, 'releaseFixerPay'])->middleware('auth:sanctum');

//record the user's new wallet balance
Route::post('/setNewWalletBalance', [AppFunctionsController::class, 'setNewWalletBalance'])->middleware('auth:sanctum');

//get the user's wallet balance
Route::post('/getWalletBalance', [AppFunctionsController::class, 'getWalletBalance'])->middleware('auth:sanctum');

//get the completed jobs as a poster
Route::get('/getJobsCompletedAsPoster/{user_id}/{page}/{page_size}', 
        [AppFunctionsController::class, 'getJobsCompletedAsPoster'])->middleware('auth:sanctum');

//get the completed jobs as a fixer
Route::get('/getJobsCompletedAsFixer/{user_id}/{page}/{page_size}', 
        [AppFunctionsController::class, 'getJobsCompletedAsFixer'])->middleware('auth:sanctum');

//submiting job poster rating
Route::post('/submitPosterRating', [AppFunctionsController::class, 'submitPosterRating'])->middleware('auth:sanctum');

//submiting fixer/tasker rating
Route::post('/submitFixerRating', [AppFunctionsController::class, 'submitFixerRating'])->middleware('auth:sanctum');

//getting the fixer rating
Route::get('/fixerRating/{fixer_id}', [AppFunctionsController::class, 'fixerRating'])->middleware('auth:sanctum');

//getting the poster rating
Route::get('/posterRating/{poster_id}', [AppFunctionsController::class, 'posterRating'])->middleware('auth:sanctum');

//getting all the reviews for a user
Route::get('/getUserReviews/{page}/{page_size}/{user_id}/{user_role}', 
        [AppFunctionsController::class, 'getUserReviews'])->middleware('auth:sanctum');

//getting all the reviews for a user
Route::get('/getUserReviews/{page}/{page_size}/{user_id}/{user_role}', 
        [AppFunctionsController::class, 'getUserReviews'])->middleware('auth:sanctum');
