<?php require_once("include/initialize.php"); ?>

<link href="<?php echo web_root ?>css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="<?php echo web_root ?>/js/bootstrap.min.js"></script>
<script src="<?php echo web_root ?>jquery/jquery.min.js"></script>

<link rel="stylesheet" href="<?php echo web_root ?>/font/css/font-awesome.min.css">
<style type="text/css">
.form-gap {
    padding-top: 70px;
}
</style>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("mailer/src/PHPMailer.php");
require("mailer/src/SMTP.php");
require("mailer/src/Exception.php");

if (isset($_POST['recover-submit'])) {
    $_SESSION['email'] = $_POST['email'];
    $conn = new mysqli('localhost', 'root', '', 'bookstore');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_SESSION['email'];
    $sql = "SELECT * FROM tblcustomer WHERE EMAILADD = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $code = mt_rand(100000, 999999);
        $_SESSION['recovery_code'] = $code;

        $update_sql = "UPDATE tblcustomer SET VERIFICATION = ? WHERE EMAILADD = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("is", $code, $email);
        $update_stmt->execute();

        $mail = new PHPMailer(true);
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'easypetcare.shop@gmail.com';
            $mail->Password   = 'aunu erdr bsgc mfzy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('easypetcare.shop@gmail.com', 'BCP Bookstore');
            $mail->addAddress($_SESSION['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Password Recovery';
            $mail->Body = 'Your recovery code is ' . $_SESSION['recovery_code'];

            $mail->send();
            echo 'Message has been sent';
            redirect('passwordrecover.php?code');
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        
    } else {
        $emailMessage = '<p>Your email address is incorrect.</p>';
    }

    $stmt->close();
    $conn->close();
}

if (isset($_POST['validatecode-submit'])) {
    if ($_SESSION['recovery_code'] == $_POST['resetcode']) {
        header('Location: passwordrecover.php?resetpassword');
    } else {
        $codemessage = '<p>Your code is incorrect.</p>';
    }
}

if (isset($_POST['savepass-submit'])) {
    $email = $_SESSION['email'];
    $new_password = sha1($_POST['newpassword']);
    
    $conn = new mysqli('localhost', 'root', '', 'bookstore');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE tblcustomer SET CUSPASS = ?, VERIFICATION = NULL WHERE EMAILADD = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();

    unset($_SESSION['email']);
    unset($_SESSION['recovery_code']);

    header('Location: passwordrecover.php?success');
}
?>

<div class="form-gap"></div>
<?php if (isset($_GET['code'])) { ?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="text-center">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Forgot Password?</h2>
                        <p>Put your code here.</p>
                        <?php echo isset($codemessage) ? $codemessage : "";?>
                        <div class="panel-body">
                            <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-code color-blue"></i></span>
                                        <input id="resetcode" name="resetcode" placeholder="Input your Code Number here"
                                            class="form-control" type="number" required="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input name="validatecode-submit" class="btn btn-lg btn-primary btn-block"
                                        value="Submit" type="submit">
                                    <a href="index.php">Back to site</a>
                                </div>
                                <input type="hidden" class="hide" name="token" id="token" value="">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } elseif (isset($_GET['resetpassword'])) { ?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="text-center">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Forgot Password?</h2>
                        <p>Change your password.</p>
                        <div class="panel-body">
                            <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user color-blue"></i></span>
                                        <input id="newpassword" name="newpassword" placeholder="New Password"
                                            class="form-control" type="password" required="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input name="savepass-submit" class="btn btn-lg btn-primary btn-block" value="Save"
                                        type="submit">
                                    <a href="index.php">Back to site</a>
                                </div>
                                <input type="hidden" class="hide" name="token" id="token"
                                    value="<?php echo $_SESSION['email']; ?>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } elseif (isset($_GET['success'])) { ?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-4">
            <h2 style="color: blue">Password has been changed</h2>
            <a href="index.php">Back to login</a>
        </div>
    </div>
    <?php } else { ?>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                            <h3><i class="fa fa-lock fa-4x"></i></h3>
                            <h2 class="text-center">Forgot Password?</h2>
                            <p>You can reset your password here.</p>
                            <?php echo isset($emailMessage) ? $emailMessage : "";?>
                            <div class="panel-body">
                                <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="fa fa-envelope color-blue"></i></span>
                                            <input id="email" name="email" placeholder="Enter your Email Address"
                                                class="form-control" type="email" required="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input name="recover-submit" class="btn btn-lg btn-primary btn-block"
                                            value="Send" type="submit">
                                        <a href="index.php">Back to site</a>
                                    </div>
                                    <input type="hidden" class="hide" name="token" id="token" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>