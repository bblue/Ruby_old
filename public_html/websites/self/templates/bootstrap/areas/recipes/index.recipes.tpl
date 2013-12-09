

    <!-- Le styles -->
    <link href="/websites/self/templates/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/websites/self/templates/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <style>

    /* GLOBAL STYLES
-------------------------------------------------- */
    /* Padding below the footer and lighter body text */

    body {
      color: #5a5a5a;
    }






    /* CUSTOMIZE THE CAROUSEL
-------------------------------------------------- */

    /* Carousel base class */
    .carousel {
      margin-bottom: 60px;
    }

    .carousel .container {
      position: relative;
      z-index: 9;
    }

    .carousel-control {
      height: 80px;
      margin-top: 0;
      font-size: 120px;
      text-shadow: 0 1px 1px rgba(0,0,0,.4);
      background-color: transparent;
      border: 0;
      z-index: 10;
    }

    .carousel .item {
      height: 500px;
    }
    .carousel img {
      position: absolute;
      top: 0;
      left: 0;
      min-width: 100%;
      height: 500px;
    }

    .carousel-caption {
      background-color: transparent;
      position: static;
      max-width: 550px;
      padding: 0 20px;
      margin-top: 200px;
    }
    .carousel-caption h1,
    .carousel-caption .lead {
      margin: 0;
      line-height: 1.25;
      color: #fff;
      text-shadow: 0 1px 1px rgba(0,0,0,.4);
    }
    .carousel-caption .btn {
      margin-top: 10px;
    }



    /* MARKETING CONTENT
-------------------------------------------------- */

    /* Center align the text within the three columns below the carousel */
    .marketing .span4 {
      text-align: center;
    }
    .marketing h2 {
      font-weight: normal;
    }
    .marketing .span4 p {
      margin-left: 10px;
      margin-right: 10px;
    }

    /* RESPONSIVE CSS
    -------------------------------------------------- */

    @media (max-width: 979px) {

      .carousel .item {
        height: 500px;
        
      }
      .carousel img {
        width: auto;
        height: 500px;
      }
    }



/* CUSTOM */
.ruby-nav-dropdown-padding {
	padding: 5px;
}


     /* Sticky footer styles
-------------------------------------------------- */
      html,
      body {
        height: 100%;
        /* The html and body elements cannot have any padding or margin. */
      }

      /* Wrapper for page content to push down footer */
      #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0 auto -60px;
      }

      /* Set the fixed height of the footer here */
      #push,
      #footer {
        height: 60px;
      }
      #footer {
        background-color: #f5f5f5;
      }
	.footer-internal-wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0 auto -7px;
	}
	#footer-internal-push {
		height: 7px;
	}

	/* Footer bottom borderline */
	#footer-border-bottom {
		background: url(/websites/self/templates/bootstrap/img/footer/footer-border-bottom.png) repeat-x;
		height: 7px;
		position: relative;
	}

      /* Lastly, apply responsive CSS fixes as necessary */
      @media (max-width: 767px) {
        #footer {
          margin-left: -20px;
          margin-right: -20px;
          padding-left: 20px;
          padding-right: 20px;
        }
      }

     /*Nav search dropdown
-------------------------------------------------- */
    .bond {
      text-align: center;
    }
    .bond img {
      max-height: 50px;
    }
.typeahead_wrapper { display: block; height: 30px; }
.typeahead_photo { float: left; max-width: 30px; max-height: 30px; margin-right: 5px; }
.typeahead_labels { float: left; height: 30px; }
.typeahead_primary { font-weight: bold; }
.typeahead_secondary { font-size: .8em; margin-top: -5px; }
    </style>

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/websites/self/templates/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/websites/self/templates/bootstrap/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/websites/self/templates/bootstrap/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="/websites/self/templates/bootstrap/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="/websites/self/templates/bootstrap/ico/favicon.png">
  </head>

  <body>

 <div id="wrap">
    <!-- NAVBAR
