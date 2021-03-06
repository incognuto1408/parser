<?php
header('HTTP/1.0 404 Not Found');
http_response_code(404);
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf8" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
        <style type="text/css">
            @import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900);
            body{font-family:Rubik,sans-serif;margin:0;overflow-x:hidden;font-weight:300}
            #wrapper{width:100%}error-box{height:100%;position:fixed;top:20%;width:100%}.error-box .footer{width:100%;left:0;right:0}.error-body{padding-top:5%}.error-body h1{font-size:210px;font-weight:900;line-height:210px}.text-danger{color:#f33155}.text-muted{color:#8d9ea7}.m-b-40{margin-bottom:40px!important}.m-t-30{margin-top:30px!important}.m-b-30{margin-bottom:30px!important}@media only screen and (max-width: 520px){.error-body h1{font-size:110px;font-weight:700;line-height:110px}}
        </style>
        <title>404</title>
    </head>
    <body>
    <section id="wrapper" class="container-fluid">
        <div class="error-box">
            <div class="error-body text-center">
                <h1 class="text-danger">404</h1>
                <h3>Страница не найдена!</h3>
                <br>
                <a href="/" class="btn btn-danger btn-rounded m-b-40">Перейти к главной странице</a> </div>
        </div>
    </section>

    </body>
    </html>
<?php
exit;
?>