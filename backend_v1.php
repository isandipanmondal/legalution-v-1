<?php
global $response_status;
global $response_message;
include('instamojo_payment.php');

//assign values
$dbug=false;
$GLOBALS['response_status']=0;
$GLOBALS['response_message']='';
try{
    $functionName = (isset($_GET['func']))?trim($_GET['func']):'';
    if(!empty($functionName)){
        if(function_exists($functionName)){
            //$inputdata = file_get_contents('php://input');
            //$data = json_decode($inputdata, true);
            $data = $_POST;
            if($dbug){
                echo "<pre>";
                print_r($data);
            }
            $functionName($data);
        }
        else{
            $GLOBALS['response_message']="Request not found.";
            return_response();
        }
    }
    else{
        $GLOBALS['response_message']="invalid request";
        return_response();
    }
}catch(Exceptions $e){
    print_r($e);
    echo $e->getMessage();
    $GLOBALS['response_message']=$e->getMessage();
    return_response();
}

function return_response($extradata=array()){
    $returndata=array(
        'status'=>$GLOBALS['response_status'],
        'message'=>$GLOBALS['response_message'],
    );
    if(is_array($extradata) && !empty($extradata)){
        $returndata  = array_merge($returndata,$extradata);
    }
    die(json_encode($returndata));
}

//email section 
function send_mail($subject="", $message=""){
    if(empty($subject)){
        $subject="Subject not set";
    }
    if(empty($message)){
        $message="Not set";
    }
    $headers = "Bcc: i.sandipanmondal@gmail.com"; //saninfowb@gmail.com
    //$adminReceiver = "legalution.in@gmail.com";
    $adminReceiver = "mrintoryal@gmail.com";
    mail($adminReceiver,$subject,$message,$headers);
}

//payment gayway function 
function transaction_key($key_name=""){
    $key = "$key_name".date("Y/m")."/".rand(9999,1000000);
    return $key;
}

//calling function 
function trademark($request){
    $trademark_option = isset($request['trademark_option'])?$request['trademark_option']:'';
    $name = isset($request['name'])?$request['name']:'';
    $email = isset($request['email'])?$request['email']:'';
    $phone = isset($request['phone'])?$request['phone']:'';
    //set trade mark prices 
    $prices = array(
        'registration'=>'5558',
        'renewal'=>'5557',
        'objection'=>'5559',
        'opppsition'=>'5556',
    );
    $response_data=array();
    if(!empty($trademark_option)){
        //checked price section 
        $price = isset($prices[strtolower($trademark_option)])?$prices[strtolower($trademark_option)]:0;
        if($price>0){
            $transaction_id = transaction_key('TRD-');
            //now send a mail to admin about the user
            $subject="Customer looking for trademark.";
            $message = "Hi,\nCustomers details are as follows\n";
            $message .="\nCustomer Name : ".$name;
            $message .="\nCustomer Phone : ".$phone;
            $message .="\nCustomer Email : ".$email;
            $message = "\nCustomer looking for \n";
            $message = "\nTrademark : ".$trademark_option;
            $message = "\nPrice : Rs. ".number_format($price,2);
            $message = "\nTransaction ID : ".$transaction_id;
            //now send the mail 
            send_mail($subject,$message);

            //now need to open the payment url
            $purpose = "Trademark $trademark_option - $transaction_id";
            $payment_url = get_payment_link($price,$purpose,$name,$phone,$email);
            $response_data['payment_url']=$payment_url;
            $GLOBALS['response_status']=1;
        }
        else{
            $GLOBALS['response_message']="Trademark option price not found";
        }
    }
    else{
        $GLOBALS['response_message']="Invalid trademark option";
    }
    return_response($response_data);
}

?>