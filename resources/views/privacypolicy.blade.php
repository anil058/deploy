<!DOCTYPE html>
<html lang="en">
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
<body>
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <header>
                <div class="container">
                    <div class="logo" id="myModalLabel">
                        <h1><a href="/home"> <span class="HeadCSS"> MANSHAA REAL</span></a></h1>
                    </div>
                    <!--//logo-->
                    <div class="w3layouts-login1">
                        <a href="/terms">Terms and Conditions</a>
                    </div>
                    <div class="w3layouts-login1">
                        <a href="/privacypolicy">Privacy Policy</a>
                    </div>

                    <div class="w3layouts-login">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>
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
                                            <h3><span>Be your </span> own boss</h3>
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

            <div class="container">
                <br>
                <h4><strong>Privacy Policy</strong></h4>

                <br>
                <strong><span style="color:blue">Information Collection And Use</span></strong>
                <br>
                <p>
                    While using our Site, we may ask you to provide us with certain personally identifiable information that can be used
                    to contact or identify you. Personally identifiable information may include, but is not limited to, your name, email address,
                     postal address and phone number (“Personal Information”). Like many site operators, we collect information that your browser
                     sends whenever you visit our Site (“Log Data”). This Log Data may include information such as your computer’s Internet Protocol
                      (“IP”) address, browser type, browser version, the pages of our Site that you visit, the time and date of your visit, the time
                      spent on those pages and other statistics.
                </p>

                <br>
                <strong><span style="color:blue">Cookies</span></strong>
                <br>
                <p>
                    Cookies are files with small amount of data, which may include an anonymous unique identifier. Cookies are sent to your browser from a web
                     site and stored on your computer’s hard drive. Like many sites, we use “cookies” to collect information. You can instruct your browser to
                     refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some
                     portions of our Site.
                </p>

                <br>
                <strong><span style="color:blue">Security</span></strong>
                <br>
                <p>
                    "The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method
                    of electronic storage, is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information,
                    we cannot guarantee its absolute security.
                </p>

                <br>
                <strong><span style="color:blue">Links To Other Sites</span></strong>
                <br>
                <p>
                    "Our Site may contain links to other sites that are not operated by us. If you click on a third party link, you will be directed to that
                    third party site. We strongly advise you to review the Privacy Policy of every site you visit. Manshaa Real Pvt. Ltd. has no control over,
                    and assumes no responsibility for, the content, privacy policies, or practices of any third party sites or services.
                </p>

                <br>
                <strong><span style="color:blue">Changes To This Privacy Policy</span></strong>
                <br>
                <p>
                    Manshaa Real Pvt. Ltd. may update this Privacy Policy from time to time. We will notify you of any changes by posting the new
                    Privacy Policy on the Site. You are advised to review this Privacy Policy periodically for any changes.
                </p>


            </div>
            <!--footer-->
            <footer>
                <div class="container-fluid">
                    <div class="w3-agile-footer-top-at">
                        <div class="col-md-2 agileits-amet-sed">
                            <h4>Company</h4>
                            <ul class="w3ls-nav-bottom">
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="#l">Support</a></li>
                                <li><a href="#">Sitemap</a></li>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Faq</a></li>
                                <li><a href="index.html#mobileappagileits">Mobile</a></li>
                                <li><a href="#">Feedback</a></li>
                                <li><a href="#">Contact</a></li>
                                <li><a href="#">Shortcodes</a></li>
                                <li><a href="#">Icons Page</a></li>

                            </ul>
                        </div>
                        <div class="col-md-3 agileits-amet-sed ">
                            <h4>Mobile Recharges</h4>
                            <ul class="w3ls-nav-bottom">
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
                            </ul>
                        </div>
                        <div class="col-md-3 agileits-amet-sed ">
                            <h4>DATACARD RECHARGES</h4>
                            <ul class="w3ls-nav-bottom">
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
                            </ul>
                        </div>
                        <div class="col-md-2 agileits-amet-sed">
                            <h4>DTH Recharges</h4>
                            <ul class="w3ls-nav-bottom">
                                <li><a href="index.html#parentVerticalTab2"> Airtel Digital TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Dish TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Tata Sky Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Reliance Digital TV Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Sun Direct Recharges</a></li>
                                <li><a href="index.html#parentVerticalTab2">Videocon D2H Recharges</a></li>
                            </ul>
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
        </div>
    </div>
</body>
</html>
