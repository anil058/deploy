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
                    <!--logo-->
                    <div class="logo" id="myModalLabel">
                        <h1><a href="/"> <span class="HeadCSS"> MANSHAA REAL</span></a></h1>
                    </div>
                    <!--//logo-->
                    <div class="w3layouts-login1">
                        <a href="/contactus">Contact us </a>
                    </div>
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
                <h4><strong>Terms and Conditions</strong></h4>

                <br>
                <strong><span style="color:blue">1. ILLEGAL ACTIVITY</span></strong>
                <br>
                <p>
                    Distributor may not present Manshaa Real promotions on any page, newsgroup, email or any distribution method that is regarded objectionable by Manshaa Real. Its internet service providers, or otherwise considered unlawful according to any controlling legal authority.
                </p>

                <br>
                <strong><span style="color:blue">2. THIRD-PARTY LIABILITY</span></strong>
                <br>
                <p>
                    Manshaa Real willing no way be liable for the actions of third parties that may in any way cause member harm.
                </p>

                <br>
                <strong><span style="color:blue">3. CHANGES.</span></strong>
                <br>
                <p>
                    Manshaa Real reserves the right to update, modify, add or change the terms and conditions without notice.
                </p>

                <br>
                <strong><span style="color:blue">4. TERMINATION.</span></strong>
                <br>
                <p>
                    The membership of the Distributor can be cancelled anytime by Manshaa Real without giving any reason.
                </p>

                <br>
                <strong><span style="color:blue">5. ETHICS</span></strong>
                <br>
                <p>
                    Any Distributor acting unethical or unprofessional may be removed without refund at the sole discretion of Manshaa Real with all future commissions or lead and/or training usage forfeited.
                </p>


                <br>
                <strong><span style="color:blue">6. CANCELLATION/TERMINATION OF MEMBERSHIP</span></strong>
                <br>
                <p>
                    Any Distributor that is cancelled/ terminated will forfeit all benefits and privileges associated with Manshaa Real. Any positions held in participating programs owned by Manshaa Real will also be forfeited and ownership will be reassigned to Manshaa Real.
                </p>

                <br>
                <strong><span style="color:blue">7. RELATIONSHIP OF PARTIES</span></strong>
                <br>
                <p>
                    While the parties shall work hand-in-hand for the benefit of both, the parties acknowledge and agree that the Distributor shall, from a legal perspective, act as and shall be an independent contractor and not an employee or agent of Manshaa Real. Nothing in this Agreement or these Terms and Conditions shall create a partnership, joint venture, agency, or franchise between the parties in the legal sense of these terms. The Distributor shall not sign any document in the name of or on behalf of Manshaa Real nor shall it hold itself out as being an agent Manshaa Real or as having apparent authority to contract for or bind Manshaa Real
                </p>

                <br>
                <strong><span style="color:blue">8. DISPUTES</span></strong>
                <br>
                <p>
                    Agreements shall be interpreted under the laws of the country of incorporation for Manshaa Real Any disputes made with financial processors for purchases made will result in immediate termination of the Distributorship account with Manshaa Real.
                </p>

                <br>
                <strong><span style="color:blue">9. COPYRIGHT MATERIAL</span></strong>
                <br>
                <p>
                    All branding, logos and graphics contained within Manshaa Real are copyright use; distribution or copying of such content is expressly prohibited. Manshaa Real provides Distributor with approved material for marketing any 3rd party or self-created marketing material must be first approved by Manshaa Real before it may be used.
                </p>

                <br>
                <strong><span style="color:blue">10. DAMAGING INTENT</span></strong>
                <br>
                <p>
                    Any Distributor who engages in chat, email, postings or any other medium, content that is deemed damaging to Manshaa Real and or its employees and/or fellow Distributor will be terminated from Manshaa Real. Depending on seriousness, Manshaa Real may deem it appropriate to exercise legal action
                </p>

                <br>
                <strong><span style="color:blue">11. UCE/UBE or SPAM</span></strong>
                <br>
                <p>
                    Manshaa Real strictly prohibits the use of UCE or SPAM. This enforcement is at the sole discretion of Manshaa Real and for the benefit of all Distributor. Distributors proven to be participating in such activities will have their account(s) terminated and they will forfeit any and all current or future commissions.
                </p>

                <br>
                <strong><span style="color:blue">12. CONTACT INFORMATION</span></strong>
                <br>
                <p>
                    It is the responsibility of Distributors to keep their personal records up to date at the member’s area. Manshaa Real will not be responsible for communication error due to incorrect or out of date contact information. Continued failed attempts to make contact with you may result in the termination of your account.
                </p>

                <br>
                <strong><span style="color:blue">13. TAX</span></strong>
                <br>
                <p>
                    You, the Distributor, are responsible for any and all taxes payable in your resident domicile or jurisdiction, for any income you receive either from Manshaa Real or any leads sold via the Manshaa Real website
                </p>

                <br>
                <strong><span style="color:blue">14. NO WARRANTY</span></strong>
                <br>
                <p>
                    All leads, trainings etc. is provided \As Is\ and without any warranty, express, implied or otherwise, regarding its accuracy or performance
                </p>

                <br>
                <strong><span style="color:blue">15. NO ASSIGNMENT</span></strong>
                <br>
                <p>
                    Distributors may not assign or transfer any rights or obligations under this Agreement without the prior written consent of the other party.
                </p>

                <br>
                <strong><span style="color:blue">16. SEVERABILITY</span></strong>
                <br>
                <p>
                    Distributors and Manshaa Real agree that if any portion of this agreement is found illegal or unenforceable, that portion shall be severed and the remainder of the agreement shall be given full force and effect
                </p>

                <br>
                <strong><span style="color:blue">17. LIMITATION OF LIABILITY; SOLE AND EXCLUSIVE REMEDY; INDEMNIFICATION</span></strong>
                <br>
                <p>
                    You agree that Manshaa Real is NOT responsible for the success or failure of your business or your business decisions relating to any information presented by our company or our company products and/or services.
                </p>

                <br>
                <strong><span style="color:blue">18. ACCURATENESS OF INFORMATION</span></strong>
                <br>
                <p>
                    Manshaa Real makes no representations as to the accurateness or reliability of any advertising message or marketing tool offered through any referral, outside company or on this site. While we believe the people/companies we work with to be honest and ethical, we recommend you do your own due diligence relating to any specific claims or promises made in the marketing message, as it relates to your use and purchase.
                </p>

                <br>
                <strong><span style="color:blue">19. CROSS SPONSORING/CROSS RECRUITING</span></strong>
                <br>
                <p>
                    Cross sponsoring, cross recruiting and cross line jumping are prohibited as per the company's terms and conditions and the Distributor agreement. \Cross sponsoring\ means soliciting a Distributor or any closely related person or entity into a down line different from the existing down line for that Associate, or a closely related person or entity. \Cross jumping\ means an Associated or any closely related person or entity voluntarily taking a business that is not in the same downline as the one in which the Distributor first was placed. \Closely related person or entity\ is any person in the household of the Distributor [e.g. Spouse, Son, Daughter, Parents living in the same household] or any Legal entity which is controlled by the Distributors
                </p>

                <br>
                <strong><span style="color:blue">20. INCOME REPRESENTATION</span></strong>
                <br>
                <p>
                    Distributor shall not make any income representations except those set forth herein or otherwise specifically set forth in official company material.
                </p>

                <br>
                <strong><span style="color:blue">21. Direct Seller (Manshaa Real Associates)</span></strong>
                <br>
                <ol>
                    <li>
                        <p>Direct Seller engaged in direct selling should carry their identity card and not visit the customer‟s premises without prior appointment/approval</p>
                    </li>
                    <li>
                        <p>At the initiation of a sales representation, without request, truthfully and clearly identify themselves, the identity of the direct selling entity, the nature of the goods or services sold and the purpose of the solicitation to the prospective consumer</p>
                    </li>
                    <li>
                        Offer a prospective consumer accurate and complete explanations and demonstrations of goods and services, prices, credit terms, terms of payment, return policies, terms of guarantee, after-sales service
                    </li>
                    <li>
                        Provide the following information to the prospect / consumers at the time of sale, namely:
                        <br>
                        <ul>
                            <li> Name, address, registration number or enrollment number, identity proof and telephone number of the direct seller and details of direct selling entity</li>
                            <li> A description of the goods or services to be supplied;</li>
                            <li> Name, address, registration number or enrollment number, identity proof and telephone number of the direct seller and details of direct selling entity</li>
                            <li> The Order date, the total amount to be paid by the consumer along with the bill and receipt;</li>
                            <li> Time and place for inspection of the sample and delivery of good;</li>
                            <li> Information of his/her rights to cancel the order and / or to return the product in saleable condition and avail full refund on sums paid;</li>
                            <li>Details regarding the complaint redressal mechanism;</li>
                        </ul>
                    </li>
                    <li>A direct seller shall keep proper book of accounts stating the details of the products, price, tax and the quantity and such other details in respect of the goods sold by him/her, in such form as per applicable law.</li>
                    <li>
                        A direct seller shall not:
                        <ul>
                            <li>Use misleading, deceptive and / or unfair trade practices;</li>
                            <li>Use misleading, false, deceptive, and / or unfair recruiting practices, including misrepresentation of actual or potential sales or earnings and advantages of Direct Selling to any prospective direct seller, in their interaction with prospective direct sellers;</li>
                            <li>Make any factual representation to a prospective direct seller that cannot be verified or make any promise that cannot be fulfilled;</li>
                            <li>Present any advantages of Direct Selling to any prospective direct seller in a false and / or a deceptive manner;</li>
                            <li>Knowingly make, omit, engage, or cause, or permit to be made, any representation relating to the Direct Selling operation, including remuneration system and agreement between the Direct Selling entity and the direct seller, or the goods and / or services being sold by such direct seller which is false and / or misleading;</li>
                            <li>Require or encourage direct sellers recruited by the first mentioned direct seller to purchase goods and / or services in unreasonably large amounts;</li>
                            <li>Provide any literature and / or training material not restricted to collateral issued by the Direct Selling entity, to a prospective and / or existing direct sellers both within and outside the parent Direct Selling entity, which has not been approved by the parent Direct Selling entity;</li>
                            <li>Require prospective or existing direct sellers to purchase any literature or training materials or sales demonstration equipment.</li>
                        </ul>
                    </li>
                </ol>







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
                            <h2><a href="/home">Mansha Real</a></h2>
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
