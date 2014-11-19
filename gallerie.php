<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SNACK</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

  </head>

  <body>

    <div class="container">
      <div class="header">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="index.php">About</a></li>
            <li role="documentation"><a href="documentation.php">Documentation</a></li>
            <li role="presentation" class="active"><a href="gallerie.php">Captures d'écrans</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">SNACK</h3>
      </div>
      <br />
      <hr>
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li>
        <li class="active" data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li class="" data-target="#carousel-example-generic" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item">
          <img data-holder-rendered="true" src="img/snack1_big.png" alt="Users">
        </div>
        <div class="item active">
          <img data-holder-rendered="true" src="img/snack2_big.png" alt="NAS">
        </div>
        <div class="item">
          <img data-holder-rendered="true" src="img/snack3_big.png" alt="Sessions">
        </div>
      </div>
      <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
     
      <hr>

      <div class="footer">
        <p>&copy; BH-Consulting 2014</p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/jquery-2.1.1.js"></script>
    <script src="bootstrap/js/carousel.js"></script>

    <script>
      !function ($) {
        $(function(){
          // carousel demo
          $('#myCarousel').carousel()
        })
      }(window.jQuery)
    </script>    
    <script src="bootstrap/js/holder/holder.js"></script>
  </body>
</html>
