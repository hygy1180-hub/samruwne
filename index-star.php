<?php
/*

- @haamadh
- https://t.me/+1ptMBPwAaXQ2Mjc0

*/
ob_start();
$admin_id = '7379785386';
if($_GET['8039750253:AAG3Xrft2JOTe2j8sxvq4bAlUYWlcp1bbl8']){
    //احمي البوت من التحديثات الوهمية عبر $_GET
    $API_KEY = $_GET['8039750253:AAG3Xrft2JOTe2j8sxvq4bAlUYWlcp1bbl8'];
}else{
    //أو
    //توكن البوت
    $API_KEY = '1234:abcd';
}

define('API_KEY', $API_KEY);
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
        $res = json_decode($res);
        return $res;
    } else {
        $res = json_decode($res);
        return $res;
    }
}
 
function rand_text(){
    $abc = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
    $fol = '#'.$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)];
    return $fol;
}

$up = file_get_contents('php://input');
$update = json_decode($up);
if ($update->message) {
    $message = $update->message;
    $chat_id = $message->chat->id;
    $text = $message->text;
    $extext = explode(" ", $text);
    $first_name = $update->message->from->first_name;
    $username = $message->from->username;
    $id = $message->from->id;
    $message_id = $message->message_id;
    $entities = $message->entities;
    $language_code = $message->from->language_code;
    $tc = $update->message->chat->type;
    $re_message = $update->message->reply_to_message;
    $re_text = $re_message->text;
    $users = file_get_contents("users.txt");
    $ex_users = explode("\n", $users);

    if ($text  and !in_array($chat_id, $ex_users)) {
        file_put_contents('users.txt', $chat_id . "\n", FILE_APPEND);
        bot('sendMessage', [
            'chat_id' => $admin_id,
            'text' => "
لديك مشترك جديد في البوت 👤

الإسم : [$first_name](tg://user?id=$chat_id)
الآيدي : [$id](tg://user?id=$chat_id)
@$username
    ",
            'parse_mode' => 'MARKDOWN',
            'disable_web_page_preview' => 'true',
        ]);
    }

    if ($text) {
        if ($text == '/start')
        {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
أهلا بك..

يمكنك شراء ملف هذا البوت عبر نجوم تليجرام.

الملف مكتوب بلغة php، وسهل الفهم
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => 'شراء الملف']]
                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
    
        if ($text == 'شراء الملف'){
            $LabeledPrice = json_encode([
                [
                    'label' => "1",
                    //عدد النجوم
                    'amount' => 1
                ]
            ]);
            bot('sendInvoice', [
                'chat_id' => $chat_id,
                'title' => "شراء ملف البوت",
                'description' => "يمكنك عبر هذا الملف إضافة المدفوعات التلقائية عبر نجوم تليجرام إلى البوت الخاص بك",
                'payload' => rand_text(7),
                'provider_token' => "",
                'start_parameter' => "",
                'currency' => "XTR",
                'prices' => $LabeledPrice,
            ]);
        }
    }

    if($message->successful_payment){
        $currency = $message->successful_payment->currency;
        $total_amount = $message->successful_payment->total_amount;
        $invoice_payload = $message->successful_payment->invoice_payload;
        $telegram_payment_charge_id = $message->successful_payment->telegram_payment_charge_id;
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "
شكرا لك على إكمال عملية الدفع..

كمية الشحن: $total_amount 🌟
ايدي العملية: $invoice_payload
ايدي العملية لدى تليجرام: $telegram_payment_charge_id
            ",
            'parse_mode' => "MarkDown",
        ]);
        bot('senddocument',[
            'chat_id'=>$id,
            'document'=>new curlfile("index.php"),
            'caption'=> 'إليك ملف البوت، احفظه في مكان آمن..'
        ]);

        bot('sendMessage', [
            'chat_id' => $admin_id,
            'text' => "
عملية شراء ناجحة

[$first_name](tg://user?id=$id) - @$username

>> $total_amount 🌟
            ",
            'parse_mode' => "MarkDown",
        ]);

        // إذا حبيت تجرب الدفع وترجع رصيدك بعد الدفع احذف التعليق من الكود هذه
//         bot('refundStarPayment', [
//             'user_id' => $id,
//             'telegram_payment_charge_id' => $telegram_payment_charge_id
//         ]);
//         bot('sendMessage', [
//             'chat_id' => $chat_id,
//             'text' => "
// لقد قمنا بإرجاع النجوم إلى رصيدك..
//             ",
//             'parse_mode' => "MarkDown",
//         ]);
    }
}


if($update->pre_checkout_query){
    $id_query = $update->pre_checkout_query->id;
    $invoice_payload = $update->pre_checkout_query->invoice_payload;
    $total_amount = $update->pre_checkout_query->total_amount;
    
    bot('answerPreCheckoutQuery',[
        'pre_checkout_query_id' => $id_query,
        'ok' => true
        //'error_message' => 'خطأ، نفذ المنتج يا صديقي'
    ]);
}

