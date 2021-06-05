<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>ScheduleIT</title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>styles/style.css" type="text/css">
            <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Rye&display=swap" rel="stylesheet">
        <noscript>
            <META HTTP-EQUIV="Refresh" CONTENT="0;URL=noJSErrShow.html">
        </noscript>
        <script src="<?php echo base_url(); ?>scripts/validation.js"></script>
    </head>
    <body>
        <div class="main-wrapper">
            <div class="header-wrapper">
                <h1 id="logo-text">ScheduleIT</h1>
                <img id="logo" src="<?php echo base_url(); ?>images/calendar.svg">
            </div>
            <div class="content-wrapper">
                <?php include($requestedPage); ?>
            </div>
            <div class="footer-wrapper">
                <h1>About US:</h1>
                <p>ScheduleIT is Website use to help people to plan and manage schedule.</p>
                <br>
                <p>Special Thanks To Freepik from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></p>
                <p id="dev-info">Developed By Gautam Patel</p>
            </div>
        </div>
    </body>
</html>