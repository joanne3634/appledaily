<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <title>急難家庭曙光再現計畫</title>
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <![endif]-->
    <!-- Google -->
    <meta name="description" content="那些遭逢巨變且急需幫助的家庭，不僅需要您的愛心，也需要更多人共同建構社會的這張保護網。我們希望，為每個捐款人找到最適合且最需要捐助的弱勢家庭，除了能為他們帶來一場及時雨，也藉由這個平台，讓我們更了解捐款者的意願以及行為。">
    <meta name="keywords" content="急需幫助, 家庭, 急難, 保護網, 捐款人, 弱勢家庭, 及時雨">
    <meta name="author" content="DIRL">
    <meta name="copyright" content="急難家庭曙光再現計畫">
    <meta name="application-name" content="急難家庭曙光再現計畫">
    <!-- Facebook -->
    <meta property="og:title" content="急難家庭曙光再現計畫">
    <meta property="og:site_name" content="急難家庭曙光再現計畫">
    <meta property="og:description" content="那些遭逢巨變且急需幫助的家庭，不僅需要您的愛心，也需要更多人共同建構社會的這張保護網。我們希望，為每個捐款人找到最適合且最需要捐助的弱勢家庭，除了能為他們帶來一場及時雨，也藉由這個平台，讓我們更了解捐款者的意願以及行為。">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="zh_TW">
    <meta property="og:locale:alternate" content="en_US">
    <meta content="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/" property="og:url">
    <meta content="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/img/ico/favicon.ico" property="og:image">
    <meta content="image/png" property="og:image:type">
    <meta content="280" property="og:image:width">
    <meta content="280" property="og:image:height">
    <!-- CSS  -->
    <link rel="shortcut icon" type="image/x-icon" sizes="16x16" href="src/img/ico/favicon.ico?v=3">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <link href="src/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="src/css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="src/css/nouislider.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="src/css/form-ui.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="src/css/main-ui.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/fontawesome/4.5.0/css/font-awesome.min.css" />
    <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/jquery.jssocials/1.1.0/jssocials.css" />
    <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/jquery.jssocials/1.1.0/jssocials-theme-flat.css" />
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>
<!-- <body oncontextmenu="return false;">   -->
<style type="text/css">

