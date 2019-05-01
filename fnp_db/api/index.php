<?php
require 'config.php';
require 'Slim/Slim.php';
date_default_timezone_set("Asia/Manila");

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->post('/login','login'); /* User login */
$app->post('/signup','signup'); /* User Signup  */
$app->post('/postProduct','postProduct'); /* Products  */
$app->post('/getProducts','getProducts'); /* Products  */
$app->post('/profileAvatar','profileAvatar');
$app->post('/updateProfile','updateProfile');
$app->post('/sendMessage','sendMessage');
$app->post('/getMessage','getMessage');
$app->post('/getMessageList','getMessageList');
$app->post('/saveItem','saveItem');
$app->post('/getSavedItems','getSavedItems');
$app->post('/confirmAccount','confirmAccount');
$app->post('/createThread','createThread');
$app->post('/getThreads','getThreads');





// $app->get('/getFeed','getFeed'); /* User Feeds  */
// $app->post('/feed','feed'); /* User Feeds  */
// $app->post('/feedUpdate','feedUpdate'); /* User Feeds  */
// $app->post('/feedDelete','feedDelete'); /* User Feeds  */
// $app->post('/getImages', 'getImages');


$app->run();

/************************* USER LOGIN *************************************/
/* ### User login ### */
function getThreads(){
   
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $uid=$data->uid;
    $token=$data->token;
    $cid=$data->cid;
    try {
         
        if(1){
            $threadData = '';
            $db = getDB();
        
                $sql = "SELECT * FROM table_threads INNER JOIN users ON table_threads.uid=users.user_id WHERE table_threads.cid=:cid ORDER BY tid DESC";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("cid", $cid, PDO::PARAM_INT);

            $stmt->execute();
            $threadData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($threadData)
            echo '{"threadData": ' . json_encode($threadData) . '}';
            else
            echo '{"threadData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}


function createThread(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $uid=$data->user_id;
    $token=$data->token;
    $cid=$data->cid;
    $title=$data->title;
    $content=$data->content;

    
    $systemToken=apiToken($uid);
   
    try {
         
        if($systemToken == $token){
            
            $createThreadData = '';
            $db = getDB();
            $sql = "INSERT INTO table_threads (cid, uid, title, content, datetime) VALUES (:cid,:uid,:title,:content,:created)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("cid", $cid, PDO::PARAM_STR);
            $stmt->bindParam("uid", $uid, PDO::PARAM_STR);
            $created = date("l, d-M-y H:i:s ");
            $stmt->bindParam("title", $title, PDO::PARAM_STR);
            $stmt->bindParam("content", $content, PDO::PARAM_STR);
            $stmt->bindParam("created", $created, PDO::PARAM_STR);
            $stmt->execute();
            
            // $sql1 = "SELECT * FROM products WHERE user_id_fk=:user_id ORDER BY product_id DESC LIMIT 1";
            // $stmt1 = $db->prepare($sql1);
            // $stmt1->bindParam("user_id", $user_id, PDO::PARAM_INT);
            // $stmt1->execute();
            // $productData = $stmt1->fetch(PDO::FETCH_OBJ);

            $db = null;
            echo '{"createThreadData": ' . json_encode($createThreadData) . '}';

        } else{

            echo '{"error":{"text":"No access"}}';

        }
       
    } catch(PDOException $e) {

        echo '{"error":{"text":'. $e->getMessage() .'}}';

    }

}

function confirmAccount() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $status=$data->status;
    
    try {
        
        $db = getDB();
        $profileData = '';

        $sql1="UPDATE users SET status=:status WHERE user_id=:user_id";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bindParam("status", $status,PDO::PARAM_INT);
        $stmt1->bindParam("user_id", $user_id,PDO::PARAM_INT);
        $stmt1->execute();

        $db = null;
     
        if(!$profileData){
            $profileData = json_encode($profileData);
            echo '{"profileData": ' .$profileData . '}';
        } else {
            echo '{"error":{"text":"Enter valid data"}}';
        }

    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function profileAvatar(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    //$filename = $_FILES['filename']['name'];
    $imageB64=$data->imageB64;
    $file = basename($imageB64);
    move_uploaded_file(basename($imageB64), $file);   
    $systemToken=apiToken($user_id);
    try {
        if($systemToken == $token){
            $imageData = '';
            $db = getDB();
            $sql = "INSERT INTO imagesdata(b64,user_id_fk) VALUES(:b64,:user_id)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam("b64", $file, PDO::PARAM_STR);
            $stmt->execute();
            $db = null;
            echo '{"imageData": ' . json_encode($imageData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}



function getMessage(){
   
    try {
         
        if(1){
            $getMessageData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM messages INNER JOIN users ON messages.user_id=users.user_id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
          
            $stmt->execute();
            $getMessageData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($getMessageData)
            echo '{"getMessageData": ' . json_encode($getMessageData) . '}';
            else
            echo '{"getMessageData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function getMessageList(){
   
    try {
         
        if(1){
            $getMessageData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM messages INNER JOIN users ON messages.user_id=users.user_id WHERE messages.message_id IN (SELECT MAX(messages.message_id) FROM messages GROUP BY messages.user_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
          
            $stmt->execute();
            $getMessageData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($getMessageData)
            echo '{"getMessageData": ' . json_encode($getMessageData) . '}';
            else
            echo '{"getMessageData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function getSavedItems(){
   
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    
    try {
         
        if(1){
            $getSavedItemsData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM saved_items INNER JOIN products ON saved_items.product_id=products.product_id WHERE user_id=:user_id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                $stmt->execute();
            $getSavedItemsData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($getSavedItemsData)
            echo '{"getSavedItemsData": ' . json_encode($getSavedItemsData) . '}';
            else
            echo '{"getSavedItemsData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}



function saveItem(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    
    $user_id=$data->user_id;
    $token=$data->token;

    try {
        
        $db = getDB();
        $saveItemData ='';
        $sql = "SELECT * FROM saved_items WHERE user_id=:user_id and product_id=:product_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("user_id", $data->user_id, PDO::PARAM_INT);
        $stmt->bindParam("product_id", $data->product_id, PDO::PARAM_INT);
        $stmt->execute();
        $mainCount=$stmt->rowCount();
        $saveItemData = $stmt->fetch(PDO::FETCH_OBJ);
        
        if($saveItemData){
            
            echo '{"error":"Already saved."}';
         
        } else {

            $sql1 = "INSERT INTO saved_items (product_id, user_id) VALUES (:product_id, :user_id)";
            $stmt1 = $db->prepare($sql1);
            $stmt1->bindParam("product_id", $data->product_id, PDO::PARAM_INT);
            $stmt1->bindParam("user_id", $data->user_id, PDO::PARAM_INT);
            $stmt1->execute();

            echo '{"error":"Saved."}';
        }

        $db = null;
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function sendMessage(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $to_user_id=$data->to_user_id;
    $time=$data->time;
    $message=$data->message;
    $status=$data->status;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            
            $messageData = '';
            $db = getDB();
            $sql = "INSERT INTO messages (user_id, to_user_id, time, dte, message, status) VALUES (:user_id, :to_user_id, :time, :dte, :message, :status)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam("to_user_id", $to_user_id, PDO::PARAM_INT);
            $created_time = date("h:i A");
            $stmt->bindParam("time", $created_time, PDO::PARAM_STR);
            $created_date = date("m/d/Y");
            $stmt->bindParam("dte", $created_date, PDO::PARAM_STR);
            $stmt->bindParam("message", $message, PDO::PARAM_STR);
            $stmt->bindParam("status", $status, PDO::PARAM_STR);
            $stmt->execute();
            
            $sql1 = "SELECT * FROM messages WHERE user_id=:user_id ORDER BY message_id DESC LIMIT 1";
            $stmt1 = $db->prepare($sql1);
            $stmt1->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt1->execute();
            $messageData = $stmt1->fetch(PDO::FETCH_OBJ);

            $db = null;
            echo '{"messageData": ' . json_encode($messageData) . '}';

        } else{

            echo '{"error":{"text":"No access"}}';

        }
       
    } catch(PDOException $e) {

        echo '{"error":{"text":'. $e->getMessage() .'}}';

    }

}

function login() {
    
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    
    try {
        
        $db = getDB();
        $userData ='';
        $sql = "SELECT * FROM users WHERE (username=:username or email=:username) and password=:password ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $data->username, PDO::PARAM_STR);
        $password=hash('sha256',$data->password);
        $stmt->bindParam("password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $mainCount=$stmt->rowCount();
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        
        if(!empty($userData))
        {
            $user_id=$userData->user_id;
            $userData->token = apiToken($user_id);
        }
        
        $db = null;
         if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":"Bad request wrong username and password"}';
            }

           
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


/* ### User registration ### */
function signup() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $email=$data->email;
    $name=$data->name;
    $username=$data->username;
    $password=$data->password;
    
    try {
        
        $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
        $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
        
        //echo $email_check.'<br/>'.$email;
        
        if (strlen(trim($username))>0 && strlen(trim($password))>0 && strlen(trim($email))>0 && $email_check>0 && $username_check>0 && $password_check>0)
        {
            //echo 'here';
            $db = getDB();
            $userData = '';
            $sql = "SELECT user_id FROM users WHERE username=:username or email=:email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username,PDO::PARAM_STR);
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=time();
            if($mainCount==0)
            {
                
                /*Inserting user values*/
                $sql1="INSERT INTO users(username,password,email,name)VALUES(:username,:password,:email,:name)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("username", $username,PDO::PARAM_STR);
                $password=hash('sha256',$data->password);
                $stmt1->bindParam("password", $password,PDO::PARAM_STR);
                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->bindParam("name", $name,PDO::PARAM_STR);
                $stmt1->execute();
                
                $userData=internalUserDetails($email);
                
            }
            
            $db = null;
         

            if($userData){

               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';

                require 'sms.php';

                $result = itexmo("09060878388","Verification Code: " . $password,"TR-CHRIS878388_H62A6");

            } else {
               echo '{"error":{"text":"Enter valid data"}}';
            }

           
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateProfile() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    //$email=$data->email;
    $name=$data->name;
    //$username=$data->username;
    //$password=$data->password;
    $occupation=$data->occupation;
    $location=$data->location;
    $mobile=$data->mobile;
    
    try {
        
        //$username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
        //$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
        //$password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
        
        //echo $email_check.'<br/>'.$email;
        
        //if (strlen(trim($username))>0 && strlen(trim($password))>0 && $username_check>0 && $password_check>0)
        //{
            //echo 'here';
            $db = getDB();
            $profileData = '';

                $sql1="UPDATE users SET 
                name=:name,  
                occupation=:occupation, 
                location=:location, 
                mobile=:mobile
                WHERE user_id=:user_id";
                $stmt1 = $db->prepare($sql1);
                //$stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->bindParam("name", $name,PDO::PARAM_STR);
                $stmt1->bindParam("occupation", $occupation,PDO::PARAM_STR);
                $stmt1->bindParam("location", $location,PDO::PARAM_STR);
                $stmt1->bindParam("mobile", $mobile,PDO::PARAM_STR);
                $stmt1->bindParam("user_id", $user_id,PDO::PARAM_INT);
                $stmt1->execute();
                
                //$userData=internalUserDetails($email);
                
            
            $db = null;
         

            if(!$profileData){
               $profileData = json_encode($profileData);
                echo '{"profileData": ' .$profileData . '}';
            } else {
               echo '{"error":{"text":"Enter valid data"}}';
            }

           
        //}
        //else{
        //    echo '{"error":{"text":"Enter valid data"}}';
        //}
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function postProduct(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $product_name=$data->product_name;
    $description=$data->description;
    $available=$data->available;
    $price=$data->price;
    $tips=$data->tips;
    $category=$data->category;
    $image=$data->image;
    $file=basename($image);
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            
            if($image==""){
                $file="not.jpg";
            }

            $productData = '';
            $db = getDB();
            $sql = "INSERT INTO products (product_name, description, price, available, image, created, user_id_fk, tips, category) VALUES (:product_name,:description,:price, :available, :image, :created, :user_id, :tips, :category)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("product_name", $product_name, PDO::PARAM_STR);
            $stmt->bindParam("description", $description, PDO::PARAM_STR);
            $stmt->bindParam("price", $price, PDO::PARAM_STR);
            $stmt->bindParam("available", $available, PDO::PARAM_INT);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam("image", $file, PDO::PARAM_STR);
            $created = date("m/d/Y");
            $stmt->bindParam("created", $created, PDO::PARAM_STR);
            $stmt->bindParam("tips", $tips, PDO::PARAM_STR);
            $stmt->bindParam("category", $category, PDO::PARAM_STR);
            $stmt->execute();
            
            $sql1 = "SELECT * FROM products WHERE user_id_fk=:user_id ORDER BY product_id DESC LIMIT 1";
            $stmt1 = $db->prepare($sql1);
            $stmt1->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt1->execute();
            $productData = $stmt1->fetch(PDO::FETCH_OBJ);

            $db = null;
            echo '{"productData": ' . json_encode($productData) . '}';

        } else{

            echo '{"error":{"text":"No access"}}';

        }
       
    } catch(PDOException $e) {

        echo '{"error":{"text":'. $e->getMessage() .'}}';

    }

}


function getProducts(){
   
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    try {
         
        if(1){
            $productData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM users";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                $stmt->bindParam("lastCreated", $lastCreated, PDO::PARAM_STR);
          
            $stmt->execute();
            $productData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($productData)
            echo '{"productData": ' . json_encode($productData) . '}';
            else
            echo '{"productData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}


function getProfile(){
   
    try {
         
        if(1){
            $productData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM products ORDER BY product_id DESC LIMIT 15";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                $stmt->bindParam("lastCreated", $lastCreated, PDO::PARAM_STR);
          
            $stmt->execute();
            $productData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($productData)
            echo '{"productData": ' . json_encode($productData) . '}';
            else
            echo '{"productData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}





function email() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $email=$data->email;

    try {
       
        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
       
        if (strlen(trim($email))>0 && $email_check>0)
        {
            $db = getDB();
            $userData = '';
            $sql = "SELECT user_id FROM emailUsers WHERE email=:email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=time();
            if($mainCount==0)
            {
                
                /*Inserting user values*/
                $sql1="INSERT INTO emailUsers(email)VALUES(:email)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->execute();
                
                
            }
            $userData=internalEmailDetails($email);
            $db = null;
            if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid dataaaa"}}';
            }
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


/* ### internal Username Details ### */
function internalUserDetails($input) {
    
    try {
        $db = getDB();
        $sql = "SELECT user_id, name, email, username FROM users WHERE username=:input or email=:input";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("input", $input,PDO::PARAM_STR);
        $stmt->execute();
        $usernameDetails = $stmt->fetch(PDO::FETCH_OBJ);
        $usernameDetails->token = apiToken($usernameDetails->user_id);
        $db = null;
        return $usernameDetails;
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
}

function getFeed(){
  
   
    try {
         
        if(1){
            $feedData = '';
            $db = getDB();
          
                $sql = "SELECT * FROM feed  ORDER BY feed_id DESC LIMIT 15";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                $stmt->bindParam("lastCreated", $lastCreated, PDO::PARAM_STR);
          
            $stmt->execute();
            $feedData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($feedData)
            echo '{"feedData": ' . json_encode($feedData) . '}';
            else
            echo '{"feedData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function feed(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $lastCreated = $data->lastCreated;
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $feedData = '';
            $db = getDB();
            if($lastCreated){
                $sql = "SELECT * FROM feed WHERE user_id_fk=:user_id AND created < :lastCreated ORDER BY feed_id DESC LIMIT 5";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                $stmt->bindParam("lastCreated", $lastCreated, PDO::PARAM_STR);
            }
            else{
                $sql = "SELECT * FROM feed WHERE user_id_fk=:user_id ORDER BY feed_id DESC LIMIT 5";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            }
            $stmt->execute();
            $feedData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;

            if($feedData)
            echo '{"feedData": ' . json_encode($feedData) . '}';
            else
            echo '{"feedData": ""}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function feedUpdate(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $feed=$data->feed;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
         
            
            $feedData = '';
            $db = getDB();
            $sql = "INSERT INTO feed ( feed, created, user_id_fk) VALUES (:feed,:created,:user_id)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("feed", $feed, PDO::PARAM_STR);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $created = time();
            $stmt->bindParam("created", $created, PDO::PARAM_INT);
            $stmt->execute();
            


            $sql1 = "SELECT * FROM feed WHERE user_id_fk=:user_id ORDER BY feed_id DESC LIMIT 1";
            $stmt1 = $db->prepare($sql1);
            $stmt1->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt1->execute();
            $feedData = $stmt1->fetch(PDO::FETCH_OBJ);


            $db = null;
            echo '{"feedData": ' . json_encode($feedData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}



function feedDelete(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $feed_id=$data->feed_id;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $feedData = '';
            $db = getDB();
            $sql = "Delete FROM feed WHERE user_id_fk=:user_id AND feed_id=:feed_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam("feed_id", $feed_id, PDO::PARAM_INT);
            $stmt->execute();
            
           
            $db = null;
            echo '{"success":{"text":"Feed deleted"}}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }   
    
}

$app->post('/getImages', 'getImages');
function getImages(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    
    $systemToken=apiToken($user_id);
    try {
        if(1){
            $db = getDB();
            $sql = "SELECT b64 FROM imagesData";
            $stmt = $db->prepare($sql);
           
            $stmt->execute();
            $imageData = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"imageData": ' . json_encode($imageData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
?>
