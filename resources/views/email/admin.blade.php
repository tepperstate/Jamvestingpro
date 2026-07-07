<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="background-color: rgb(192, 194, 194); font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; margin: 0; padding: 0;">
<br>
<br>
<div class="card" style="background-color: white; width: 100%; max-width: 600px; border: 1px solid rgb(239, 234, 234); margin: 80px auto; padding: 30px;">
    <div class="logo" style="display: flex; justify-content: center; margin-bottom: 20px;">
        <p style="text-align:center"><img src="{{ $message->embed(public_path('assets/images/logo_dark.svg')) }}" alt=""></p>
        </div>
    <h1 style="color: #004080; font-size: 32px; font-weight: 300; text-align: center;">Hello Admin !</h1>
    <p style="color: #8c8c8c; font-size: 19px; font-weight: 300; line-height: 1.4; text-align: center;"><i>A User with name {{$name}} just login your application</i></p>

   

    <hr style="margin-bottom: 40px;">

    <p style="font-size: 15px; color: #5e5e5e; line-height: 1.4; text-align: center;">
        Trading are complex. There is a high risk of losing money rapidly due to leverage. 86% of retail investor accounts lose money when trading CFDs with this provider. You should consider whether you understand how CFDs work and whether you can afford to take the high risk of losing your money.
        <br><br>
        Trading CFDs involves risks – You should consider our Risk Disclosure Notice, available from , before making a decision to acquire or to continue to hold our products.
    </p>
</div>
<br>
<br>

</body>
</html>
