<?php

if (!strlen($_SERVER["QUERY_STRING"]))
{
    header("Location: index.html");
} else {
    if (isset($_POST['send-email'])) {
        $name= trim($_POST['name']);
        $event= trim($_POST['event']);
        // $event = htmlspecialchars($_POST["event"]);
        $email= trim($_POST['email']);
        $message= trim($_POST['message']);
    
        $is_form_valid = TRUE;
    
        if($name == "")
        {        
           $valid_name = "<h5>Name is required.</h5>";
           $is_form_valid = FALSE;
        }
        else
        {
            $name= filter_var($name, FILTER_SANITIZE_STRING);
            if ($name == FALSE)
            {
                $valid_name .= "<h5>Please enter a valid name.</h5>";
                $is_form_valid = FALSE;
            }
            else 
            {
                if (strpos($name, " ") == FALSE )
                {
                    $valid_name .= "<h5>Please enter your both first and last name</h5>";
                    $is_form_valid = FALSE;
                }
            }
        }
    
        if($email == "")
        {
            $valid_email = "<h5>Email is required.</h5>";
            $is_form_valid = FALSE;
        }
        else
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if ($email == FALSE)
            {
                $valid_email .= "<h5>Please enter a valid email.</h5>";
                $is_form_valid = FALSE;
            }
            else 
            {
                $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                if($email == FALSE)
                {
                    $valid_email .= "<h5>Email failed validation.</h5>";
                    $is_form_valid = FALSE;
                }
                else
                {
                    $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,3})$^";
    
                    if(preg_match($pattern, $email) == FALSE)
                    {   
                        $valid_email .= "<h5>Email does not fit pattern</h5>";
                        $is_form_valid = FALSE;
                    }
                }
            }
        } 
        
        if ($message == "")
        {
            $valid_message .= "<h5>Message is required.</h5>";
            $is_form_valid = FALSE;
        }
        else
        {
            $message = filter_var($message, FILTER_SANITIZE_STRING);
            if ($message == FALSE)
            {
                $valid_message .= "<h5>Please enter a valid message</h5>";
                $is_form_valid = FALSE;
            }
        }
        
        // if ($is_form_valid == TRUE) 
        // {
        //     $valid = "<h5>Yay! Form is validated.</h5>";
        // }
        
        $badStrings = array("Content-Type:", "MIME-Version:", "Content-Transfer-Encoding:", "bcc:", "cc:");
        foreach($_POST as $k => $v)
        { 
            foreach($badStrings as $v2){
                if(strpos($v, $v2) !== false)
                {
                    $is_form_valid = false;
                    $valid .= "<h5>Bad email injector.</h5>";
                }
            }
        }
    
        // $refer = $_SERVER['HTTP_REFERER'];
    
        // if ($refer != "http://jkhanna1.dmitstudent.ca/dmit2025/labs/lab3/contactv2.php") {
        //     $is_form_valid = FALSE;
        //     $valid .= "<h5>not</h5>";
        // }
    
        $ip = $_SERVER['REMOTE_ADDR'];
    
        $spams = array (
        "static.16.86.46.78.clients.your-server.de", 
        "87.101.244.8", 
        "144.229.34.5", 
        "89.248.168.70",
        "reserve.cableplus.com.cn",
        "94.102.60.182",
        "194.8.75.145",
        "194.8.75.50",
        "194.8.75.62",
        "194.170.32.252"
        ); 
    
        foreach ($spams as $site) 
        {
            $pattern = "/$site/i";
            if (preg_match ($pattern, $ip)) 
            {
                $is_form_valid = false;
                $valid .= "<h5>Bad spammer</h5>"	;		   
            }
        }
    
        
        if ($is_form_valid == TRUE) 
        {
            $valid = "<h5>Yay! Form is validated.</h5>";
    
            $to = "jagritikhannaqms@gmail.com";
            $subject = "Web Form Submission";
            
            $body = "<h2>Royal Alberta Museum - Event Confirmation</h2>";
            $body .= "<h5>Name : $name</h5>";
            $body .= "<h5>Event : $event</h5>";
            $body .= "<h5>Email : $email</h5>";
            $body .= "<h5>Message : $message</h5>";
    
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/html ; charset=ISO-8859-1\n";
            $headers .= "X-Priority: 1\n";
            $headers .= "X-MSMail-Prioirty: Normal\n";
            $headers .= "X-Mailer: php\n";
    
            $headers .="From: DMITStudent <info@dmitstudent.ca>\n";
    
            mail($to, $subject, $body, $headers);
            header('Location: confirmation.html');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Alberta Museum</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Royal Alberta Museum Events</h1>
        <h2>Contact Us</h2>
        <div class="form-p">
            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
            <h3 class="alert">*All fields are required.*</h3>

            <?php if ($valid): ?>
            <div class="error">
                <?php echo $valid; ?>
            </div>
            <?php endif ?>

            <div class="form-input">
            <label for="name">Your name: </label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>">
            <?php echo $valid_name; ?>
            </div>
            
            <div class="form-input">
            <?php $event = isset($_GET['event']) ? $_GET['event'] : ''; ?>
            <label for="event">Event: </label>
            <select name="event" id="event">
                <option>Please select an event</option>
                <option<?php echo $event == 'world-war' ? ' selected' : ''; ?> value="world-war">Remembering the First World War - May 27</option>
                <option<?php echo $event == 'from-here' ? ' selected' : ''; ?> value="from-here">I Am From Here - May 28</option>
                <option<?php echo $event == 'vikings' ? ' selected' : ''; ?> value="vikings">Vikings: Beyong the legend - May 29</option>
                <option<?php echo $event == 'late-nights' ? ' selected' : '';?> value="late-nights">Late Nights at RAM - May 30</option>
            </select>
            </div>
            <div class="form-input">
            <label for="email">Email address</label>
            <input type="text" id="email" name="email" value="<?php echo $email; ?>">
            <?php echo $valid_email; ?>
            </div>
            <div class="form-input">
            <label for="message">Message </label>
            <textarea name="message" id="message"><?php echo $message; ?></textarea>
            <?php echo $valid_message; ?>
            </div>
            <input type="submit" name="send-email" value="Send me email">
            </form>
        </div>
        <footer>
        <p class="footer">Jagriti Khanna &copy; 2021 | All content for academic purposes </p>
    </footer>
    </div>

    
</body>
</html>