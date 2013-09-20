Geometry-captcha
================

Base usage:
 
    <?php  
    $captcha = new componentCaptcha();  
    $result = $captcha->create();  
    if (true)  
    {  
      //direct show image  
      $captcha->show();  
    }  
    else  
    {  
      //save image to file  
      $captcha->save('./captcha_1657651.jpg');  
    }  
    ?>