================================================== -->    
      <!-- Fixed navbar -->
      <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
          <div class="container">
          	<!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="brand" href="#">{M1_SERVER_HOST_NAME}</a>
			<form class="navbar-search pull-right">
				<input type="text" id="user-input" class="search-query typeahead" placeholder="Search" autocomplete="off" data-provide="typeahead">
			<input type="hidden" class="span1" name="bondId" id="bondId" value="" />
			</form>
			<ul class="nav pull-right">
            <!-- IF USR_IS_GUEST -->
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-off"></i> Sign In</a>
				<ul class="dropdown-menu">				
					<li>
						<form class="ruby-nav-dropdown-padding">
							<div class="control-group">
								<div class="controls">
									<input type="text" id="inputEmail" placeholder="Email">
								</div>
							</div>
							<div class="control-group error" id="inputPassword-control-group" data-placement="right" data-toggle="tooltip"  title='{VAL_PASSWORD_VALIDATION_PATTERN}YTEST'>
								<div class="controls">
									<input type="password" id="inputPassword" placeholder="Password" required="required" pattern='{VAL_PASSWORD_VALIDATION_PATTERN}YTEST'>
									<span class="help-inline">Something may have gone wrong</span>
								</div>
							</div>
							<div class="control-group pull-right">
								<div class="controls">
									<button type="submit" class="btn btn-success" style="width: 100%;">Sign in</button>
								</div>
							</div>
						</form>
					</li>
				</ul>            		
			</li>
			<!-- IF M1_CONFIG_ISHOWREGISTRATIONLINK == 1 -->
			<li><a href=""><i class="icon-pencil"></i> Register</a></li>
			<!-- ENDIF -->
			<!-- ELSEIF USR_IS_LOGGED_IN -->
              	<li class="dropdown">
              		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{USR_FIRSTNAME} <b class="caret"></b></a>
              		<ul class="dropdown-menu">						
						<li><a href="#"><i class="icon-star"></i> Favourites</a></li> 
						<li><a href="#"><i class="icon-book"></i> My recipes</a></li>
						<li><a href=""><i class="icon-comment"></i> Messages <span class="badge badge-important">6</span></a></li>
						<li class="divider"></li> 
						<li><a href="#"><i class="icon-user"></i> Configure user</a></li>
						<li><a href="#"><i class="icon-lock"></i> Change password</a></li>
						<li class="divider"></li>
						<li><a href=""><i class="icon-off"></i> Sign Out</a>     			
              		</ul>
              	<!-- ENDIF -->
              	</ul>
            <div class="nav-collapse collapse">
              <ul class="nav">
                <li class="active"><a href="#"><i class="icon-home"></i> Home</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-th-list"></i> Oppskrifter <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Kategori 1</a></li>
                    <li><a href="#">Kategori 2</a></li>
                    <li><a href="#">Kategori 3</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Vis alle oppskrifter</a></li>
                  </ul>
                </li> 
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-th-large"></i>Nyttig <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                  	<li class="nav-header">Oppslag</li>
                    <li><a href="#">Steketemperatur</a></li>
                    <li><a href="#">Lenker</a></li>
                    <li><a href="#">Ingredienser</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Verktøy</li>
                    <li><a href="#">Sorte gryte</a></li>
                  </ul>
                </li>
              </ul>
              <ul class="nav pull-right">
    			<!-- IF USR_IS_LOGGED_IN -->
    			<li class="dropdown">
    				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-wrench"></i><b class="caret"></b></a>          
					<ul class="dropdown-menu">
						<li><a href="#"><i class="icon-home"></i> Dashboard</a></li>
						<li class="divider"></li>
						<li class="nav-header">Recipe database</li>
						<li><a href="#"><i class="icon-pencil"></i> Add Recipe</a></li>
						<li><a href="#"><i class="icon-pencil"></i> Add Ingredient</a></li>
						<li><a href="#"><i class="icon-pencil"></i> Add oppslag</a></li>
						<li class="divider"></li>
 						<li class="nav-header">Users</li>
						<li><a href="#"><i class="icon-share"></i> Send invitation</a></li>
						<li><a href="#"><i class="icon-edit"></i> Administrate</a></li>
						<li><a href="#"><i class="icon-th-large"></i> Usergroups</a></li>
						<li><a href="#"><i class="icon-eye-close"></i> Permissions</a></li>
						<li class="divider"></li>
						<li class="nav-header">Server</li>
						<li><a href="#"><i class="icon-cog"></i> Configuration</a></li>
						<li><a href="#"><i class="icon-eye-open"></i> Search engines</a></li>
						<li><a href="#"><i class="icon-download"></i> Database backup</a></li>
						<li class="divider"></li>
						<li class="nav-header">Development</li>
						<li><a href="#"><i class="icon-upload"></i> Add to database</a></li>
						<li><a href="#"><i class="icon-qrcode"></i> Test commands</a></li>
					</ul>
				</li>
				<li class="divider-vertical"></li>
	            <!-- ENDIF -->
              </ul>
            </div><!--/.nav-collapse -->
          </div><!-- /.container -->
        </div><!-- /.navbar-inner -->
      </div><!-- /.navbar -->
      
    <!-- Carousel
