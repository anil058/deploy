<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>💥 Mansha Real</title>
    <link rel="stylesheet" href="{{ asset('css1/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css1/bootstrap-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css1/font-awesome.css') }}">

    {{-- <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" /><!-- bootstrap-CSS -->
    <link rel="stylesheet" href="css/bootstrap-select.css"><!-- bootstrap-select-CSS -->
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css" media="all" /><!-- Fontawesome-CSS --> --}}


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type='text/javascript' src='{{ asset('js1/jquery-2.2.3.min.js') }}'></script>
    <!-- Custom Theme files -->
    <!--theme-style-->
    <link href="css1/style.css" rel="stylesheet" type="text/css" media="all" />
    <!--//theme-style-->
    <!--meta data-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords"
        content="MLM, Multi level marketing, Hazaribag, Hazaribag MLM, Manshaa Real, Recharge, Online Recharge, Mobile Recharge, Networking, Investment" />
    <script type="application/x-javascript">
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }

    </script>

    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito';
        }

    </style>
</head>

<body class="antialiased">
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            {{-- @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                        @endif
                    @endif
                </div>
            @endif --}}

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <header>
                <div class="container-fluid" style="height: 400px">
                    <!--logo-->
                    <img src="images/mansha-newlogo.png" width="100" height="100" alt=" " class="img-responsive" style=" position: relative; left:100px; "/>

                    <div class="logo" id="myModalLabel">
                        <h1><a href="/home"> <span class="HeadCSS"> MANSHAA REAL</span></a></h1>
                    </div>
                    <!--//logo-->
                    <div class="w3layouts-login1">
                        <a href="/privacypolicy">Contact us </a>
                    </div>
                    <div class="w3layouts-login1">
                        <a href="/terms">Terms and Conditions |</a>
                    </div>
                    <div class="w3layouts-login1">
                        <a href="/privacypolicy">Privacy Policy |</a>
                    </div>

                    <div class="w3layouts-login">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login |</a>
                        {{-- <a data-toggle="modal" data-target="#myModal" href="#"><i class="glyphicon glyphicon-user">
                            </i>Login</a> --}}
                    </div>
                    <div class="clearfix"></div>
                    <!--Login modal-->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;</button>
                                    <h4 class="modal-title">
                                        MANSHAA REAL</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-8 extra-w3layouts"
                                            style="border-right: 1px dotted #C2C2C2;padding-right: 30px;">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#Login" data-toggle="tab">Login</a></li>
                                                <li><a href="#Registration" data-toggle="tab">Register</a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="Login">
                                                    <form class="form-horizontal" action="#" method="get">
                                                        <div class="form-group">
                                                            <label for="email" class="col-sm-2 control-label">
                                                                Email</label>
                                                            <div class="col-sm-10">
                                                                <input type="email" class="form-control" id="email1"
                                                                    placeholder="Email" required="required" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1"
                                                                class="col-sm-2 control-label">
                                                                Password</label>
                                                            <div class="col-sm-10">
                                                                <input type="password" class="form-control"
                                                                    id="exampleInputPassword1" placeholder="password"
                                                                    required="required" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <button type="submit"
                                                                    class="submit btn btn-primary btn-sm">
                                                                    Submit</button>
                                                                <a href="javascript:;" class="agileits-forgot">Forgot
                                                                    your password?</a>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="tab-pane" id="Registration">
                                                    <form class="form-horizontal" action="#" method="get">
                                                        <div class="form-group">
                                                            <label for="email" class="col-sm-2 control-label">
                                                                Name</label>
                                                            <div class="col-sm-10">
                                                                <div class="row">
                                                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                                                        <select class="form-control">
                                                                            <option>Mr.</option>
                                                                            <option>Ms.</option>
                                                                            <option>Mrs.</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-9 col-sm-9 col-xs-9">
                                                                        <input type="text" class="form-control"
                                                                            placeholder="Name" required="required" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="email" class="col-sm-2 control-label">
                                                                Email</label>
                                                            <div class="col-sm-10">
                                                                <input type="email" class="form-control" id="email"
                                                                    placeholder="Email" required="required" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="mobile" class="col-sm-2 control-label">
                                                                Mobile</label>
                                                            <div class="col-sm-10">
                                                                <input type="tel" class="form-control" id="mobile"
                                                                    placeholder="Mobile" required="required" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="password" class="col-sm-2 control-label">
                                                                Password</label>
                                                            <div class="col-sm-10">
                                                                <input type="password" class="form-control"
                                                                    id="password" placeholder="Password"
                                                                    required="required" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <button type="submit"
                                                                    class="submit btn btn-primary btn-sm">
                                                                    Save & Continue</button>
                                                                <button type="reset"
                                                                    class="submit btn btn-default btn-sm">
                                                                    Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="OR">
                                                OR</div>
                                        </div>
                                        <div class="col-md-4 extra-agileits">
                                            <div class="row text-center sign-with">
                                                <div class="col-md-12">
                                                    <h3 class="other-nw">
                                                        Sign in with</h3>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="btn-group btn-group-justified">
                                                        <a href="#" class="btn btn-primary">Facebook</a> <a href="#"
                                                            class="btn btn-danger">
                                                            Google +</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--//Login modal-->
                </div>
            </header>
            <!--//-->
            <div class=" header-right">
                <div class="banner">
                    <div class="slider">
                        <div class="callbacks_container">
                            <ul class="rslides" id="slider">
                                <li>
                                    <div class="banner1">
                                        <div class="caption">
                                            <h3><span style="color: black">Be your own boss</h3>
                                            <p><a href="#"><i class="fa fa-phone"></i> 9546291136</a></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="banner2">
                                        <div class="caption">
                                            <h3><span>Best way to</span> earn money</h3>
                                            <p><a href="#"><i class="fa fa-phone"></i> 9546291136</a></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="banner3">
                                        <div class="caption">
                                            <h3><span>Hastle Free</span> Income</h3>
                                            <p><a href="#"><i class="fa fa-phone"></i> 9546291136</a></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="banner4">
                                        <div class="caption">
                                            <h3><span>Assured benefit in </span> every Recharge</h3>
                                            <p><a href="#"><i class="fa fa-phone"></i> 9546291136</a></p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- content -->
            <div class="terms w3ls-about w3layouts-content">
                <div class="container">
                    <h3 class="w3-head">Life Time Income</h3>
                    <p>BOOM BOOM BOOM BOOM!! भारत में पहली बार🕺नेटवर्क मार्केटिंग में धमाका !! मात्र 1180 रु लगाके 6
                        अरब से भी ज्यादा रुपये कमाने का सुनहरा मौका। </p>

                    <h6>छोटा पैकेज बड़ा धमाका !</h6>
                    <ol start="1">
                        <li>Joining Amount Rs1000+(18%Gst180)=1180</li>
                        <li> 03Aug2020
                            से स्टार्ट होने जा रहा है । किस्मत बदलने वाला मात्र एक प्लान,MANSHAA REAL E-COMMERCE PVT.
                            LTD.वो भी अपना हजारीबाग से।
                            जो पिछले 11 सालों से हमारे बीच रहकर रियल स्टेट सेक्टर में लाखों लोगों को बेहतर सर्विस दे रही
                            है ।
                            और सबसे बड़ी बात लाखों रुपये कमाने वाले बहुत लोग हैं इस कंपनी में ,जो पिछले 11 साल से कंपनी
                            के साथ जुड़े हुये हैं, और भी लोग जुड़ते जा रहे हैं ।
                            क्योंकि MANSHAA का दूसरा नाम ही सफलता है। </li>
                        <li>1180रु से जुड़ते ही 1000 Reward Point आपके Digital Wallet में जमा हो जाएंगे । जिसे आप हर
                            सर्विस में 0.5% से 75% तक उपयोग कर सकते हैं ।</li>
                        <li><strong>Note:-</strong>1Reward Point= 1Rs और हर डाइरेक्ट जॉइनिंग से आपको 30रिवार्ड पॉइंट
                            फ्री में मिलता है ।</li>
                    </ol>
                    <!-- type of income -->
                    <h3 class="w3-head">Types Of Income:</h3>
                    <ol start="1">
                        <li>Level Income</li>
                        <li>Club Income </li>
                        <li>Leaddership Income</li>
                        <li>Reward Income</li>
                        <li>Recharge Income </li>
                        <li>Booking Income</li>
                        <li>Royalty Income</li>
                    </ol>
                    <!-- type of income -->
                    <!-- level income -->
                    <!-- tab pane -->
                    <div id="parentHorizontalTab" style="display: block; width: 100%; margin: 0px;">
                        <ul class="resp-tabs-list hor_1">
                            <li class="resp-tab-item hor_1 resp-tab-active" aria-controls="hor_1_tab_item-0" role="tab"
                                style="background-color: white; border-color: rgb(193, 193, 193);">Level Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-1" role="tab"
                                style="background-color: rgb(245, 245, 245);">Club Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-2" role="tab"
                                style="background-color: rgb(245, 245, 245);">Leadership Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-3" role="tab"
                                style="background-color: rgb(245, 245, 245);">Reward Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-4" role="tab"
                                style="background-color: rgb(245, 245, 245);">Recharge Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-5" role="tab"
                                style="background-color: rgb(245, 245, 245);">Booking Income</li>
                            <li class="resp-tab-item hor_1" aria-controls="hor_1_tab_item-6" role="tab"
                                style="background-color: rgb(245, 245, 245);">Royalty Income</li>
                        </ul>
                        <div class="resp-tabs-container hor_1" style="border-color: rgb(193, 193, 193);">
                            <h2 class="resp-accordion hor_1 resp-tab-active" role="tab" aria-controls="hor_1_tab_item-0"
                                style="background: none; border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>FULLTT</h2>
                            <div class="resp-tab-content hor_1 resp-tab-content-active"
                                aria-labelledby="hor_1_tab_item-0" style="display:block">
                                <h6>🔰🔰Level Income🔰🔰</h6>
                                <p>जब आप 5 सदस्य को लाते हैं, और वो भी अपना-अपना 5-5सदस्य को लाते हैं । तो आपकी हर लेवल
                                    की इनकम इस 👇तरह होती है ।</p>
                                <hr>
                                <div class="panel">
                                    <div class="panel-body">
                                        <table class="table table-hover table-responsive table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Level</th>
                                                    <th>1ld Inc</th>
                                                    <th>5*5ld Inc</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Level 1</td>
                                                    <td>200</td>
                                                    <td>1000</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 2</td>
                                                    <td>100</td>
                                                    <td>2500</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 3</td>
                                                    <td>70</td>
                                                    <td>8750</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 4</td>
                                                    <td>50</td>
                                                    <td>31250</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 5</td>
                                                    <td>40</td>
                                                    <td>125000</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 6</td>
                                                    <td>30</td>
                                                    <td>468750</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 7</td>
                                                    <td>30</td>
                                                    <td>2343750</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 8</td>
                                                    <td>25</td>
                                                    <td>9765625</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 9</td>
                                                    <td>25</td>
                                                    <td>48828125</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 10</td>
                                                    <td>20</td>
                                                    <td>195312500</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 11</td>
                                                    <td>20</td>
                                                    <td>976562500</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 12</td>
                                                    <td>20</td>
                                                    <td>4882812500</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <p>Total Income:-6अरब,11करोड़,62लाख,62हज़ार,2सौ,50 रुपये</p>
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-1"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>TOPUP</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-1"
                                style="border-color: rgb(193, 193, 193);">
                                <h6>🔰🔰Club Income🔰🔰</h6>
                                <ol start="1">
                                    <li>Bronze Achiever</li>
                                    <li>Silver Achiever</li>
                                    <li>Gold Achiever</li>
                                    <li>Diamond Achiever</li>
                                </ol>
                                <hr>
                                <h6>1. 🔮Bronze Achiever🔮</h6>
                                <p>जितने भी सदस्य अपने-अपने डायरेक्ट 5-5 सदस्यों को जॉइन करवाते हैं, वो सभी सदस्य Bronze
                                    Achiever हो जाते हैं।इन सभी सदस्यों को कम्पनी अपनी टर्न ओवर का 5% रुपये निकाल कर
                                    बराबर बराबर बाँट देती है।</p>
                                <hr>
                                <h6>2. 🔮 Silver Achiever🔮</h6>
                                <p>जब आप 3rd लेवल कम्प्लीट कर लेते हैं। 👇कम्पनी के नियमानुसार</p>
                                <ol start="1">
                                    <li>1st level 5 members</li>
                                    <li>2nd Level 25 Members</li>
                                    <li>3rd Level 125 Members</li>
                                </ol>
                                <p>तब आप Silver Achiever बन जाते हैं , यहाँ आपको आपके टीम के टर्न ओवर का 5% रुपये मिलते
                                    हैं। आजीवन ।</p>
                                <hr>
                                <h6>3. 🔮Gold Achiever🔮</h6>
                                <p>जब आप 6th लेवल कम्प्लीट कर लेते हैं। 👇कम्पनी के नियमानुसार</p>
                                <ol>
                                    <li>4th level 375 members</li>
                                    <li>5th Level 1125 Members</li>
                                    <li>6th Level 3375 Members</li>
                                </ol>
                                <p>तब आप Gold Achiever बन जाते हैं , यहाँ आपको आपके टीम के टर्न ओवर का 3% रुपये मिलते
                                    हैं। आजीवन ।</p>
                                <hr>
                                <h6>4. 🔮Diamond Achiever🔮</h6>
                                <p>जब आप 9th लेवल कम्प्लीट कर लेते हैं । 👇कम्पनी के नियमानुसार</p>
                                <ol>
                                    <li>7th level 10125 members</li>
                                    <li>8th Level 30375 Members</li>
                                    <li>9th Level 91125 Members</li>
                                </ol>
                                <p>तब आप Diamond Achiever बन जाते हैं , यहाँ आपको आपके टीम के टर्न ओवर का 2% रुपये मिलते
                                    हैं। आजीवन ।</p>
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-2"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>3G/4G</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-2"
                                style="border-color: rgb(193, 193, 193);">
                                <!-- Leadership Income -->
                                <h6>🔰🔰Leadership Income🔰🔰</h6>
                                <p>जितने भी सदस्य आपके डायरेक्ट जुड़े हैं , वो सभी आपके 1st लेवल और उनके जितने भी
                                    डाइरेक्ट जुड़े सदस्य हैं, वो आपके 2nd लेवल होंगें।इन सभी के लेवल इन्कम का 5% और 3%
                                    आपको मिलेगा।</p>
                                <ol start="1">
                                    <li>1st Level 5%</li>
                                    <li>2nd Level 3%</li>
                                </ol>
                                <!-- Leadership Income -->
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-3"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>SPL/RATE CUTTER</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-3"
                                style="border-color: rgb(193, 193, 193);">
                                <!-- Reward Income -->
                                <h6>🔰🔰Reward Income🔰🔰</h6>
                                <div class="panel">
                                    <div class="panel-body">
                                        <table class="table table-hover table-responsive table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Level</th>
                                                    <th>Members.</th>
                                                    <th>Reward</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Level 2</td>
                                                    <td>25</td>
                                                    <td>1000</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 3</td>
                                                    <td>125</td>
                                                    <td>5000</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 4</td>
                                                    <td>625</td>
                                                    <td>25000</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 5</td>
                                                    <td>3125</td>
                                                    <td>Pulsar 150</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 6</td>
                                                    <td>15625</td>
                                                    <td>Wagon R vxi</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 7</td>
                                                    <td>78125</td>
                                                    <td>Innova Crysta</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 8</td>
                                                    <td>390625</td>
                                                    <td>Fortuner</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 9</td>
                                                    <td>1953125</td>
                                                    <td>BMW X5</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 10</td>
                                                    <td>9765625</td>
                                                    <td>8000Sq Ft Plot</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 11</td>
                                                    <td>48828125</td>
                                                    <td>3BHK Flat+1Cr Cash</td>
                                                </tr>
                                                <tr>
                                                    <td>Level 12</td>
                                                    <td>244140625</td>
                                                    <td>Bung.+2.5Cr Cash</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Reward Income -->
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-4"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>2G</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-4"
                                style="border-color: rgb(193, 193, 193);">
                                <h6>🔰🔰Recharge Income🔰🔰</h6>
                                <p>आप जितने रुपये का मोबाईल रिचार्ज करते हैं, उसका 1% Reward Point से कटेगा और जितना
                                    रुपया आपका रिचार्ज में कटता है ,उसका 2% आपको Cash Back मिलता है। इसे आप कोई भी
                                    सर्विस में Use कर सकते हैं ।</p>
                                <p> उदाहरण</p>
                                <ol start="1">
                                    <li>500*1%=5 रिवॉर्ड पॉइन्ट</li>
                                    <li>495*2%=9.90 कैश बैक</li>
                                    <li>1% Rp And 2% CB</li>
                                    <li>Total Income 3%</li>
                                </ol>
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-5"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>2G</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-4"
                                style="border-color: rgb(193, 193, 193);">
                                <h6>🔰🔰Booking Income🔰🔰</h6>
                                <p>हमारे यहाँ से कोई भी सामान की Booking करते हैं, तो आपको ये छूट मिलेगा। 0.5 To 50 %
                                </p>
                            </div>
                            <h2 class="resp-accordion hor_1" role="tab" aria-controls="hor_1_tab_item-6"
                                style="background-color: rgb(245, 245, 245); border-color: rgb(193, 193, 193);"><span
                                    class="resp-arrow"></span>2G</h2>
                            <div class="resp-tab-content hor_1" aria-labelledby="hor_1_tab_item-4"
                                style="border-color: rgb(193, 193, 193);">
                                <!-- Royalty Income -->
                                <h6>🔰🔰 Royalty Income🔰🔰</h6>
                                <p>जब आप 12th लेवल कम्प्लीट कर लेते हैं। 👇कम्पनी के नियमानुसार</p>
                                <ol start="1">
                                    <li>10th level 182250 members</li>
                                    <li>11th Level 364500 Members</li>
                                    <li>12th Level 729000 Members</li>
                                </ol>
                                <p>तब आप Royalty Achiever बन जाते हैं , यहाँ आपको कम्पनी के टर्न ओवर का 1% रुपये मिलते
                                    हैं। आजीवन ।</p>
                                <!-- Royality Income -->
                            </div>
                        </div>
                        <!-- tab pane -->
                    </div>
                </div>
            </div>
            <!-- content -->


            <!--phone-->
            <div class="phone" id="mobileappagileits">
                <div class="container">
                    <div class="col-md-6">
                        <img src="images/mobile1.png" class="img-responsive" alt="" />
                    </div>
                    <div class="col-md-6 phone-text">
                        <h4>E-COMMERCE की सारी सुविधाएं ले सकते हैं Manshaa की App se!</h4>
                        <p class="subtitle">Simple and Fast Payments</p>
                        <div class="text-1">
                            <h5>Withdrawal System</h5>
                            <p>IMPS Withdrawal</p>
                            <p>Minimum Withdrawal Rs100/- Every Day</p>
                            <p>Maximum withdrawal Rs10000/-Every Day</p>
                            <p>Rest Balance Weekly Account Transfer Every Wednesday</p>
                            <p>Deduction 5%Tds & 6%Admin Charge</p>
                        </div>
                        <div class="text-1">
                            <h5>एक मोबाइल नम्बर और एक पैन कार्ड से एक आई डी लगेगी।</h5>
                            <p>और अधिक जानकारी के लिए आप मुझे Call या Whatsapp कर सकते हैं नीचे दिए गए नम्बर पर <h5><i
                                        class="fa fa-phone"></i> 9546291136</h5>
                            </p>
                        </div>
                        <div class="agileinfo-dwld-app">
                            <h6>Download The App :
                                <a href="#"><i class="fa fa-apple"></i></a>
                                <a href="#"><i class="fa fa-windows"></i></a>
                                <a href="#"><i class="fa fa-android"></i></a>
                            </h6>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="wthree-mobile-app">
                        <form action="#" method="get">
                            <input class="text" type="tel" name="tel" placeholder="Enter Your Mobile Number"
                                required="">
                            <input type="submit" value="Send Download Link">
                        </form>
                    </div>
                </div>
            </div>
            <!--//phone-->


            <!-- Support content -->
            <div class="w3l-support">
                <div class="container">
                    <div class="col-md-5 w3_agile_support_left">
                        <img src="images/telecalling1.png" alt=" " class="img-responsive" />
                    </div>
                    <div class="col-md-7 w3_agile_support_right">
                        <h5>Manshaa Real</h5>
                        <h3>24/7 Customer Service Support</h3>
                        <p>Our dedicated team is ready to assist you 24 x 7. Just call us if you have any queries
                            regarding our services. </p>
                        <div class="agile_more">
                            <a href="support.html" class="type-4">
                                <span> Support </span>
                                <span> Support </span>
                                <span> Support </span>
                                <span> Support </span>
                                <span> Support </span>
                                <span> Support </span>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"> </div>
                </div>
            </div>
            <!-- //Support content -->


            <!--offers-->
            <div class="w3-offers">
                <div class="container">
                    <div class="w3-agile-offers">
                        <h3>the best offers</h3>
                        <p>With every mobile recharge you get bonus point. Recharge has never been so easy and
                            beneficial.</p>
                    </div>
                </div>
            </div>
            <!--//offers-->

            <!-- subscribe -->
            {{-- <div class="w3-subscribe agileits-w3layouts">
                <div class="container">
                    <div class="col-md-6 social-icons w3-agile-icons">
                        <h4>Join Us</h4>
                        <ul>
                            <li><a href="#" class="fa fa-facebook sicon facebook"> </a></li>
                            <li><a href="#" class="fa fa-twitter sicon twitter"> </a></li>
                            <li><a href="#" class="fa fa-google-plus sicon googleplus"> </a></li>
                            <li><a href="#" class="fa fa-dribbble sicon dribbble"> </a></li>
                            <li><a href="#" class="fa fa-rss sicon rss"> </a></li>
                        </ul>
                    </div>
                    <div class="col-md-6 w3-agile-subscribe-right">
                        <h3 class="w3ls-title">Subscribe to Our <br><span>Newsletter</span></h3>
                        <form action="#" method="post">
                            <input type="email" name="email" placeholder="Enter your Email..." required="">
                            <input type="submit" value="Subscribe">
                            <div class="clearfix"> </div>
                        </form>
                    </div>
                    <div class="clearfix"> </div>
                </div>
            </div> --}}
            <!-- //subscribe -->

            <!--footer-->
            <footer>
                <div class="container-fluid">
                    <div class="w3-agile-footer-top-at">
                        <div class="col-md-2 agileits-amet-sed">
                            <h4>Mansha Real</h4>
                            <ul class="w3ls-nav-bottom">
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="#l">Support</a></li>
                                <li><a href="#">Sitemap</a></li>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Faq</a></li>
                                {{-- <li><a href="index.html#mobileappagileits">Mobile</a></li> --}}
                                <li><a href="#">Feedback</a></li>
                                <li><a href="#">Contact</a></li>
                                {{-- <li><a href="#">Shortcodes</a></li>
                                <li><a href="#">Icons Page</a></li> --}}
                            </ul>
                        </div>
                        <div class="col-md-3 agileits-amet-sed ">
                            <h4>Mobile Recharges</h4>
                            {{-- <ul class="w3ls-nav-bottom">
                                <li><a href="index.html#parentVerticalTab1">Airtel</a></li>
                                <li><a href="index.html#parentVerticalTab1">Aircel</a></li>
                                <li><a href="index.html#parentVerticalTab1">Vodafone</a></li>
                                <li><a href="index.html#parentVerticalTab1">BSNL</a></li>
                                <li><a href="index.html#parentVerticalTab1">Tata Docomo</a></li>
                                <li><a href="index.html#parentVerticalTab1">Reliance GSM</a></li>
                                <li><a href="index.html#parentVerticalTab1">Reliance CDMA</a></li>
                                <li><a href="index.html#parentVerticalTab1">Telenor</a></li>
                                <li><a href="index.html#parentVerticalTab1">MTS</a></li>
                                <li><a href="index.html#parentVerticalTab1">Jio</a></li>
                            </ul> --}}
                        </div>
                        <div class="col-md-3 agileits-amet-sed ">
                            <h4>DATACARD RECHARGES</h4>
                            {{-- <ul class="w3ls-nav-bottom">
                                <li><a href="index.html#parentVerticalTab3">Tata Photon</a></li>
                                <li><a href="index.html#parentVerticalTab3">MTS MBlaze</a></li>
                                <li><a href="index.html#parentVerticalTab3">MTS MBrowse</a></li>
                                <li><a href="index.html#parentVerticalTab3">Airtel</a></li>
                                <li><a href="index.html#parentVerticalTab3">Aircel</a></li>
                                <li><a href="index.html#parentVerticalTab3">BSNL</a></li>
                                <li><a href="index.html#parentVerticalTab3">MTNL Delhi</a></li>
                                <li><a href="index.html#parentVerticalTab3">Vodafone</a></li>
                                <li><a href="index.html#parentVerticalTab3">Idea</a></li>
                                <li><a href="index.html#parentVerticalTab3">MTNL Mumbai</a></li>
                                <li><a href="index.html#parentVerticalTab3">Tata Photon Whiz</a></li>
                            </ul> --}}
                        </div>
                        <div class="col-md-2 agileits-amet-sed">
                            <h4>DTH Recharges</h4>
                            {{-- <ul class="w3ls-nav-bottom">
                                <li><a href="index.html#parentVerticalTab2"> Airtel Digital TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Dish TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Tata Sky Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Reliance Digital TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Sun Direct Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Videocon D2H Recharges</a></li>
                            </ul> --}}
                        </div>
                        <div class="col-md-2 agileits-amet-sed ">
                            <h4>Payment Options</h4>
                            <ul class="w3ls-nav-bottom">
                                <li>Credit Cards</li>
                                <li>Debit Cards</li>
                                <li>Any Visa Debit Card (VBV)</li>
                                <li>Direct Bank Debits</li>
                                <li>Cash Cards</li>
                            </ul>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
                <div class="w3l-footer-bottom">
                    <div class="container-fluid">
                        <div class="col-md-4 w3-footer-logo">
                            <h2><a href="index.html">Mansha Real</a></h2>
                        </div>
                        <div class="col-md-8 agileits-footer-class">
                            <p>© 2020 Mansha Real All Rights Reserved | Design by <a href="#"
                                    target="_blank">JumboCoder</a> </p>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
            </footer>
            <!--//footer-->


            <!-- //Bootstrap select option script -->

            <!-- easy-responsive-tabs -->
            <link rel="stylesheet" type="text/css" href="css/easy-responsive-tabs.css " />


        </div>
    </div>


    <!-- for bootstrap working -->
    <script src="js/bootstrap.js"></script>
    <!-- //for bootstrap working -->
    <!-- Responsive-slider -->
    <!-- Banner-slider -->
    <script src="js/responsiveslides.min.js"></script>
    <script>
        $(function () {
            $("#slider").responsiveSlides({
                auto: true,
                speed: 500,
                namespace: "callbacks",
                pager: true,
            });
        });

    </script>
    <!-- //Banner-slider -->
    <!-- //Responsive-slider -->
    <!-- Bootstrap select option script -->
    <script src="js/bootstrap-select.js"></script>
    <script>
        $(document).ready(function () {
            var mySelect = $('#first-disabled2');

            $('#special').on('click', function () {
                mySelect.find('option:selected').prop('disabled', true);
                mySelect.selectpicker('refresh');
            });

            $('#special2').on('click', function () {
                mySelect.find('option:disabled').prop('disabled', false);
                mySelect.selectpicker('refresh');
            });

            $('#basic2').selectpicker({
                liveSearch: true,
                maxOptions: 1
            });
        });

    </script>


    <script src="js/easyResponsiveTabs.js"></script>
    <!-- //easy-responsive-tabs -->
    <!-- here stars scrolling icon -->
    <script type="text/javascript">
        $(document).ready(function () {
            /*
            	var defaults = {
            	containerID: 'toTop', // fading element id
            	containerHoverID: 'toTopHover', // fading element hover id
            	scrollSpeed: 1200,
            	easingType: 'linear'
            	};
            */

            $().UItoTop({
                easingType: 'easeOutQuart'
            });

        });

    </script>
    <!-- start-smoth-scrolling -->
    <script type="text/javascript" src="js/move-top.js"></script>
    <script type="text/javascript" src="js/easing.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $(".scroll").click(function (event) {
                event.preventDefault();
                $('html,body').animate({
                    scrollTop: $(this.hash).offset().top
                }, 1000);
            });
        });

    </script>
    <!-- star-smoth-scrolling -->
    <!--Plug-in Initialisation-->
    <script type="text/javascript">
        $(document).ready(function () {
            //horizontal tab
            $('#parentHorizontalTab').easyResponsiveTabs({
                type: 'default', //Types: default, vertical, accordion
                width: 'auto', //auto or any width like 600px
                fit: true, // 100% fit in a container
                tabidentify: 'hor_1', // The tab groups identifier
                activate: function (event) { // Callback function if tab is switched
                    var $tab = $(this);
                    var $info = $('#nested-tabInfo');
                    var $name = $('span', $info);
                    $name.text($tab.text());
                    $info.show();
                }
            });
            //Vertical Tab
            $('#parentVerticalTab').easyResponsiveTabs({
                type: 'vertical', //Types: default, vertical, accordion
                width: 'auto', //auto or any width like 600px
                fit: true, // 100% fit in a container
                closed: 'accordion', // Start closed if in accordion view
                tabidentify: 'hor_1', // The tab groups identifier
                activate: function (event) { // Callback function if tab is switched
                    var $tab = $(this);
                    var $info = $('#nested-tabInfo2');
                    var $name = $('span', $info);
                    $name.text($tab.text());
                    $info.show();
                }
            });
        });

    </script>

</body>

</html>
