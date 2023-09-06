<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8mb4">
    <link rel="stylesheet" href="design.css">
    <script src="local_brain.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();
$servername = "sql6.webzdarma.cz";
$user = "";
$password = "";
$db_name = "whitefoxbank7108";

$conn = new mysqli($servername, $user, $password, $db_name);
$conn->set_charset("utf8mb4");

if ($conn->connect_error){
    die("ERROR :( - ".$conn->connect_error);
}
//connected
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['client_number']) and isset($_POST['login_pass'])){
    login();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['password_reset'])){
    password_reset();
}
if (isset($_GET['log_out']) ) {
    log_out();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['register_name_form']) and isset($_POST['register_surname_form']) and isset($_POST['register_nickname_form']) and isset($_POST['register_email_form']) and isset($_POST['register_phone_form']) and isset($_POST['register_password1_form']) and isset($_POST['register_password2_form'])){
    register();
}
if (isset($_GET['action']) && $_GET['action'] == 'main_page') {
    $top_bar_check = 0;
    main_page();
}
if (isset($_GET['action']) && $_GET['action'] == 'main_page_admin') {
    main_page_admin();
}
if (isset($_GET['delete']) ) {
    main_page_admin_delete();
}
if (isset($_GET['ban_user'])){
    main_page_admin_ban();
}
if (isset($_GET['unban_user'])){
    main_page_admin_unban();
}
if (isset($_GET['edit_user'])){
    main_page_admin_edit_user();
}
if (isset($_GET['edit_local_user'])){
    edit_local_user($_SESSION["user_id"]);
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['user_user_id']) and isset($_POST['user_name_form']) and isset($_POST['user_surname_form']) and isset($_POST['user_nickname_form']) and isset($_POST['user_email_form']) and isset($_POST['user_phone_form']) and isset($_POST['user_password1_form']) and isset($_POST['user_password2_form'])){
    edit_local_user_form_success();
    //if ($_POST['user_password1_form'] === ''){
        //edit_local_user_form_success();
    //}
    //elseif ($_POST['user_password1_form'] === $row["password"]){
        //edit_local_user_form_success();
    //} else {
        //edit_local_user_form_success();
    //}
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['edit_user_id']) and isset($_POST['edit_name_form']) and isset($_POST['edit_surname_form']) and isset($_POST['edit_nickname_form']) and isset($_POST['edit_email_form']) and isset($_POST['edit_phone_form']) and isset($_POST['edit_password1_form'])){
    main_page_admin_edit_user_form();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['card_name'])){
    add_card();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['card_delete'])){
    card_delete();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['card_id'])){
    show_stats($_POST['card_id']);
}
if(isset($_GET['account_see_all'])){
    account_see_all();
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['account_add_money']) and $_POST['account_send_money'] === ""){
    money_transaction_plus($_SESSION["card_id"]);
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['account_send_money']) and $_POST['account_add_money'] === ""){
    money_transaction_minus($_SESSION["card_id"]);
}
//login-------------------------------
function login(){
    $_SESSION["top_bar_check"] = 0;
    global $conn;
    $client_number = isset($_POST["client_number"]) ? $_POST["client_number"] : "";
    $login_password = isset($_POST["login_pass"]) ? $_POST["login_pass"] : "";

    $sql_verify = "Select * from user where user_key='".$client_number."' and password='".$login_password."';";
    $result = $conn->query($sql_verify);
    $user_number = $result->fetch_assoc();
    if ($result->num_rows > 0 and $user_number["admin"] < 1 and $user_number["ban_status"] < 1) {
        $_SESSION['user_id'] = $user_number["user_id"];
        $_SESSION["user_key"] = $user_number["user_key"];
        $_SESSION["u_name"] = $user_number["user_name"];
        $_SESSION["u_surname"] = $user_number["user_surname"];
        $_SESSION["u_nickname"] = $user_number["nickname"];
        $_SESSION["u_email"] = $user_number["email"];
        $_SESSION["u_phone"] = $user_number["phone"];
        $_SESSION["u_admin"] = $user_number["admin"];
        main_page();
    }
    elseif ($result->num_rows > 0 and $user_number["admin"] > 0 and $user_number["ban_status"] < 1){
        $_SESSION['user_id'] = $user_number["user_id"];
        $_SESSION["user_key"] = $user_number["user_key"];
        $_SESSION["u_name"] = $user_number["user_name"];
        $_SESSION["u_surname"] = $user_number["user_surname"];
        $_SESSION["u_nickname"] = $user_number["nickname"];
        $_SESSION["u_email"] = $user_number["email"];
        $_SESSION["u_phone"] = $user_number["phone"];
        $_SESSION["u_admin"] = $user_number["admin"];
        main_page_admin();
    }
    elseif ($result->num_rows > 0 and $user_number["ban_status"] > 0){
        $_SESSION['user_id'] = $user_number["user_id"];
        $_SESSION["user_key"] = $user_number["user_key"];
        $_SESSION["u_name"] = $user_number["user_name"];
        $_SESSION["u_surname"] = $user_number["user_surname"];
        $_SESSION["u_nickname"] = $user_number["nickname"];
        $_SESSION["u_email"] = $user_number["email"];
        $_SESSION["u_phone"] = $user_number["phone"];
        $_SESSION["u_admin"] = $user_number["admin"];
        main_page_banned();
    }
    else{
        failed_login();
    }
}
//password------------------------------
function password_reset(){
    global $conn;

    $forgot_email = isset($_POST["password_reset"]) ? $_POST["password_reset"] : "";
    $sql_search_user = "SELECT * FROM `user` WHERE email = '".$forgot_email."';";
    $result = $conn->query($sql_search_user);
    $row = $result->fetch_assoc();

    $id_char = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '!', '@', '#', '$', '%', '&', '*', 1, 2, 3, 4, 5, 6, 7, 8, 9];
    $id_generator_lengh = rand(4, 10);
    $id_generator_storage = [];
    for ($i = 0; $i <= $id_generator_lengh; $i++) {
        $random_generator = rand(0, 41);
        $id_generator_storage[$i] = $id_char[$random_generator];
    }
    if ($result->num_rows > 0){
        for ($i = 0; $i <= $id_generator_lengh; $i++) {
            $random_generator = rand(0, 41);
            $id_generator_storage[$i] = $id_char[$random_generator];
        }
    }


    $sql_update_password = "UPDATE `user` SET `password`= '".implode('',$id_generator_storage)."' WHERE user_id =".$row["user_id"];
    echo "<br>";
    //var_dump(implode('',$id_generator_storage));
    $result_update_password = $conn->query($sql_update_password);
    send_email_reset($row["user_name"], $row["user_surname"],$forgot_email, implode('',$id_generator_storage));

    readfile("password_reset_confirm.html");
}
//log_out------------------------------
function log_out(){
    global $conn;

    $_SESSION['user_id'] = NULL;
    $_SESSION["user_key"] = NULL;
    $_SESSION["u_name"] = NULL;
    $_SESSION["u_surname"] = NULL;
    $_SESSION["u_nickname"] = NULL;
    $_SESSION["u_email"] = NULL;
    $_SESSION["u_phone"] = NULL;
    $_SESSION["u_admin"] = NULL;
    $_SESSION["top_bar_check"] = 0;
    session_destroy();
    header("Location: log_out.php");
    exit();
}
//register----------------------------
function register() {
    global $conn;
    $register_name = isset($_POST['register_name_form']) ? $_POST['register_name_form'] : "";
    $register_surname = isset($_POST['register_surname_form']) ? $_POST['register_surname_form'] : "";
    $register_nickname = isset($_POST['register_nickname_form']) ? $_POST['register_nickname_form'] : "";
    $register_email = isset($_POST['register_email_form']) ? $_POST['register_email_form'] : "";
    $register_phone = isset($_POST['register_phone_form']) ? $_POST['register_phone_form'] : "";
    $register_password = isset($_POST['register_password1_form']) ? $_POST['register_password1_form'] : "";
    $register_password2 = isset($_POST['register_password2_form']) ? $_POST['register_password2_form'] : "";

    //id_generator
    $id_char = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '!', '@', '#', '$', '%', '&', '*', 1, 2, 3, 4, 5, 6, 7, 8, 9];
    $id_generator_lengh = rand(4, 10);
    $id_generator_storage = [];
    for ($i = 0; $i <= $id_generator_lengh; $i++) {
        $random_generator = rand(0, 41);
        $id_generator_storage[$i] = $id_char[$random_generator];
    }
    $sql_control = "SELECT * FROM user WHERE user_key='".implode('', $id_generator_storage)."'";
    $result = $conn->query($sql_control);
    while ($result->num_rows > 0){
        $result = $conn->query($sql_control);
        for ($i = 0; $i <= $id_generator_lengh; $i++) {
            $random_generator = rand(0, 41);
            $id_generator_storage[$i] = $id_char[$random_generator];
        }
    }
    //registration algorithm
    $sql_verify_nickname = "SELECT * FROM user WHERE nickname = '".$register_nickname."';";
    $sql_verify_email = "SELECT * FROM user WHERE email = '".$register_email."';";
    $result_nickname = $conn->query($sql_verify_nickname);
    $result_email = $conn->query($sql_verify_email);
    if ($register_password === $register_password2 && $result_nickname->num_rows === 0 && $result_email->num_rows === 0){
        $sql_register = "INSERT INTO user(user_key,user_name, user_surname, nickname, email, phone, password) VALUES ('".implode('', $id_generator_storage)."','".$register_name."', '".$register_surname."', '".$register_nickname."', '".$register_email."', ".$register_phone.", '".$register_password."');";
        $result = $conn->query($sql_register);
        if ($result == False) {
            echo "Error " . $sql_register . "<br>" . $conn -> error;
        } else {
            send_email(implode('', $id_generator_storage),$register_name,$register_surname,$register_email);
            readfile("register_confirm.html");
        }
    }
    elseif ($result_email->num_rows > 0){
        readfile("register.html");
        echo "<p style='position: absolute; top: 53.5%; right: 30.5%; font-size: 1.8vh;'>already in-use</p>";
        echo '<svg style="position:absolute; top:54%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
    }
    elseif ($result_nickname->num_rows > 0) {
        readfile("register.html");
        echo "<p style='position: absolute; top: 48%; right: 30.5%; font-size: 1.8vh;'> already taken</p>";
        echo '<svg style="position:absolute; top:48.5%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
    }
    //elseif (!$register_name){
        //readfile("register.html");
        //echo "<p style='position: absolute; top: 45%; right: 30.5%; font-size: 1.8vh;'>*can't live blank</p>";
    //}
}
function send_email($registration_user_key,$registration_user_name,$registration_user_surname,$registration_user_email) {
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function

    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'whitefox.banking';                     //SMTP username
        $mail->Password = '';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('whitefox.banking@gmail.com', 'White Fox');
        $mail->addAddress($registration_user_email, ''.$registration_user_name.' '.$registration_user_surname.'');     //Add a recipient

        //Content
        //$mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Welcome - Registration key';
        $mail->Body = 'Hello '.$registration_user_name.' '.$registration_user_surname.'!<br><br>Welcome to WhiteFox!<br>Here is your registration key - <b>'.$registration_user_key.'</b><br><br>We wish you a plesent day,<br>Team WhiteFox';
        $mail->AltBody = 'Hello!<br>This is your registration key - '.$registration_user_key;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
function send_email_reset($registration_user_name,$registration_user_surname,$registration_user_email,$new_password) {
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function

    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'whitefox.banking';                     //SMTP username
        $mail->Password = '';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('whitefox.banking@gmail.com', 'White Fox');
        $mail->addAddress($registration_user_email, ''.$registration_user_name.' '.$registration_user_surname.'');     //Add a recipient

        //Content
        //$mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Password reset!';
        $mail->Body = 'Hello '.$registration_user_name.' '.$registration_user_surname.'!<br><br>It seems you have forgot your password. We have reseted the password for your account<br>Here is your new pasword - <b>'.$new_password.'</b><br><br>We wish you a plesent day,<br>Team WhiteFox';
        $mail->AltBody = 'Hello!<br>You have reseted your password. This is your new password - '.$new_password;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
//working_page---------------------------------
class card{
    protected $name;
    protected $info;
    protected $card_id;
    protected $user_id;

    public function __construct($name, $info, $card_id){
        $this->name = $name;
        $this->info = $info;
        $this->card_id = $card_id;
        $this->user_id = $_SESSION['user_id'];
    }
    public function do_card(){
        global $conn;

        $sql = "SELECT `color` FROM `cards` WHERE user_id = ".$_SESSION["user_id"]." and card_id = ".$this->card_id;
        $result = $conn->query($sql);
        $row_color = $result->fetch_assoc();
        echo "<div class='main_panel_card'>
        <form action='local_brain.php' method='post' id='card_form".$this->card_id."'>
                    <input type='hidden' name='card_id' value='".$this->card_id."'>
                    <input type='hidden' name='user_id' value='".$this->user_id."'>
                </form>
                <a href='#' onclick='document.getElementById(\"card_form".$this->card_id."\").submit();'>";
        if ($row_color["color"] === '1'){
            echo "<div class='main_panel_card_color' style='background-color: #EA4335'></div>";
        }
        elseif ($row_color["color"] === '2'){
            echo "<div class='main_panel_card_color' style='background-color: #FBBC05'></div>";
        }
        elseif ($row_color["color"] === '3'){
            echo "<div class='main_panel_card_color' style='background-color: #34A853'></div>";
        }
        elseif ($row_color["color"] === '4'){
            echo "<div class='main_panel_card_color' style='background-color: #4285F4'></div>";
        }
        elseif ($row_color["color"] === '5'){
            echo "<div class='main_panel_card_color' style='background-color: #5D2E8C'></div>";
        }
        elseif ($row_color["color"] === '6'){
            echo "<div class='main_panel_card_color' style='background-color: #513C2C'></div>";
        }
        elseif ($row_color["color"] === '7'){
            echo "<div class='main_panel_card_color' style='background-color: #100B00'></div>";
        }
        //<img src='oYiTqum.jpg' alt='' class='main_panel_card_img'>
        echo "<div class='main_panel_card_bottom'>";
        if (strlen($this->name) <= 9){
            echo "<div class='main_panel_card_bottom_h1'>
                            <h1>".$this->name."</h1>
                        </div>";
        } else{
            echo "<div class='main_panel_card_bottom_h1_2'>
                            <h1>".$this->name."</h1>
                        </div>";
        }
                        echo "<div class='main_panel_card_bottom_p'>
                            <p>".$this->info."</p>
                        </div>
                    </div>
                </a>
                <div class='main_panel_card_bottom_options'>
                            <form method='post' action='local_brain.php' onsubmit='return confirmDeleteCard()'>
                                <input type='text' name='card_delete' style='display: none' value='".$this->card_id."'>
                                <button type='submit'>Delete <svg xmlns='http://www.w3.org/2000/svg' style='margin-bottom: 10%' width='16' height='16' fill='currentColor' class='bi bi-trash3-fill' viewBox='0 0 16 16'>
                                <path d='M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z'/>
                                </svg></button>
                            </form>";
                        //echo "<form method='post' action='local_brain.php' onsubmit='return confirmSubmit()'>
                                //<input type='text' name='card_rename' style='display: none' value='".$this->card_id."'>
                                //<button type='submit'>Rename <svg xmlns='http://www.w3.org/2000/svg' style='margin-bottom: 10%' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
                                //<path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/>
                                //</svg></button>
                                //</form>
                        echo "</div>
        </div>";
    }
}
class graph{
    protected $xValues;
    protected $yValues;
    protected $yValues_money;

    public function __construct($xValues, $yValues){
        $this->xValues = $xValues;
        $this->yValues = $yValues;
        $this->yValues_money = $_SESSION["show_money"];
    }

    public function graph(){
        echo "<script type='text/javascript'>
var xValues = [".$this->xValues."];
var yValues = [".$this->yValues.",".$this->yValues_money."];
console.log(xValues);
console.log(yValues);
new Chart('myChart', {
    type: 'line',
    data: {
        labels: xValues,
        datasets: [{
            fill: false,
            lineTension: 0,
            backgroundColor: 'rgba(0,0,255,1.0)',
            borderColor: 'rgba(0,0,255,0.1)',
            data: yValues
        }]
    },
    options: {
        legend: {display: false},
        scales: {
            yAxes: [{ticks: {min: 6, max:16}}],
        }
    }
});</script>";
    }
}
//normal_user----------------------------------------------------
function main_page(){
    global $conn;

    //if the user is banned
    $sql_ban = "SELECT * FROM `user` WHERE ban_status = 1 and user_key = '".$_SESSION["user_key"]."';";
    $result = $conn->query($sql_ban);
    if ($result->num_rows > 0){
        main_page_banned();
    } else {
        //if user is not banned
        if ($_SESSION["top_bar_check"] === 0) {
            echo "<div id='welcome_top' class='welcome_top'>
<h1>Welcome " . $_SESSION['u_nickname'] . "</h1>
</div>";
        }
        readfile("main_panel.html");
        echo '<!--Navbar-->
    <div class="navbar_own">
        <div id="myDropdown" class="navbar_own_account_button_dropdown_content">
            <div>
                <a href="?edit_local_user">Edit profile <svg style="margin-left: 1vh; margin-bottom: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Zm9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382l.045-.148ZM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                </svg></a>
            </div>
            <div>
                <a href="?log_out">Log out <svg style="margin-left: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg></a>
            </div>
        </div>
        <div class="navbar_own_color"></div>
        <div class="navbar_own_img_control">
            <img class="navbar_own_img" src="Untitled-4.png" alt="whitefox" onclick="window.location.href=/"local_brain.php?action=main_page/"">
        </div>
        <div class="navbar_own_account">
            <div class="navbar_own_account_button_control">
                <button onclick="dropdownFunction()">'.$_SESSION["u_nickname"].' <svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block" id="drop_down_arrow1" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg><svg xmlns="http://www.w3.org/2000/svg" style="transition: 0.2s; display: none" id="drop_down_arrow2" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg></button>
            </div>
        </div>
    </div>';
        $sql = "SELECT * FROM `cards` WHERE user_id = '" . $_SESSION["user_id"] . "' ORDER BY `card_id` ASC";
        $result = $conn->query($sql);
        echo "
<div class='main_panel_row'>
        <div class='main_panel_addcard'>
            <a href='#' class='main_panel_addcard_href' id='add_card'>
                <h1>+<p>Add card</p></h1>
            </a>
        </div>";
        while ($row = $result->fetch_assoc()) {
            $show_card = new card($row["name"], $row["info"], $row['card_id']);
            $show_card->do_card();
        }
        echo "</div>";
        echo "    <!--Footer-->
    <div class='index_footer'>
        <div class='index_footer_logo_align'>
            <img class='index_footer_logo' src='Untitled-4.png' alt='whitefox'>
        </div>
        <div class='index_footer_text'>
            <p>© 2023 Whitefox</p>
            <p><br>If you would like to find out more about WhiteFox, follow <a href='https://www.instagram.com/tarelunga_daniel/?hl=en' style='color: white' target='_blank'>@tarelunga_daniel</a> on instagram. If you have any other questions, please reach out to us via our mobile number: +420 775 696 129. WhiteFox is a website established in the Czech Republic, registered address: Višňová 4, Moravany U Brna, 664 48, Czech Republic, number of registration: none. WhiteFox is a project by Daniel Tarelunga and regulated by EducaNET Brno High-school. WhiteFox provides services that include: statistics, review, control and management of your money</p>
            <p><br>Insurance distribution service is not provided by anyone, any data breach or data leaking that might occur, WhiteFox is not responsible. Please follow our recommendations for using our website. That includes: do not have the same password on another platform, do not share your Client number with anyone, do not visit WhiteFox website on public networks, try using VPN if possible.</p>
            <p><br>WhiteFox is authorised by High-school EducaNET Brno under the Laws of the Czech Republic. EducaNet High-school address: Jánská 22, 602 00 Brno-střed, Czech Republic. School related-products the same as WhiteFox customers are provided by EducaNet Brno High-school which is authorised by the Ministry of Education to control the progress of the project and by WhiteFox.</p>
        </div>
    </div>";
    }
    $_SESSION["top_bar_check"] = 1;
}
//mainpage_admin------------------------------------------------
function main_page_admin(){
    global $conn;

    //if the user is not admin
    $sql_admin_check = "SELECT * FROM `user` WHERE admin > 0 and user_key = '".$_SESSION["user_key"]."';";
    $result = $conn->query($sql_admin_check);
    if ($result->num_rows < 1){
        main_page();
    } else {
        if($_SESSION["top_bar_check"] === 0) {
            echo "<div id='welcome_top' class='welcome_top'>
<h1>Welcome " . $_SESSION['u_nickname'] . "</h1>
</div>";
        }
        readfile("main_panel_admin.html");
        echo '<!--Navbar-->
    <div class="navbar_own">
        <div id="myDropdown" class="navbar_own_account_button_dropdown_content">
            <div>
                <a href="?edit_local_user">Edit profile <svg style="margin-left: 1vh; margin-bottom: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Zm9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382l.045-.148ZM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                </svg></a>
            </div>
            <div>
                <a href="?log_out">Log out <svg style="margin-left: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg></a>
            </div>
        </div>
        <div class="navbar_own_color"></div>
        <div class="navbar_own_img_control">
            <img class="navbar_own_img" src="Untitled-4.png" alt="whitefox" onclick="window.location.href=/"local_brain.php?action=main_page/"">
        </div>
        <div class="navbar_own_account">
            <div class="navbar_own_account_button_control">
                <button onclick="dropdownFunction()">'.$_SESSION["u_nickname"].' <svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block" id="drop_down_arrow1" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg><svg xmlns="http://www.w3.org/2000/svg" style="transition: 0.2s; display: none" id="drop_down_arrow2" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg></button>
            </div>
        </div>
    </div>';
        $sql = "SELECT * FROM `user` ORDER BY `user_id` ASC ;";
        $result = $conn->query($sql);
        echo "<div class='main_panel_admin_all_control'>
              <div class='main_panel_admin_table_control'>";
        echo "<table>
<tr>
    <th>User key</th>
    <th>Username</th>
    <th>Nickname</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Admin</th>
    <th>Ban status</th>
    <th></th>
    <th></th>
    <th></th>
</tr>";
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["user_key"] . "</td>
              <td>" . $row["user_name"] . " " . $row["user_surname"] . "</td>
              <td>" . $row["nickname"] . "</td>
              <td>" . $row["email"] . "</td>
              <td>" . $row["phone"] . "</td>";
            if ($row["admin"] > 0) {
                echo "<td>YES</td>";
            } else {
                echo "<td>NO</td>";
            }
            if ($row["ban_status"] > 0) {
                echo "<td style='color: red'>YES</td>";
            } else {
                echo "<td style='color: green'>NO</td>";
            }
            //actions-------------------------------------------

            echo "<td><a href='?edit_user=".$row['user_id']."'>EDIT</a></td>";
            if ($row["ban_status"] < 1) {
                echo "<td><a href='?ban_user=" . $row['user_id'] . "'>BAN</a></td>";
            } else {
                echo "<td><a href='?unban_user=" . $row['user_id'] . "'>UNBAN</a></td>";
            }
              echo "<td><a href='?delete=" . $row['user_id'] . "'>DELETE</a></td>";
            echo "</tr>";
            $i++;
        }
        echo "</table>";
        echo "</div>";
        echo "</div>";
        echo "    <!--Footer-->
    <div class='index_footer' style='margin-top: 10%'>
        <div class='index_footer_logo_align'>
            <img class='index_footer_logo' src='Untitled-4.png' alt='whitefox'>
        </div>
        <div class='index_footer_text'>
            <p>© 2023 Whitefox</p>
            <p><br>If you would like to find out more about WhiteFox, follow <a href='https://www.instagram.com/tarelunga_daniel/?hl=en' style='color: white' target='_blank'>@tarelunga_daniel</a> on instagram. If you have any other questions, please reach out to us via our mobile number: +420 775 696 129. WhiteFox is a website established in the Czech Republic, registered address: Višňová 4, Moravany U Brna, 664 48, Czech Republic, number of registration: none. WhiteFox is a project by Daniel Tarelunga and regulated by EducaNET Brno High-school. WhiteFox provides services that include: statistics, review, control and management of your money</p>
            <p><br>Insurance distribution service is not provided by anyone, any data breach or data leaking that might occur, WhiteFox is not responsible. Please follow our recommendations for using our website. That includes: do not have the same password on another platform, do not share your Client number with anyone, do not visit WhiteFox website on public networks, try using VPN if possible.</p>
            <p><br>WhiteFox is authorised by High-school EducaNET Brno under the Laws of the Czech Republic. EducaNet High-school address: Jánská 22, 602 00 Brno-střed, Czech Republic. School related-products the same as WhiteFox customers are provided by EducaNet Brno High-school which is authorised by the Ministry of Education to control the progress of the project and by WhiteFox.</p>
        </div>
    </div>";
    }
    $_SESSION["top_bar_check"] = 1;
}
//user_banned-----------------------------------------------------------------------
function main_page_banned(){
    global $conn;
    echo "<div id='welcome_top' class='welcome_top'>
<h1>Welcome ".$_SESSION['u_nickname']."</h1>
</div>";
    readfile("main_panel.html");
    echo '<!--Navbar-->
    <div class="navbar_own">
        <div id="myDropdown" class="navbar_own_account_button_dropdown_content">
            <div>
                <a style="color: #cc0000; opacity: 50%">Edit profile <svg style="margin-left: 1vh; margin-bottom: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Zm9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382l.045-.148ZM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                </svg></a>
            </div>
            <div>
                <a href="?log_out">Log out <svg style="margin-left: 1vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg></a>
            </div>
        </div>
        <div class="navbar_own_color"></div>
        <div class="navbar_own_img_control">
            <img class="navbar_own_img" src="Untitled-4.png" alt="whitefox" onclick="window.location.href=/"local_brain.php?action=main_page/"">
        </div>
        <div class="navbar_own_account">
            <div class="navbar_own_account_button_control">
                <button onclick="dropdownFunction()">'.$_SESSION["u_nickname"].' <svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block" id="drop_down_arrow1" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg><svg xmlns="http://www.w3.org/2000/svg" style="transition: 0.2s; display: none" id="drop_down_arrow2" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg></button>
            </div>
        </div>
    </div>';
    echo "<div class='main_panel_banned_text'>
          <svg style='position:relative;' xmlns='http://www.w3.org/2000/svg' width='10vh' height='10vh' fill='currentColor' class='bi bi-exclamation-circle' viewBox='0 0 16 16'>
              <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
              <path d='M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z'/>
          </svg>
              <h1><b>BANNED</b></h1>
              <p>You are banned. If you think that we made a mistake, please contact us at whitefox.banking@gmail.com</p>
          </div>";
    echo "    <!--Footer-->
    <div class='index_footer'>
        <div class='index_footer_logo_align'>
            <img class='index_footer_logo' src='Untitled-4.png' alt='whitefox'>
        </div>
        <div class='index_footer_text'>
            <p>© 2023 Whitefox</p>
            <p><br>If you would like to find out more about WhiteFox, follow <a href='https://www.instagram.com/tarelunga_daniel/?hl=en' style='color: white' target='_blank'>@tarelunga_daniel</a> on instagram. If you have any other questions, please reach out to us via our mobile number: +420 775 696 129. WhiteFox is a website established in the Czech Republic, registered address: Višňová 4, Moravany U Brna, 664 48, Czech Republic, number of registration: none. WhiteFox is a project by Daniel Tarelunga and regulated by EducaNET Brno High-school. WhiteFox provides services that include: statistics, review, control and management of your money</p>
            <p><br>Insurance distribution service is not provided by anyone, any data breach or data leaking that might occur, WhiteFox is not responsible. Please follow our recommendations for using our website. That includes: do not have the same password on another platform, do not share your Client number with anyone, do not visit WhiteFox website on public networks, try using VPN if possible.</p>
            <p><br>WhiteFox is authorised by High-school EducaNET Brno under the Laws of the Czech Republic. EducaNet High-school address: Jánská 22, 602 00 Brno-střed, Czech Republic. School related-products the same as WhiteFox customers are provided by EducaNet Brno High-school which is authorised by the Ministry of Education to control the progress of the project and by WhiteFox.</p>
        </div>
    </div>";
}

function failed_login(){
    echo "<p style='position: absolute; text-align: center ; width: 100%; top: 25%'>Unexistent client or incorrect password!</p>";
    readfile("login_page.html");
}
function add_card(){
    global $conn;
    $card_name = isset($_POST['card_name']) ? $_POST['card_name'] : "";
    $card_info = isset($_POST['card_info']) ? $_POST['card_info'] : "";
    $card_color = isset($_POST["color_pick"]) ? $_POST["color_pick"] : "";
    $card_currency = isset($_POST["currency_pick"]) ? $_POST["currency_pick"] : "";
    $sql = "INSERT INTO cards(user_id, name, info, color, currency) VALUES ('".$_SESSION['user_id']."','".$card_name."','".$card_info."',".$card_color.",'".$card_currency."');";
    $result = $conn->query($sql);

    echo '<div class="main_panel_user_edited_successfully"><p><svg style="margin-bottom: 0.5vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg> Card created successfully!</p></div>';
    main_page();
}
function card_delete(){
    global $conn;

    $card_delete = isset($_POST['card_delete']) ? $_POST['card_delete'] : "";

    $sql_delete_card = "DELETE FROM `cards` WHERE card_id = ".$card_delete." and user_id = ".$_SESSION["user_id"];
    $sql_delete_transactions = "DELETE FROM `transaction` WHERE card_id = ".$card_delete." and user_id = ".$_SESSION["user_id"];
    $sql_delete_money = "DELETE FROM `money` WHERE card_id = ".$card_delete." and user_id = ".$_SESSION["user_id"];

    $result_delete_card = $conn->query($sql_delete_card);
    $result_delete_transactions = $conn->query($sql_delete_transactions);
    $result_delete_money = $conn->query($sql_delete_money);

    echo '<div class="main_panel_user_edited_successfully"><p><svg style="margin-bottom: 0.5vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg> Card deleted successfully!</p></div>';
    main_page();
}
function show_stats($card_id){
    $_SESSION["show_money"] = 0;
    $_SESSION["card_id"] = $card_id;
    global $conn;

    readfile("account.html");
    $sql = "SELECT * FROM `money` WHERE card_id = ".$card_id." and user_id = ".$_SESSION['user_id'].";";
    $result = $conn->query($sql);

    $sql_currency = "SELECT * FROM `cards` WHERE card_id = ".$card_id." and user_id = ".$_SESSION['user_id'];
    $result_currency = $conn->query($sql_currency);
    $row_currency = $result_currency->fetch_assoc();

    $sql2 = "SELECT * FROM `cards` WHERE card_id = '".$card_id."'";
    $result2 = $conn->query($sql2);
    $row_name = $result2->fetch_assoc();

    $show_money = 0;
    echo '<div class="account_all_control">
        <h1 class="account_card_name"><b>'.$row_name["name"].'</b></h1>
        <div class="account_maincard">
            <div class="account_maincard_top">
                <div class="account_maincard_top_money">';
    while($row = $result->fetch_assoc()){
        $show_money = explode(".",$row["money"]);
        echo '<h1>'.$row_currency["currency"].' '.$show_money[0].'</h1><p>.'.$show_money[1].'</p>';
        $_SESSION["show_money"] = $row["money"];
    }
    if ($show_money[0] === NULL){
        echo '<h1>'.$row_currency["currency"].' 0</h1><p>.00</p>';
    }
    echo '</div>
                <div class="account_maincard_top_buttons">
                    <button id="account_button_add_money" onclick="add_money_show()">+ Add Money</button>
                    <button id="account_button_send_money" onclick="">- Send Money</button>
                    <form action="local_brain.php" method="post">
                        <input type="text" step="any" id="account_transaction_name" name="account_transaction_name" placeholder="Transaction description">
                        <input type="number" style="width: 12vh" step="any" id="account_add_money" name="account_add_money" placeholder="SUM"><button class="account_button_add_money_php" id="account_button_add_money1">+ Add Money</button>
                        <input type="number" style="width: 12vh" step="any" id="account_send_money" name="account_send_money" placeholder="SUM"><button class="account_button_send_money_php" id="account_button_send_money1">- Send Money</button>
                        <a href="#" id="close_transaction" onclick="transaction_hide()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                </svg></a>
                    </form>
                </div>
            </div>
            <div class="account_maincard_mid">
                <div class="account_maincard_mid_graph_control1">
                    <div class="account_maincard_mid_graph_control2">
                        <canvas id="myChart" class="account_graph"></canvas>
                    </div>
                </div>
                <div class="account_maincrad_mid_tittle">
                    <p>Transactions</p>
                    <a href="?account_see_all">See all</a>
                </div>';
    $sql = 'SELECT * FROM `transaction` WHERE card_id = '.$_SESSION["card_id"].' and user_id = '.$_SESSION["user_id"].' ORDER BY `transaction_id` DESC;';
    $result = $conn->query($sql);
    echo '<div class="account_maincard_mid_transactions">
                    <div class="account_maincard_mid_transactions_card">';
    $i = 4;
    while ($row = $result->fetch_assoc() and $i > 0) {
        echo '<div class="account_maincard_mid_transactions_card_bubble">
                  <a href="#"><p style="width: 58%; margin: 0;">' . $row["description"] . '</p><div class="account_maincard_mid_transactions_card_bubble_control"><p class="account_maincard_mid_transactions_card_bubble_date_dissapear">' . $row["date"] . ' </p><p class="account_maincard_mid_transactions_card_bubble_p1">' . $row["suma"] . '</p><p class="account_maincard_mid_transactions_card_bubble_p2">'.$row_currency["currency"].'</p></div></a>
              </div>';
        $i--;
    }
    if ($result->num_rows === 0){
        echo '<div class="account_maincard_mid_transactions_card_bubble_empty">
                      <p>There are no transaction\'s yet</p>
                  </div>';
    }
                    echo '</div>
                </div>
            </div>
        </div>
    </div>';
    info_graph($_SESSION["show_money"]);
}
function money_transaction_plus($card_id){
    global $conn;

    $money = isset($_POST["account_add_money"]) ? $_POST["account_add_money"] : "";
    $money_test = isset($_POST["account_send_money"]) ? $_POST["account_send_money"] : "";
    $money_description = isset($_POST["account_transaction_name"])? $_POST["account_transaction_name"] : "";
    $calculate = $_SESSION["show_money"] + $money;
    $sql = "SELECT * FROM `money` WHERE card_id = ".$card_id.";";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()){
        $sql2 = "UPDATE `money` SET `money`= ".$calculate.",`date`=curdate() WHERE card_id = ".$card_id." and user_id = ".$_SESSION["user_id"].";";
        $sql_web_hosting = "INSERT INTO `transaction`(`card_id`, `user_id`, `suma`, `date`, `money`, `description`) VALUES (".$card_id.",".$_SESSION["user_id"].",".$money.",curdate(),".$_SESSION["show_money"].",'".$money_description."')";
        $result_web_hosting = $conn->query($sql_web_hosting);
        //echo "<br>";
        $result2 = $conn->query($sql2);
        $sql_add_transaction_description = "UPDATE `transaction` SET `description`= '".$money_description."' WHERE user_id = ".$_SESSION["user_id"]." ORDER BY transaction_id DESC LIMIT 1;";
        $result3 = $conn->query($sql_add_transaction_description);
        show_stats($card_id);
    } else{
        $sql3 = "INSERT INTO `money`(`card_id`, `user_id`, `money`, `date`) VALUES (".$card_id.",".$_SESSION["user_id"].",0,curdate());";
        $result3 = $conn->query($sql3);
        //$sql_web_hosting = "INSERT INTO `transaction`(`card_id`, `user_id`, `suma`, `date`, `money`, `description`) VALUES (".$card_id.",".$_SESSION["user_id"].",".$money.",curdate(),".$calculate.",'".$money_description."')";
        //$result_web_hosting = $conn->query($sql_web_hosting);
        money_transaction_plus($card_id);
    }
}
function money_transaction_minus($card_id){
    global $conn;
    $money = isset($_POST["account_send_money"]) ? $_POST["account_send_money"] : "";
    $money_description = isset($_POST["account_transaction_name"])? $_POST["account_transaction_name"] : "";

    $calculate = $_SESSION["show_money"] - $money;
    $sql = "SELECT * FROM `money` WHERE card_id = ".$card_id.";";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()){
        $sql2 = "UPDATE `money` SET `money`= ".$calculate.",`date`=curdate() WHERE card_id = ".$card_id." and user_id = ".$_SESSION["user_id"].";";
        $result2 = $conn->query($sql2);
        $sql_web_hosting = "INSERT INTO `transaction` (`card_id`, `user_id`, `suma`, `date`, `money`, `description`) VALUES (".$card_id.",".$_SESSION["user_id"].",".$calculate - $_SESSION["show_money"].",CURDATE(),".$_SESSION["show_money"].",'".$money_description."')";
        $result_web_hosting = $conn->query($sql_web_hosting);
        $sql_add_transaction_description = "UPDATE `transaction` SET `description`= '".$money_description."' WHERE user_id = ".$_SESSION["user_id"]." ORDER BY transaction_id DESC LIMIT 1;";
        $result3 = $conn->query($sql_add_transaction_description);
        show_stats($card_id);
    } else{
        $sql3 = "INSERT INTO `money`(`card_id`, `user_id`, `money`, `date`) VALUES (".$card_id.",".$_SESSION["user_id"].",0,curdate());";
        $result3 = $conn->query($sql3);
        money_transaction_plus($card_id);
    }
}
function info_graph($money){
    global $conn;

    $sql = "SELECT * FROM `transaction` WHERE user_id = ".$_SESSION["user_id"]." and card_id = ".$_SESSION["card_id"]." ORDER BY `transaction_id` DESC;";
    $result = $conn->query($sql);
    $i = 5;
    $j = 0;
    $graph2 = NULL;
    $graphX = NULL;
    $graphY = NULL;
    while ($row = $result->fetch_assoc() and $i > 0) {
        $graph2[$j] = $row["money"];
        $j++;
        $i--;
    }
    if ($graph2 != NULL) {
        $reverse_graphY = array_reverse($graph2);
        $graph1 = [1, 2, 3, 4, 5, 6];
        $graphX = implode(",", $graph1);
        $graphY = implode(",", $reverse_graphY);
    }
    $graph_construct = new graph($graphX, $graphY);
    $graph_maker = $graph_construct->graph();
}
function account_see_all(){
    global $conn;

    readfile("account_see_all.html");
    $sql = "SELECT * FROM `transaction` WHERE user_id = ".$_SESSION["user_id"]." and card_id = ".$_SESSION["card_id"]." ORDER BY `transaction_id` DESC ";
    $sql_card = "SELECT * FROM `cards` WHERE card_id = ".$_SESSION["card_id"];
    $result = $conn->query($sql);
    $result_card = $conn->query($sql_card);
    $row_card = $result_card->fetch_assoc();
    echo '<div class="account_see_all_allcontrol">
    <div class="account_see_all_card">
        <div class="account_see_all_card_top">
            <form method="post" action="local_brain.php">
                <input style="display: none" name="card_id" type="text" value="'.$_SESSION["card_id"].'">
                <button class="account_see_all_card_top_go_back_button">
                    <svg xmlns="http://www.w3.org/2000/svg" style="position: relative; left: 10%;" width="50" height="30" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                </button>
            </form>
            <h1 class="account_see_all_card_h1">'.$row_card["name"].'</h1>
        </div>';
    echo '<div class="account_see_all_card_mid">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="account_maincard_mid_transactions_card_bubble">
                <a href="#"><p style="width: 58%; margin: 0;">' . $row["description"] . '</p><div class="account_maincard_mid_transactions_card_bubble_control"><p class="account_maincard_mid_transactions_card_bubble_date">' . $row["date"] . ' </p><p class="account_maincard_mid_transactions_card_bubble_p1">' . $row["suma"] . '</p><p class="account_maincard_mid_transactions_card_bubble_p2">'.$row_card['currency'].'</p></div></a>
            </div>';
    }
    if ($result->num_rows === 0){
        echo '<div class="account_maincard_mid_transactions_card_bubble_empty">
                      <p>There are no transaction\'s yet</p>
                  </div>';
    }
    echo '</div>
    </div>
