<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $site_title; ?></title>
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic' rel='stylesheet' type='text/css'>
    <link href="css/flatly/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>


    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo $site_title; ?></a>
        </div>
  <?php if ( isLogged() ): ?>

        <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="index.php">Home</a></li>
          <li><a href="ranking.php">Rankings</a></li>
          <!-- <li><a href="#">Marketplace</a></li> -->
          <li><a href="battle.php">Battleground</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo getCharacterName($_SESSION['uid']); ?> <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="profile.php">View Profile</a></li>
              <li class="divider"></li>
              <li><a href="settings.php">Settings</a></li>
              <li class="divider"></li>
              <li><a href="logout.php">Log Out</a></li>
            </ul>
          </li>
        </ul>
        </div><!--/.navbar-collapse -->

  <?php else: ?>

        <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="index.php">Home</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>

        </ul>
        </div><!--/.navbar-collapse -->

  <?php endif ?>

      </div>
    </nav>

    <div id="headimg"></div>

    <div class="container">

    <!-- end global header -->