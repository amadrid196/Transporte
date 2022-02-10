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
                 Hello {{$drive_name}},<br>
                 Our records indicate your {{$license}} is going to expire in {{$expire_date}}. <br>
                 Please update us with your new {{$license}} by contacting us via email <a>dispatch@milamtrans.com</a> or call <a>(888) 433-0331 ext700 </a><br><br>

                 Have a great day!<br><br>

                 - Milam Transport<br><br>

                 <b>MILAM TRANSPORT, LLC</b><br>
                 <b>PO BOX 47083 TAMPA, FL 33646</b><br>
                 <b>P: (888)433-0331</b><br>
                 <b><a href="#">DISPATCH@MILAMTRANS.COM</a></b><br>
                 <b>MC 051238 | USDOT 3053736</b><br>
                 <b><i>“Change Starts With Us…”</i></b><br>
                 </div>
            </div>
            <br></br>
            <div class="row">
                <div class="col-md-12">
                    <img src="{{asset('public/images/logo.png')}}">
                </div>
            </div>
        </div>
    </body>
</html>