</style>
<body>
    <div class="navbar-fixed">
        <nav class="white" role="navigation">
            <div class="nav-wrapper container">
                <a id="logo-container" class="brand-logo">
                    <span class="brand-logo-text">急難家庭曙光再現計畫</span>
                </a>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a id="update-subscribe-button-mainside" style="display: none;">更新推薦頻率</a></li>
                    <li>
                        <a id="fb-status"></a>
                    </li>
                </ul>
                <ul id="nav-mobile" class="side-nav">
                    <li>
                        <a id="update-subscribe-button-mobileside" style="display: none;">更新推薦頻率</a>
                    </li>
                    <li>
                        <a id="fb-status-side"></a>
                    </li>
                </ul>
                <a data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            </div>
        </nav>
    </div>
    <main>
        <div id="subscribingPage" style="display:none;"></div>
        <div id="questionairePage" style="display:none;"></div>
        <div id="surveyPage" style="display:none;"></div>
        <div id="thankPage" style="display:none;"></div>
        <div id="landingPage" style="display:none;"></div>
    </main>
    <footer class="page-footer teal">
        <div class="footer-copyright">
            <div class="center-align container">
                © 2016 急難家庭曙光再現計畫
            </div>
        </div>
        <!--  <div class="container">
                <div class="row">
                    <div class="col s12 copyright">
                        <p>© 2016 曙光再現計劃. All rights reserved. <a href="info/terms-of-service">Terms of Service</a> | <a href="info/privacy-policy">Privacy Policy</a></p>
                    </div>
                </div>
            </div> -->
    </footer>
    <a class="cd-top">Top</a>
    <!-- Modal Structure -->
    <div id="promptLogin" class="modal">
        <div class="modal-content" style="padding: 24px 24px 0 24px">
            <h4 class="center">登入提示</h4>
            <p>我們需要您的識別資料，但不會將您的資料散佈或做其他用途、或任意貼文。 往後可以以您的 FB 帳號登入檢視或修改您的資料。</p>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close waves-effect waves-red btn-flat">取消</a>
            <a id="agree-login" class="modal-action modal-close waves-effect waves-green btn-flat">確認</a>
        </div>
    </div>
    <!--         <div id="promptQuestion" class="modal">
            <div class="modal-content" style="padding: 24px 24px 0 24px">
                <h4 class="center">問卷提示</h4>
                <p>您的個人屬性和平時參與的公益行為，可以讓我們更了解捐款人的特質和關注的議題。</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">取消</a>
                <a href="#!" id="agree-question" class="modal-action modal-close waves-effect waves-green btn-flat">確認</a>
            </div>
        </div> -->
    <div id="promptSurvey" class="modal">
        <div class="modal-content center" style="padding: 24px 24px 0 24px">
            <h4 class="center">評分提示</h4>
            <p>看了這篇文章，若您有能力捐款，請問您的捐款意願如何？
                <br>每回合共閱讀 10 篇文章，我們將據此推薦未來的暖流版報導給您。</p>
        </div>
        <div class="modal-footer">
            <a id="agree-survey" class="modal-action modal-close waves-effect waves-green btn-flat">確認</a>
        </div>
    </div>
    <div class="preloader">
        <div class="preloader_image"></div>
    </div>
    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="src/js/materialize.js"></script>
    <script src="src/js/init.js"></script>
    <script src="src/js/nouislider.min.js"></script>
    <script src="src/js/hashids.min.js"></script>
    <script src="src/js/backtotop.js"></script>
    <script src="src/js/ui_texts.js"></script>
    <script src="src/js/ui_logic.js"></script>
    <script src="src/js/common_var.js"></script>
    <script src="src/js/common_func.js"></script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-57ce72f29e93febf"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.jssocials/1.1.0/jssocials.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.5.1/jquery.nicescroll.min.js"></script> -->
    <!-- my script -->
    <script type="text/javascript">
    $(document).ready(function() {

        // $('input').on('focus', function() {
        //     $('body').css('position','fixed');
        // });
        // $('input').on('blur', function() {
        //     $('body').css('position','static');
        // });
        // $("html").niceScroll({
        //     cursorcolor: '#f9c003',
        //     cursorwidth: '8px',
        //     cursorborder: 0,
        //     cursorborderradius: 0,
        //     autohidemode: false,
        //     background: 'rgba(0, 0, 0, 0.15)',
        //     cursorminheight: 30
        // });
        $('#logo-container').click(LoadLandPage);
        $("a[id^='fb-status']").click(ClickFBStatusBtn);
        $('#agree-login').click(ClickLoginBtn);

        window.fbAsyncInit = function() {
            FB.init({
                appId: '342086465999475',
                cookie: true,
                xfbml: true,
                version: 'v2.2'
            });
            <?php       
                if( isset($_GET['history_id']) ){
                    echo "    
                        // subscribe_email
                        $('nav .button-collapse').css('display','none');
                        $('#logo-container').click(function(){
                           window.location.assign(window.location.pathname);
                        });

                        RECOMMEND_PROFILE.history_id = '".$_GET['history_id']."';
                        USER_PROFILE.fbId = '".$_GET['fb_id']."';
                        console.log('".$_GET['fb_id']."'); 
                        BeforeLoadLanding();
                        LoadSubscribingPage('subscribe_email');
                    ";
                }else{
                    echo 'LoadLandPage();';
                }
            ?>
           

            // FB.getLoginStatus(function(response) {
            //     StatusChangeCallback(response);
            // });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.2&appId=342086465999475";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));


    });
    </script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        if (BOOL_VARS.isTesting) {
            s1.src = 'https://embed.tawk.to/57bff569b6fbb95fad802339/default';
        } else {
            s1.src = 'https://embed.tawk.to/57c3c0bab6fbb95fad82f832/default';
        }
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</body>

</html>
