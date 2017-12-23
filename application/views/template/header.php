<html>

<head>
  <?php 
    /* Variable initialization */
    $strFileName  = '';
    /* if exiting enviroment is not developement then do neeeful */
    if(ENVIRONMENT != 'development'){
      $strFileName  = '.mini';
    }
  ?>
  <title><?php echo (isset($moduleTitle)?$moduleTitle.' - ':'');?>Lead Management Open Source Framework</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>materialize<?php echo $strFileName?>.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>jquery.fancybox<?php echo $strFileName?>.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>style<?php echo $strFileName?>.css?v=1.0.0.0.1" />
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL.RESOURCE_CSS_PATH?>highcharts<?php echo $strFileName?>.css" />
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }

    main {
      flex: 1 0 auto;
    }

    body {
      background: #fff;
    }

    .input-field input[type=date]:focus + label,
    .input-field input[type=text]:focus + label,
    .input-field input[type=email]:focus + label,
    .input-field input[type=password]:focus + label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=email]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;
    }
  </style>
  <script language="javaScript">
  <!--
    var SITE_URL  = "<?php echo SITE_URL;?>";
	var DELIMITER  = "<?php echo DELIMITER;?>";
  -->
  </script>
</head>

<body>