================================================== -->
    <div id="myCarousel" class="carousel slide">
      <div class="carousel-inner">
        <div class="item active">
          <img src="websites/self/templates/bootstrap/img/examples/bird_msn.jpg" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Example headlin e.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <a class="btn btn-large btn-primary" href="#">Sign up today</a>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="websites/self/templates/bootstrap/img/examples/slide-02.jpg" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Another example headline.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
			<div>			<form class="form-search">
								<div class="input-append">
									<input type="text" class="span2 search-query">
									<button type="submit" class="btn">Search</button>
								</div>
							</form>
			</div>
              <a class="btn btn-large btn-primary" href="#">Learn more</a>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="/websites/self/templates/bootstrap/img/examples/slide-03.jpg" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <a class="btn btn-large btn-primary" href="#">Browse gallery</a>
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div><!-- /.carousel -->



    <!-- Marketing messaging and featurettes
================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->
<a href="#" data-toggle="tooltip" title="first tooltip">hover over me</a>
		<div class="container marketing">
			<div class="row">
				<div class="span4">
					<img class="img-circle" data-src="holder.js/140x140">
						<h2>Heading</h2>
						<p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.</p>
						<p>   
							<form class="form-search">
								<div class="input-append">
									<input type="text" class="span2 search-query">
									<button type="submit" class="btn">Search</button>
								</div>
							</form>
				    	</p>
						<p><a class="btn" href="#">View details &raquo;</a></p>
				</div><!-- /.span4 -->
			</div><!-- /.row -->
		</div><!-- /.container -->
		
		<div id="push"></div>
    </div><!-- /#wrap -->
    
      <!-- FOOTER -->
    <div id="footer">
      <div class="container footer-internal-wrap">
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>&copy; 2013 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
        <div id="footer-internal-push"></div>
      </div><!-- /.container -->
	<div id="footer-border-bottom"></div>
    </div><!-- /#footer -->


    <!-- Le javascript
================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="/websites/self/templates/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="/websites/self/templates/bootstrap/js/tooltips.js"></script>
<script src="//underscorejs.org/underscore-min.js"></script>
    <script>
      !function ($) {
        $(function(){
          // carousel demo
          $('#myCarousel').carousel();
        });
      }(window.jQuery);
    </script>
   <script type="text/javascript">
		$(function(){

			var bondObjs = {};
			var bondNames = [];

			//get the data to populate the typeahead (plus an id value)
			var throttledRequest = _.debounce(function(query, process){
				//get the data to populate the typeahead (plus an id value)
				$.ajax({
					url: 'bonds.json'
					,cache: false
					,success: function(data){
						//reset these containers every time the user searches
						//because we're potentially getting entirely different results from the api
						bondObjs = {};
						bondNames = [];

						//Using underscore.js for a functional approach at looping over the returned data.
						_.each( data, function(item, ix, list){

							//for each iteration of this loop the "item" argument contains
							//1 bond object from the array in our json, such as:
							// { "id":7, "name":"Pierce Brosnan" }

							//add the label to the display array
							bondNames.push( item.name );

							//also store a hashmap so that when bootstrap gives us the selected
							//name we can map that back to an id value
							bondObjs[ item.name ] = item;
						});

						//send the array of results to bootstrap for display
						process( bondNames );
					}
				});
			}, 300);


			$(".typeahead").typeahead({
				source: function ( query, process ) {

					//here we pass the query (search) and process callback arguments to the throttled function
					throttledRequest( query, process );
						}
				        ,highlighter: function( item ){
				          var bond = bondObjs[ item ];
				          
				          return '<div class="bond">'
				                +'<img src="' + bond.photo + '" />'
				                +'<strong>' + bond.name + '</strong><br />'
				                +'<span>' + bond.films+ '</span>'
				                +'</div>';
				        }
								, updater: function ( selectedName ) {
				          
				          //note that the "selectedName" has nothing to do with the markup provided
				          //by the highlighter function. It corresponds to the array of names
				          //that we sent from the source function.

					//save the id value into the hidden field
					$( "#bondId" ).val( bondObjs[ selectedName ].id );

					//return the string you want to go into the textbox (the name)
					return selectedName;
				}
			});
		});
	</script>
  </body>
</html>