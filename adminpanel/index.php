<?php
        session_start();
        use GuzzleHttp\Client as Guzzle;
        use AfricasTalking\SDK\AfricasTalking;
        require '../vendor/autoload.php';
        
        // Enabling error reporting
        error_reporting(-1);
        ini_set('display_errors', 'On');
        
        include_once '../includes/config.php';
 
        //include fcm files
        require_once __DIR__ . '/../libs/fcm/fcm.php';
        require_once __DIR__ . '/../libs/fcm/push.php';
        
        //get the beyonic lib file 
        require_once __DIR__ . '/../libs/beyonic/lib/Beyonic.php';
        
        //the api key should be supplied for all api requests
        Beyonic::setApiKey('ceddef3b55b4f803a9ac43b2a92b62e37ec9017c');
 
        $firebase = new FCM();
        $push = new Push();
 
        // optional payload
        $payload = array();
        $payload['team'] = 'India';
        $payload['score'] = '5.6';
 
        // notification title
        $title = isset($_GET['title']) ? $_GET['title'] : '';
         
        // notification message
        $message = isset($_GET['message']) ? $_GET['message'] : '';
         
        // push type - single user / topic
        $push_type = isset($_GET['push_type']) ? $_GET['push_type'] : '';
         
        // whether to include to image or not
        $include_image = isset($_GET['include_image']) ? TRUE : FALSE;
        
        //get the collection request id
        $requestid = isset($GET['request']) ? $GET['request'] : '';
        
        // request type - single collection / topic
        $request_type = isset($_GET['request_type']) ? $_GET['request_type'] : '';
        
        // verification code
        $code = isset($_GET['v_code']) ? $_GET['v_code'] : '';
        
        //
        $random_code = '';
 
        $push->setTitle($title);
        $push->setMessage($message);
        if ($include_image) {
            $push->setImage('https://api.androidhive.info/images/minion.jpg');
        } else {
            $push->setImage('');
        }
        $push->setIsBackground(FALSE);
        $push->setPayload($payload);
 
 
        $json = '';
        $response = '';
 
        if ($push_type == 'topic') {
            $json = $push->getPush();
            $response = $firebase->sendToTopic('global', $json);
        } else if ($push_type == 'individual') {
            $json = $push->getPush();
            $regId = isset($_GET['regId']) ? $_GET['regId'] : '';
            $response = $firebase->send($regId, $json);
        }
        
        //beyonic stuff
        //*********make a payment - pay a mobile subscriber
        if ($request_type == 'make_payment'){
            try{
            $payment = [
                'phonenumber' => '+80000000001',
                'first_name' => 'Kennedy',
                'last_name' => 'Kennedy',
                'amount' => '12',
                'currency' => 'BXC',
                'description' => 'Per diem payment',
                'payment_type' => 'money',
                //'callback_url' => 'https://my.website/payments/callback',
                //'metadata' => array('id'=>'1234', 'name'=>'Lucy')
                ];
            
            //beyonic server url to send the curl request
            $url = BEYONIC_API_URL .'/payments';
            
            $headers = [
            'Authorization' => 'Token ceddef3b55b4f803a9ac43b2a92b62e37ec9017c',
            'Content-Type' => 'application/json'
                    ];
        
        $client = new Guzzle([
            'headers' => $headers
                ]);
        
        $json = $client->request('POST',$url, ['form_params' => $payment]);
        $response = $json->getBody()->getContents();
            }
            catch (Beyonic_Exception $ex) {
            $response = $ex->getMessage();
            }
            catch (Exception $ex) {
            $response = $ex->getMessage();
            }
        }
        //*********retrieve a single collection request
        else if ($request_type == 'single_collection_request'){
            try{
            //$requestid = [12926545];
            $requestid = [24500427];
            
            //beyonic server url to send the curl request
            $url = BEYONIC_API_URL . '/collections';
            
            $headers = [
            'Authorization' => 'Token ceddef3b55b4f803a9ac43b2a92b62e37ec9017c',
            'Content-Type' => 'application/json'
                    ];
        
        $client = new Guzzle([
            'headers' => $headers
                ]);
        
        $json = $client->request('GET',$url, ['query' => $requestid]);
        $response = $json->getBody()->getContents();
            }
            catch (Beyonic_Exception $ex) {
            $response = $ex->getMessage();
            }
            catch (Exception $ex) {
            $response = $ex->getMessage();
            }
        }
        //*********get single collection request
        else if ($request_type == 'single_collection'){
            try{
            //$requestid = [12926545];
            $requestid = [24500427];
            
            //beyonic server url to send the curl request
            $url = BEYONIC_API_URL .'/collectionrequests';
            
            $headers = [
            'Authorization' => 'Token ceddef3b55b4f803a9ac43b2a92b62e37ec9017c',
            'Content-Type' => 'application/json'
                    ];
        
        $client = new Guzzle([
            'headers' => $headers
                ]);
        
        $json = $client->request('GET',$url, ['query' => $requestid]);
        $response = $json->getBody()->getContents();
            }
            catch (Beyonic_Exception $ex) {
            $response = $ex->getMessage();
            }
            catch (Exception $ex) {
            $response = $ex->getMessage();
            }
        
        } 
        //*********collection request - prompt user to pay 
        else if ($request_type == 'collection_request'){
            try{
                $collection_request = [
                'phonenumber' => '+80000000004',
                'amount' => '25',
                'currency' => 'BXC',
                'reason' => 'paying for job done',
                'first_name' => 'Luke',
                'last_name' => 'Woods',
                'metadata' => array('my_id' => '123ASDAsd123'),
                'send_instructions' => true
                ];
                
        //beyonic server url to send the curl request
        $url = BEYONIC_API_URL . '/collectionrequests';
 
        //building headers for the request
        $headers = [
            'Authorization' => 'Token ceddef3b55b4f803a9ac43b2a92b62e37ec9017c',
            'Content-Type' => 'application/json'
        ];
        
        $client = new Guzzle([
            'headers' => $headers
                ]);
        
        $json = $client->request('POST',$url, ['form_params' => $collection_request]);
        
        $response = $json->getBody()->getContents();
 
        //Initializing curl to open a connection
//        $ch = curl_init();
// 
//        // Set the url, number of POST vars, POST data
//        curl_setopt($ch, CURLOPT_URL, $url);
//        
//        //setting the method as post
//        curl_setopt($ch, CURLOPT_POST, true);
// 
//        //adding headers 
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 
//        //disabling ssl support
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        
//        //adding the fields in json format 
//        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n\t\"phonenumber\": \"+80000000004\",\n\t\"first_name\": \"Ngari\",\n\t\"amount\": \"50\",\n\t\"currency\": \"BXC\"\n}");
// 
//        //finally executing the curl request 
//        $response = curl_exec($ch);
/*
        if ($response === FALSE) {
            //die('Curl failed: ' . curl_error($ch));
            echo $response->getStatusCode();
        }else{
            echo $response->getStatusCode();
        }
        */
 
        //Now close the connection
        //curl_close($ch);
            } catch (Beyonic_Exception $ex) {
            $response = $ex->getMessage();
            }
            catch (Exception $ex) {
            $response = $ex->getMessage();
            }
        }
        //----------AT verify phone number send code via sms
        else if($request_type == 'send_code'){
           $response = sendVC();
           $verifyResult = json_encode($response);
           $res = json_decode($verifyResult);
           //var_dump($res);
           echo $res->data->SMSMessageData->Recipients[0]->status;
        }
        //----------AT verify phone number send code via sms
        else if($request_type == 'verify_code'){
            global $random_code, $code;
            echo $_SESSION['verify_code'];
            if ($_SESSION['verify_code'] == $code){
                $response = 'Verification successful | random code = '.$_SESSION['verify_code'].' code received = '.$code;
            }else{
                $response = 'Verification NOT successful | random code = '.$_SESSION['verify_code'].' code received = '.$code;
            }
        }
        
        //function to send the verification code
       function sendVC(){
        global $random_code;
        
        $username = 'sandbox';
        $apiKey = '7968791cddb7a80e1cc02a67ad73bfb20b8c029f0dcb960092d66eabf0541acc';

        $AT = new AfricasTalking($username, $apiKey);

        // Router
        $router = new AltoRouter();
        
        $phone = '+256702880765';
            //generate the random number
            $random_code = rand(1000, 9999);
            $_SESSION['verify_code'] = $random_code;
            $sms = $AT->sms();
                $response = $sms->send(array(
                    'to' => $phone,
                    'message' => 'Your FixApp verfication code is ' . $random_code
                ));
            return $response;    
        }
        
        ?>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>FixApp | Firebase Cloud Messaging</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="//www.gstatic.com/mobilesdk/160503_mobilesdk/logo/favicon.ico">
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
 
        <style type="text/css">
            body{
            }
            div.container{
                width: 1000px;
                margin: 0 auto;
                position: relative;
            }
            legend{
                font-size: 30px;
                color: #555;
            }
            .btn_send{
                background: #00bcd4;
            }
            label{
                margin:10px 0px !important;
            }
            textarea{
                resize: none !important;
            }
            .fl_window{
                width: 400px;
                position: absolute;
                right: 0;
                top:100px;
            }
            pre, code {
                padding:10px 0px;
                box-sizing:border-box;
                -moz-box-sizing:border-box;
                webkit-box-sizing:border-box;
                display:block; 
                white-space: pre-wrap;  
                white-space: -moz-pre-wrap; 
                white-space: -pre-wrap; 
                white-space: -o-pre-wrap; 
                word-wrap: break-word; 
                width:100%; overflow-x:auto;
            }
 
        </style>
    </head>
    <body>
        
        <div class="container">
            <div class="fl_window">
                <div><img src="https://api.androidhive.info/images/firebase_logo.png" width="200" alt="Firebase"/></div>
                <br/>
                <?php if ($json != '') { ?>
                    <label><b>Request:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($json) ?></pre>
                    </div>
                <?php } ?>
                <br/>
                <?php if ($response != '') { ?>
                    <label><b>Response:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($response) ?></pre>
                    </div>
                <?php } ?>
 
            </div>
 
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Send to Single Device</legend>
 
                    <label for="redId">Firebase Reg Id</label>
                    <input type="text" id="redId" name="regId" class="pure-input-1-2" placeholder="Enter firebase registration id">
 
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="pure-input-1-2" placeholder="Enter title">
 
                    <label for="message">Message</label>
                    <textarea class="pure-input-1-2" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
 
                    <label for="include_image" class="pure-checkbox">
                        <input name="include_image" id="include_image" type="checkbox"> Include image
                    </label>
                    <input type="hidden" name="push_type" value="individual"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Send</button>
                </fieldset>
            </form>
            <br/><br/><br/><br/>
 
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Send to Topic `global`</legend>
 
                    <label for="title1">Title</label>
                    <input type="text" id="title1" name="title" class="pure-input-1-2" placeholder="Enter title">
 
                    <label for="message1">Message</label>
                    <textarea class="pure-input-1-2" name="message" id="message1" rows="5" placeholder="Notification message!"></textarea>
 
                    <label for="include_image1" class="pure-checkbox">
                        <input id="include_image1" name="include_image" type="checkbox"> Include image
                    </label>
                    <input type="hidden" name="push_type" value="topic"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Send to Topic</button>
                </fieldset>
            </form>
            <h3>Methods to test the Beyonic Api</h3>
            
            <p>Collection Request - Prompting user to pay</p>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Send collection request</legend>
 
                    <label for="redId">Firebase Reg Id</label>
                    <input type="text" id="redId" name="regId" class="pure-input-1-2" placeholder="Enter firebase registration id">
 
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="pure-input-1-2" placeholder="Enter title">
 
                    <label for="message">Message</label>
                    <textarea class="pure-input-1-2" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
 
                    <label for="include_image" class="pure-checkbox">
                        <input name="include_image" id="include_image" type="checkbox"> Include image
                    </label>
                    <input type="hidden" name="request_type" value="collection_request"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Send</button>
                </fieldset>
            </form>
            
            <p>Collection request - Receiving a collection request</p>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Get Single collection request</legend>
 
                    <label for="requestId">Request ID</label>
                    <input type="text" id="requestId" name="requestId" class="pure-input-1-2" placeholder="Enter request ID">
                    <input type="hidden" name="request_type" value="single_collection_request"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Get collection</button>
                </fieldset>
            </form>
            
            <p>Collection - Receiving a payment</p>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Get Single collection</legend>
 
                    <label for="requestId">Request ID</label>
                    <input type="text" id="requestId" name="requestId" class="pure-input-1-2" placeholder="Enter request ID">
                    <input type="hidden" name="request_type" value="single_collection"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Get collection</button>
                </fieldset>
            </form>
            
            <p>Payment - Making a payment to a mobile subscriber</p>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Make a payment to a mobile subscriber</legend>
 
                    <label for="requestId">Request ID</label>
                    <input type="text" id="requestId" name="requestId" class="pure-input-1-2" placeholder="Enter request ID">
                    <input type="hidden" name="request_type" value="make_payment"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Make Payment</button>
                </fieldset>
            </form>
            
            <h3>Testing AT SMS verification</h3>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <label for="requestId">Click to send verification code</label>
                    <input type="hidden" name="request_type" value="send_code"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Send Code</button>
                </fieldset>
            </form>
            <form class="pure-form pure-form-stacked" method="get">
                <fieldset>
                    <legend>Please enter the verification code sent to your phone</legend>
 
                    <label for="requestId">Verification Code</label>
                    <input type="text" id="requestId" name="v_code" class="pure-input-1-2" placeholder="Enter code">
                    <input type="hidden" name="request_type" value="verify_code"/>
                    <button type="submit" class="pure-button pure-button-primary btn_send">Verify</button>
                </fieldset>
            </form>
        </div>
    </body>
</html>