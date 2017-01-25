<?php
session_start();
if(!isset($_SESSION['connected'])){
    header('Location: index.php');
    exit;
}

/**
 * @brief include libs
 */
require_once('config/config.php');
require_once('core/lib/xmlManager.php');
require_once('core/lib/dbManager.php');
require_once('core/lib/outils.php');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <!-- Bootstrap Styles-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FontAwesome Styles-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Morris Chart Styles-->
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
    <!-- Custom Styles-->
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
    <!-- Google Fonts-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <!-- TABLE STYLES-->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
</head>

<body>
    <div id="wrapper">
        <nav class="navbar navbar-default top-navbar" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href=""><img src="assets/img/logo.png" width="100%" style="margin-top: -5px"></a>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a><i class="fa fa-user fa-fw"></i> Welcome, <?php echo $_SESSION['connected'];?></a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="dash.php?a=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
                <!-- /.dropdown -->
            </ul>
        </nav>
        <!--/. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">

                    <li>
                        <a class="active-menu" ><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <?php
                    echo getMenuList();
                    ?>
                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">


                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">
                            Dashboard <small>FoxiAdmin <small>Crud Admin Generetor V0.1.1</small></small>
                        </h1>
                    </div>
                </div>
                <!-- /. ROW  -->
                <?php
                /** Logout */
                if(isset($_GET['a']) && 'logout' === $_GET['a']) {
                    session_unset();
                    redirectToUrl('index.php');
                }
                /** generate error messages */
                if(isset($_GET['a']) && 'error' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t'])){
                    echo getMessage($_GET['t']);
                }
                /** generate DataList */
                if (isset($_GET['a']) && 'list' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t'])){
                    if(notHaveAccess($_GET['a'], $_GET['t'])){
                        /** TODO find solution to fix >> Warning: Cannot modify header information - headers already sent by (output started...
                        header('Location: dash.php?t=405&a=error'); */
                        redirectMessage(405);
                    }
                    echo getListTable($db,$_GET['t']);
                }
                /** generate create object Form */
                if (isset($_GET['a']) && 'add' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t'])){
                    /** create object Form */
                    if(isset($_POST['table']) && !empty($_POST['table'])){
                        $params = getRequestParams($_POST);
                        $res = setDataTabel($db, $params['table'], $params['action'], $params['data']);
                        if($res){
                            echo getMessage(201);
                        }else{
                            echo getMessage(400);
                        }
                    }
                    echo getFormSetTable($db, $_GET['t'], 'add');

                }
                /** generate edit object Form */
                if (isset($_GET['a']) && 'edit' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t']) && isset($_GET['id']) && !empty($_GET['id'])){
                    /** create object Form */
                    if(isset($_POST['table']) && !empty($_POST['table'])){
                        $params = getRequestParams($_POST);
                        $res = setDataTabel($db, $params['table'], $params['action'], $params['data'],$_GET['id']);
                        if($res){
                            echo getMessage(202);
                        }else{
                            echo getMessage(400);
                        }
                    }else {
                        echo getFormSetTable($db, $_GET['t'], 'edit', $_GET['id']);
                    }
                }
                /** delete object Form */
                if (isset($_GET['a']) && 'delete' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t']) && isset($_GET['id']) && !empty($_GET['id'])){
                    if(deleteDataTable($db,$_GET['t'],$_GET['id'])){
                        echo getMessage(203);
                    }else{
                        echo getMessage(400);
                    }
                }
                /** delete object Form */
                if (isset($_GET['a']) && 'view' === $_GET['a'] && isset($_GET['t']) && !empty($_GET['t']) && isset($_GET['id']) && !empty($_GET['id'])) {
                    echo viewDataTable($db, $_GET['t'], $_GET['id']);
                }
                ?>





                <!-- /. ROW  -->
				<footer><p>&copy;2016 All right reserved. Developed by <a href="https://tn.linkedin.com/in/abdelazizjribi" target="_blank">Abdelaziz Jribi</a><br />
                        <span style="font-size:2px">Template by <a href="http://webthemez.com">WebThemez</a></span></p></footer>
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->
    <!-- JS Scripts-->
    <!-- jQuery Js -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Metis Menu Js -->
    <script src="assets/js/jquery.metisMenu.js"></script>
    <!-- Morris Chart Js -->
    <script src="assets/js/morris/raphael-2.1.0.min.js"></script>
    <script src="assets/js/morris/morris.js"></script>
    <!-- DATA TABLE SCRIPTS -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTables-example').dataTable();
        });
    </script>
    <!-- Custom Js -->
    <script src="assets/js/custom-scripts.js"></script>


</body>

</html>