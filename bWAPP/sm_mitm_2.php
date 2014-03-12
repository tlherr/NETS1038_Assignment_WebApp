<?php

/*

bWAPP, or a buggy web application, is a free and open source deliberately insecure web application.
It helps security enthusiasts, developers and students to discover and to prevent web vulnerabilities.
bWAPP covers all major known web vulnerabilities, including all risks from the OWASP Top 10 project!
It is for educational purposes only.

Enjoy!

Malik Mesellem
Twitter: @MME_IT

© 2014 MME BVBA. All rights reserved.

*/

include("security.php");
include("security_level_check.php");
include("functions_external.php");
include("connect_i.php");
include("selections.php");

$message = "";

if($_COOKIE["security_level"] == "1" or $_COOKIE["security_level"] == "2")
{

    $message = "We currently have no cure for this bug. Try to configure SMTPS or IPSEC.";

}

if(isset($_POST["action"]))    
{

    $login = $_SESSION["login"];
    
    // Debugging
    // echo $login;
    
    $sql = "SELECT * FROM users WHERE login = '" . $login . "'";

    // Debugging
    // echo $sql;    

    $recordset = $link->query($sql);             

    if (!$recordset)
    {

        die("Error: " . $link->error);

    }

    // Debugging                   
    // echo "<br />Affected rows: ";                
    // printf($link->affected_rows);

    $row = $recordset->fetch_object();   

    // If the user is present
    if ($row)
    {

        if($_POST["server"] != "")
        {

            ini_set( "SMTP", $_POST["server"]);

        //Debugging
        // $debug = "true";     

        } 

        $secret = $row->secret;
        $email = $row->email;

        // Sends a mail to the user
        $subject = "bWAPP - Forgot";      

        $sender = "bwapp@mailinator.com";

        $content = "Hello " . ucwords($login) . ",\n\n";
        $content.= "Your secret: " . $secret . "\n\n";
        $content.= "Greets from bWAPP!";                 

        $status = @mail($email, $subject, $content, "From: $sender");

        if($status != true)
        {

            $message = "<font color=\"red\">An e-mail could not be send...</font>";

            // Debugging
            // die("Error: mail was NOT send");
            // echo "Mail was NOT send";

        }

        else
        {

            $message = "<font color=\"green\">An e-mail with your secret has been sent to " . $email . ".</font>"; 

        }

    }   
    
}

?>
<!DOCTYPE html>
<html>
    
<head>
        
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Architects+Daughter">
<link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />

<!--<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>-->
<script src="js/html5.js"></script>

<title>bWAPP - Security Misconfiguration</title>

</head>

<body>
    
<header>

<h1>bWAPP</h1>

<h2>an extremely buggy web app !</h2>

</header>    

<div id="menu">
      
    <table>
        
        <tr>
            
            <td><a href="portal.php">Bugs</a></td>
            <td><a href="password_change.php">Change Password</a></td>
            <td><a href="user_extra.php">Create User</a></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>            
            <td><a href="credits.php">Credits</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?php if(isset($_SESSION["login"])){echo ucwords($_SESSION["login"]);}?></font></td>
            
        </tr>
        
    </table>   
   
</div> 

<div id="main">
    
    <h1>Man-in-the-Middle Attack (SMTP)</h1>

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST"> 

        <p>Click the button to mail your secret:
        <button type="submit" name="action" value="forgot">Mail</button></p>

        <p><label for="server">Mail server:</label><br />
        <input type="text" id="server" name="server" value=""></p>

    </form>
    
    <br />

    <?php

    echo $message;

    $link->close();

    ?>


</div>
    
<div id="side">    
    
    <a href="http://itsecgames.blogspot.com" target="blank_" class="button"><img src="./images/blogger.png"></a>
    <a href="http://be.linkedin.com/in/malikmesellem" target="blank_" class="button"><img src="./images/linkedin.png"></a>
    <a href="http://twitter.com/MME_IT" target="blank_" class="button"><img src="./images/twitter.png"></a>
    <a href="http://www.facebook.com/pages/MME-IT-Audits-Security/104153019664877" target="blank_" class="button"><img src="./images/facebook.png"></a>

</div>     
    
<div id="disclaimer">
          
    <p>bWAPP is for educational purposes only / Follow <a href="http://twitter.com/MME_IT" target="_blank">@MME_IT</a> on Twitter and ask for our cheat sheet, containing all solutions! / Need a <a href="http://www.mmeit.be/bWAPP/training.htm" target="_blank">training</a>? / &copy; 2014 MME BVBA</p>
   
</div>
    
<div id="bee">
    
    <img src="./images/bee_1.png">
    
</div>
    
<div id="security_level">
  
    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">
        
        <label>Set your security level:</label><br />
        
        <select name="security_level">
            
            <option value="0">low</option>
            <option value="1">medium</option>
            <option value="2">high</option> 
            
        </select>
        
        <button type="submit" name="form_security_level" value="submit">Set</button>
        <font size="4">Current: <b><?php echo $security_level?></b></font>
        
    </form>   
    
</div>
    
<div id="bug">

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">
        
        <label>Choose your bug:</label><br />
        
        <select name="bug">
   
<?php

// Lists the options from the array 'bugs' (bugs.txt)
foreach ($bugs as $key => $value)
{
    
   $bug = explode(",", trim($value));
   
   // Debugging
   // echo "key: " . $key;
   // echo " value: " . $bug[0];
   // echo " filename: " . $bug[1] . "<br />";
   
   echo "<option value='$key'>$bug[0]</option>";
 
}

?>


        </select>
        
        <button type="submit" name="form_bug" value="submit">Hack</button>
        
    </form>
    
</div>
      
</body>
    
</html>