</div>';
}
function main_page_admin_delete(){
    global $conn;

    if ($_SESSION['u_admin'] > 0) {
        //delete user
        $sql1 = "DELETE FROM `user` WHERE user_id = " . $_GET['delete'] . ";";
        $result1 = $conn->query($sql1);
        //delete transactions
        $sql2 = "DELETE FROM `transaction` WHERE user_id = " . $_GET['delete'] . ";";
        $result2 = $conn->query($sql2);
        //delete cards
        $sql3 = "DELETE FROM `cards` WHERE user_id = " . $_GET['delete'] . ";";
        $result3 = $conn->query($sql3);
        //delete money
        $sql4 = "DELETE FROM `money` WHERE user_id = " . $_GET['delete'] . ";";
        $result4 = $conn->query($sql4);
        main_page_admin();
    } else {
        echo "<h1>You don't have the permission to do that!</h1>
              <a href='local_brain.php?action=main_page'>Back to main page</a>";
    }
}
function main_page_admin_ban(){
    global $conn;

    if ($_SESSION['u_admin'] > 0) {
        //ban user
        $sql1 = "UPDATE `user` SET `ban_status`= 1 WHERE user_id = " . $_GET['ban_user'] . ";";
        $result1 = $conn->query($sql1);
        main_page_admin();
    } else {
        echo "<h1>You don't have the permission to do that!</h1>
              <a href='local_brain.php?action=main_page'>Back to main page</a>";
    }
}
function main_page_admin_unban(){
    global $conn;

    if ($_SESSION['u_admin'] > 0) {
        //ban user
        $sql1 = "UPDATE `user` SET `ban_status`= 0 WHERE user_id = " . $_GET['unban_user'] . ";";
        $result1 = $conn->query($sql1);
        main_page_admin();
    } else {
        echo "<h1>You don't have the permission to do that!</h1>
              <a href='local_brain.php?action=main_page'>Back to main page</a>";
    }
}
function main_page_admin_edit_user(){
    global $conn;

    if($_SESSION['u_admin'] > 0){
        $sql = "SELECT * FROM `user` WHERE user_id = ".$_GET['edit_user'].";";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        readfile("edit_user.html");
        echo '<div class="register_all_control">
    <div class="register_card">
        <img class="login_card_icon" src="Untitled-3-icon.svg">
        <h1 class="h1-main">Edit user</h1>
        <form method="post" action="local_brain.php">
            <input style="display: none" type="text" id="register_name_form" name="edit_user_id" value="'.$_GET['edit_user'].'">
            <input type="text" id="register_name_form" name="edit_name_form" placeholder="Surname" value="'.$row["user_name"].'">
            <input type="text" id="register_surname_form" name="edit_surname_form" placeholder="Surname" value="'.$row["user_surname"].'">
            <input type="text" id="register_nickname_form" name="edit_nickname_form" placeholder="Nickname" value="'.$row["nickname"].'">
            <input type="email" id="register_email_form" name="edit_email_form" placeholder="Email" value="'.$row["email"].'">
            <input type="tel" id="register_phone_form" name="edit_phone_form" placeholder="Phone number" value="'.$row["phone"].'">
            <input type="text" id="register_password1_form" name="edit_password1_form" placeholder="Password" value="'.$row["password"].'">
            <button type="submit">Edit user</button>
        </form>
    </div>
</div>
<p id="register_name_form_warning" class="register_warning">*fill all the fields</p>';
    } else {
        echo "<h1>You don't have the permission to do that!</h1>
              <a href='local_brain.php?action=main_page'>Back to main page</a>";
    }
}
function main_page_admin_edit_user_form(){
    global $conn;
    $edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : "";
    $edit_name = isset($_POST['edit_name_form']) ? $_POST['edit_name_form'] : "";
    $edit_surname = isset($_POST['edit_surname_form']) ? $_POST['edit_surname_form'] : "";
    $edit_nickname = isset($_POST['edit_nickname_form']) ? $_POST['edit_nickname_form'] : "";
    $edit_email = isset($_POST['edit_email_form']) ? $_POST['edit_email_form'] : "";
    $edit_phone = isset($_POST['edit_phone_form']) ? $_POST['edit_phone_form'] : "";
    $edit_password = isset($_POST['edit_password1_form']) ? $_POST['edit_password1_form'] : "";

    //registration algorithm
    $sql_verify_nickname = "SELECT * FROM user WHERE nickname = '".$edit_nickname."';";
    $sql_verify_email = "SELECT * FROM user WHERE email = '".$edit_email."';";
    $result_nickname = $conn->query($sql_verify_nickname);
    $result_email = $conn->query($sql_verify_email);

    $row_nickname = $result_nickname->fetch_assoc();
    $row_email = $result_email->fetch_assoc();

    if ($edit_password != FALSE){
        $sql_register = "UPDATE `user` SET `user_name`= '".$edit_name."',`user_surname`= '".$edit_surname."',`nickname`= '".$edit_nickname."',`email`= '".$edit_email."',`phone`= ".$edit_phone.",`password`= '".$edit_password."' WHERE user_id = ".$edit_user_id.";";
        $result = $conn->query($sql_register);
        if ($result == False) {
            echo "Error " . $sql_register . "<br>" . $conn -> error;
        } else {
            main_page_admin();
        }
    }
    elseif ($result_email->num_rows > 0){
        $_GET['edit_user'] = $edit_user_id;
        main_page_admin_edit_user();
        echo "<p style='position: absolute; top: 53.5%; right: 30.5%; font-size: 1.8vh;'>already in-use</p>";
        echo '<svg style="position:absolute; top:54%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
    }
    elseif ($result_nickname->num_rows > 0) {
        $_GET['edit_user'] = $edit_user_id;
        main_page_admin_edit_user();
        echo "<p style='position: absolute; top: 48%; right: 30.5%; font-size: 1.8vh;'> already taken</p>";
        echo '<svg style="position:absolute; top:48.5%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
    }
}
function edit_local_user($user_id){
    global $conn;

    if($_SESSION['user_id'] === $user_id){
        //$sql = "SELECT * FROM `user` WHERE user_id = ".$_GET['edit_user'].";";
        //$result = $conn->query($sql);
        //$row = $result->fetch_assoc();
        readfile("edit_user.html");
        echo '<div class="register_all_control">
    <div class="register_card">
        <h1 class="h1-main">Edit user</h1>
        <form method="post" action="local_brain.php">
            <input style="display: none" type="text" id="edit_user_id_form" name="user_user_id" value="'.$_SESSION["user_id"].'">
            <input type="text" id="edit_user_name_form" name="user_name_form" placeholder="Name" value="'.$_SESSION["u_name"].'">
            <input type="text" id="edit_user_surname_form" name="user_surname_form" placeholder="Surname" value="'.$_SESSION["u_surname"].'">
            <input type="text" id="edit_user_nickname_form" name="user_nickname_form" placeholder="Nickname" value="'.$_SESSION["u_nickname"].'">
            <input type="email" id="edit_user_email_form" name="user_email_form" placeholder="Email" value="'.$_SESSION["u_email"].'">
            <input type="tel" id="edit_user_phone_form" name="user_phone_form" placeholder="Phone number" value="'.$_SESSION["u_phone"].'">
            <input type="password" id="edit_user_password1_form" name="user_password1_form" placeholder="OLD Password">
            <input type="password" id="edit_user_password2_form" name="user_password2_form" placeholder="NEW Password">
            <button type="submit">Edit user</button>
        </form>
    </div>
</div>
<p id="register_name_form_warning" class="register_warning">*fill all the fields</p>';
    } else {
        echo "<h1>You don't have the permission to do that!</h1>
              <a href='local_brain.php?action=main_page'>Back to main page</a>";
    }
}
function edit_local_user_form_success(){
    global $conn;
    $edit_user_id = isset($_POST['user_user_id']) ? $_POST['user_user_id'] : "";
    $edit_name = isset($_POST['user_name_form']) ? $_POST['user_name_form'] : "";
    $edit_surname = isset($_POST['user_surname_form']) ? $_POST['user_surname_form'] : "";
    $edit_nickname = isset($_POST['user_nickname_form']) ? $_POST['user_nickname_form'] : "";
    $edit_email = isset($_POST['user_email_form']) ? $_POST['user_email_form'] : "";
    $edit_phone = isset($_POST['user_phone_form']) ? $_POST['user_phone_form'] : "";
    $edit_password1 = isset($_POST['user_password1_form']) ? $_POST['user_password1_form'] : "";
    $edit_password2 = isset($_POST['user_password2_form']) ? $_POST['user_password2_form'] : "";

    //registration algorithm
    $sql_verify_nickname = "SELECT * FROM user WHERE nickname = '".$edit_nickname."';";
    $sql_verify_email = "SELECT * FROM user WHERE email = '".$edit_email."';";
    $sql_verify_password = "SELECT * FROM user WHERE user_id = '".$_SESSION["user_id"]."';";
    $result_password = $conn->query($sql_verify_password);
    $result_nickname = $conn->query($sql_verify_nickname);
    $result_email = $conn->query($sql_verify_email);

    $row_nickname = $result_nickname->fetch_assoc();
    $row_email = $result_email->fetch_assoc();
    $row_password = $result_password->fetch_assoc();

    if (($edit_password1 === '' and $edit_password2 === '' or ($edit_password1 === $row_password["password"])) or (($edit_password1 != '' or $edit_password2 != '') and ($edit_password1 === $row_password["password"]))){
        if ($row_email["email"] === $_SESSION["u_email"] and $row_nickname["nickname"] === $_SESSION["u_nickname"]) {
            if (($edit_password1 != '' or $edit_password2 != '') and ($edit_password1 === $row_password["password"])){
                $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . ", `password` = '".$edit_password2."' WHERE user_id = " . $edit_user_id . ";";
                $result = $conn->query($sql_register);
            }
            $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . " WHERE user_id = " . $edit_user_id . ";";
            $result = $conn->query($sql_register);
            $_SESSION["u_name"] = $edit_name;
            $_SESSION["u_surname"] = $edit_surname;
            $_SESSION["u_nickname"] = $edit_nickname;
            $_SESSION["u_email"] = $edit_email;
            $_SESSION["u_phone"] = $edit_phone;
            if ($result == False) {
                echo "Error " . $sql_register . "<br>" . $conn->error;
            } else {
                echo '<div class="main_panel_user_edited_successfully"><p><svg style="margin-bottom: 0.5vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg> Profile updated successfully!</p></div>';
                main_page();
            }
        }
        if (($row_email["email"] != $_SESSION["u_email"] and $result_email->num_rows < 1) or ($row_nickname["nickname"] != $_SESSION["u_nickname"] and $result_nickname->num_rows < 1)) {
            if ($row_nickname["nickname"] != $_SESSION["u_nickname"] and $result_nickname->num_rows < 1 and ($row_email["email"] === $_SESSION["u_email"] or $result_email->num_rows < 1)) {
                if (($edit_password1 != '' or $edit_password2 != '') and ($edit_password1 === $row_password["password"])){
                    $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . ", `password` = '".$edit_password2."' WHERE user_id = " . $edit_user_id . ";";
                    $result = $conn->query($sql_register);
                }
                $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . " WHERE user_id = " . $edit_user_id . ";";
                $result = $conn->query($sql_register);
                $_SESSION["u_name"] = $edit_name;
                $_SESSION["u_surname"] = $edit_surname;
                $_SESSION["u_nickname"] = $edit_nickname;
                $_SESSION["u_email"] = $edit_email;
                $_SESSION["u_phone"] = $edit_phone;
                if ($result == False) {
                    echo "Error " . $sql_register . "<br>" . $conn->error;
                } else {
                    echo '<div class="main_panel_user_edited_successfully"><p><svg style="margin-bottom: 0.5vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg> Profile updated successfully!</p></div>';
                    main_page();
                }
            }
            elseif ($row_nickname["nickname"] === $_SESSION["u_nickname"]){
                if (($edit_password1 != '' or $edit_password2 != '') and ($edit_password1 === $row_password["password"])){
                    $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . ", `password` = '".$edit_password2."' WHERE user_id = " . $edit_user_id . ";";
                    $result = $conn->query($sql_register);
                }
                $sql_register = "UPDATE `user` SET `user_name`= '" . $edit_name . "',`user_surname`= '" . $edit_surname . "',`nickname`= '" . $edit_nickname . "',`email`= '" . $edit_email . "',`phone`= " . $edit_phone . " WHERE user_id = " . $edit_user_id . ";";
                $result = $conn->query($sql_register);
                $_SESSION["u_name"] = $edit_name;
                $_SESSION["u_surname"] = $edit_surname;
                $_SESSION["u_nickname"] = $edit_nickname;
                $_SESSION["u_email"] = $edit_email;
                $_SESSION["u_phone"] = $edit_phone;
                if ($result == False) {
                    echo "Error " . $sql_register . "<br>" . $conn->error;
                } else {
                    echo '<div class="main_panel_user_edited_successfully"><p><svg style="margin-bottom: 0.5vh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg> Profile updated successfully!</p></div>';
                    main_page();
                }
            }
        }
        if ($row_email["email"] != $_SESSION["u_email"] and $result_email->num_rows > 0){
            edit_local_user($_SESSION["user_id"]);
            echo "<p style='position: absolute; top: 54.5%; right: 31.2%; font-size: 1.8vh;'>already in-use</p>";
            echo '<svg style="position:absolute; top:55%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
        }
        elseif ($row_nickname["nickname"] != $_SESSION["u_nickname"] and $result_nickname->num_rows > 0) {
            edit_local_user($_SESSION["user_id"]);
            echo "<p style='position: absolute; top: 49%; right: 31.5%; font-size: 1.8vh;'> already taken</p>";
            echo '<svg style="position:absolute; top:49.5%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
</svg>';
        }
    }
    if ((($edit_password1 != '' and $edit_password2 != '') or ($edit_password2 != '' and $edit_password1 === '') or ($edit_password1 != '' and $edit_password2 === '')) and ($edit_password1 != $row_password["password"])){
        edit_local_user($_SESSION["user_id"]);
        echo "<p style='position: absolute; top: 66.5%; right: 30.3%; font-size: 1.8vh;'>Wrong password</p>";
        echo '<svg style="position:absolute; top:67%; right: 37%" xmlns="http://www.w3.org/2000/svg" width="2vh" height="2vh" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
      <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
    </svg>';
    }
}
?>