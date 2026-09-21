<?php
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1].'/control_sidebar.js', ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']);


$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['lastlogin','ischief'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}



?>

<script>
//let $dark_mode_checkbox = $('<input />', {
      //  type: 'checkbox',
     //   value: 1,
     //   checked: $('body').hasClass('dark-mode'),
    //    class: 'mr-1'
   // }).on('click', function () {
       function dm(){
           console.log($(this));
            
       // if ($(this).is(':checked')) {
        if ($('body').hasClass('dark-mode') ) {
$('body').removeClass('dark-mode');
        }else{
        $('body').addClass('dark-mode');
        }
       // else {
       //     $('body').removeClass('dark-mode');
        //}
   }
    //)
    ;
 //   let $dark_mode_container = $('<div />', 
 //   {class: 'mb-4'}).append($dark_mode_checkbox).append('<span>Dark Mode</span>');
 //   $container.append($dark_mode_container);
 
 
 
 </script>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links 
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" data-enable-remember="true">
                <i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?=\yii\helpers\Url::home()?>" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>

        <li><?=Html::a('Eventi', ['agenda/selcal'], ['class' => 'nav-link'])?></li>


        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Dropdown</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="#" class="dropdown-item">Some action </a></li>
                <li><a href="#" class="dropdown-item">Some other action</a></li>
                <li><?=Html::a('Sign out', ['site/logout'], ['data-method' => 'post', 'class' => 'dropdown-item'])?></li>

                <li class="dropdown-divider"></li>

                <!-- Level two dropdown 
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Hover for action</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li>
                            <a tabindex="-1" href="#" class="dropdown-item">level 2</a>
                        </li>

                        <!-- Level three dropdown 
                        <li class="dropdown-submenu">
                            <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
                            <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">3rd level</a></li>
                                <li><a href="#" class="dropdown-item">3rd level</a></li>
                            </ul>
                        </li>
                        <!-- End Level three 

                        <li><a href="#" class="dropdown-item">level 2</a></li>
                        <li><a href="#" class="dropdown-item">level 2</a></li>
                    </ul>
                </li>
                <!-- End Level two -->
            </ul>
        </li>
    </ul>

    <!-- SEARCH FORM  
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    -->
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
       <!-- <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>-->
        <!-- Messages Dropdown Menu
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                     Message Start
                    <div class="media">
                        <img src="<?=$assetDir?>/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Call me whenever you can...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                      Message End
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                     Message Start
                    <div class="media">
                        <img src="<?=$assetDir?>/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                John Pierce
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">I got your message bro</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    Message End
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                     Message Start
                    <div class="media">
                        <img src="<?=$assetDir?>/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                  Message End -
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
</li>-->
        <!-- Notifications Dropdown Menu -->
     <!--   <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 444 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>https: //wa.me/1XXXXXXXXXX
<i class="fa-brands fa-whatsapp"></i>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>-->
     <li class="nav-item">
      
 
</li>
    <li class="nav-item">
          </li>
    
    </li>

        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" data-enable-remember="true">
                <i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <?=Html::a('<i class="fas fa-sign-out-alt"></i>', ['/site/logout'],
    ['data-method' => 'post', 'class' => 'nav-link'])?>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
      <!--  <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>-->
         <li class="nav-item">
<a class="nav-link" onclick="dm();" href="javascript:void(0);">DM</a>
        </li>

    <!--               <li class="nav-item">
<a class="nav-link">UA:<?php echo date_format(date_create($ris['lastlogin']),"d/m/Y H:i") ?></a>
        </li>-->
    </ul>
</nav>
<!-- /.navbar -->