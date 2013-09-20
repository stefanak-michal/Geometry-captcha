Geometry-captcha
================
Geometry captcha generate image with simple geometry example.  
You can use it two ways, directly show generated image or save it to file.

Example of use:  
1. generate image to file, save filename and result to database  
2. draw it to form with hidden input with identificator (image filename or some ID from database)  
3. after submit form check user result towards database  


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
