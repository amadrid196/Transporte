<html>
    <head>
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <style>
        .center {
            text-align: center;
        }
        .container {
            margin-top:20px;
            padding:3%;
        }

        a {
            color: #007bff !important;
        }
    </style>
    <body>
        <div class="container">
            <div class="row center">
                <div class="col-md-12">
              
                </div>  
            </div>
            <h2 class="center"></h2>
            <br>
            
            <br></br>
            <div class="row">
                <div class="col-md-12">
                 Hello {{$admin_name}},<br>
                 Your driver {{ $drive_name }} has a {{$license}} that’s going to expire on {{$expire_date}}. <br>
                 Please contact your driver {{ $drive_name }} {{$driver_phone}}and update their {{$license}} on the website.<br>

                 <a>https://truckers.milamtrans.com/drivers-index</a><br><br>

                 - Milam Transport<br><br>

                 
                 </div>
            </div>
            <br></br>
            <div class="row">
                <div class="col-md-12">
                    
                </div>
            </div>
        </div>
    </body>
</html>