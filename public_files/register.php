<?php
include "../lib/common.php";
include "../lib/db.php";
include "../lib/functions.php";
include "../lib/User.php";

session_start();
header('Cache-control:private');

ob_start();
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
<table>
    <tr>
        <td>
            <label for="username">Username </label>
        </td>
        <td>
            <input type="text" name="username" id="username" value="
            <?php 
                if (isset($_POST['username'])){
                    echo htmlspecialchars($_POST['username']);
                }
            ?>">
        </td>
    </tr>
    <tr>
        <td>
            <label for="password1">Password </label>
        </td>
        <td>
            <input type="password" name="password1" id="password1">
        </td>
    </tr>
    <tr>
        <td>
            <label for="password2">Password again </label>
        </td>
        <td>
            <input type="password" name="password2" id="password2">
        </td>
    </tr>
    <tr>
        <td>]
            <label for="email">Email Address </label>
        </td>
        <td>
            <input type="text" name="email" id="email" value="
            <?php
                if (isset($_POST['email'])){
                    echo $_POST['email'];
                }
            ?>
            ">
        </td>
    </tr>
    <tr>
        <td>
            <label for="captcha">Verify </label>
        </td>
        <td>
            Enter text seen in this imagebr <br/>
        </td>
        <img src="img/captcha.php?nocache=<?php echo time();?>" alt="">
        <input type="text" name="captcha" id="captcha">
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="submit" value="Sign up">
        </td>
        <td>
            <input type="hidden" name="submitted" value="1">
        </td>
    </tr>
</table>
</form>
<?php
$form=ob_get_clean();

//show the form if this is the first time the page is viewed
if (!isset($_POST['submitted'])){
    $GLOBALS['TEMPLATE']['content']=$form;
}else{
    //otherwise process incoming data
    //验证密码
    $password1=(isset($_POST['password1'])) ? $_POST['password1'] : '';
    $password2=(isset($_POST['password2'])) ? $_POST['password2'] : '';
    $password=($password1&&$password1==$password2) ? sha1($password1) : '';
    
    //验证码
    $captcha=(isset($_POST['captcha'])&&strtoupper($_POST['captcha'])==$_SESSION['captcha']);
    
    //add the record if all input validate
    if (User::validateUsername($_POST['username'])&&$password&&User::validateEmailAddr($_POST['email'])&&$captcha){
        //make sure the user doesn't exist
        $user=User::getByUsername($_POST['username']);
        if ($user->userId){
            $GLOBALS['TEMPLATE']['content']='<p><strong>Sorry,this account already exists. Please try again.</strong></p>';
            $GLOBALS['TEMPLATE']['content'].=$form;
        }else{
            //create an inactive user record
            $user=new User();
            $user->username=$_POST['username'];
            $user->password=$password;
            $user->emailAddr=$_POST['email'];
            $token=$user->setInactive();
            
            $GLOBALS['TEMPLATE']['content']="<p><strong>Thank you!Be sure your account by visiting <a href='verify.php?uid={$user->userId}&token={$token}'>verify.php?uid={$user->userId}&token={$token}</a></strong></p>";
        }
    }else{
        //验证不正确
        $GLOBALS['TEMPLATE']['content']="<p><strong>Invalid data. Try again</strong></p>";
        $GLOBALS['TEMPLATE']['content'].=$form;
    }
}

//display the page
include "../templates/template_page.php";