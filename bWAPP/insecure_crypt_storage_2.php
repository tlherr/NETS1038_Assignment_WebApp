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
include("selections.php");

$field_empty = false;
$record_added = false;

if(isset($_REQUEST['insert']))
{
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    
    if($username == "" or $password == "")
    {        
        
        $field_empty = true;
        
    }
    
    else
    {
    
        switch($_COOKIE["security_level"])
        {
         
            case "0" :  
                
                $line = "'" . $username . "', '" . $password . "'" . "\r\n";
                
                break;
    
            case "1" :
                                
                $username = xss_check_3($username);
                $password = sha1($password, false);
                
                $line = "'" . $username . "', '" . $password . "'" . "\r\n";
                            
                break;
        
            case "2" :
                       
                $username = xss_check_3($username);
                $salt = md5(uniqid());                                
                // $password = sha1($salt . $password, false);
                //$password = hash("sha512", $salt . $password, false);
                $password = hash("sha256", $salt . $password, false);
                
                $line = "'" . $username . "', '" . $password . "', 'salt:" . $salt . "'" . "\r\n";
                            
                break;
        
            default :  
                
                $line = "'" . $username . "', '" . $password . "'" . "\r\n";
            
                break;

        } 
        
        // Writes a new line to the file (append)
        $fp = fopen("passwords/accounts.txt", "a");
        fputs($fp, $line, 200);
        fclose($fp);
        
        $record_added = true;
        
    }
    
}

if(isset($_REQUEST['delete'])) 
{
    
    if(file_exists("passwords/accounts.txt"))
    {
    
        // Deletes the file   
        $file = "passwords/accounts.txt";
    
        unlink($file);
        
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

<title>bWAPP - Sensitive Data Exposure</title>

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
    
    <h1>Text Files (Accounts)</h1>

    <p>Insert a new account into a text file:</p>

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">

    <p><label for="username">Username:</label><br />
    <input type="text" id="username" name="username"></p>

    <p><label for="password">Password:</label><br />
    <input type="password" id="password" name="password"></p>

    <input type="submit" name="insert" value="Insert"/>

    </form>

    <br />
    <?php

    if($record_added == true)
    {

        // echo "The account was added: " . $line . "<br /><br />";
        echo "<font color=\"green\">The account was added!</font><br /><br />";

    }

    else

        if($field_empty == true)    
        {

    ?>    
    <font color="red">Please enter both fields...</font><br /><br />    
    <?php

    }

    else
    {

        echo "<br /><br />";

    }

    if(file_exists("passwords/accounts.txt"))
    {      

    ?>

    <a href="passwords/accounts.txt" target="_blank">Download</a> the file.<br />

    <a href="<?php echo($_SERVER["SCRIPT_NAME"]);?>?delete">Delete</a> the file.

    <?php

    }

    else
    {

        echo "<br />";  

    }

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