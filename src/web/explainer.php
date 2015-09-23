<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
  require_once (__DIR__ .  '/../../etc/sl_ini.php');
  include_once('includes/item.inc.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<?php
include_once('includes/includes.php');
echo <<<EOF
  <link rel="stylesheet" href="$www_root/css/jquery-ui-1.7.1.custom.css" type="text/css" />
	<script type="text/javascript" src="$www_root/js/explainer.js"></script>
	<script type="text/javascript" src="$www_root/js/jquery.history.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<!--[if IE]>
		<link rel="stylesheet" href="$www_root/stackview/ie.stackview.css" type="text/css" />
		<link rel="stylesheet" href="$www_root/css/ie.template.css" type="text/css" />
  	<![endif]-->
EOF;
?>

<script type="text/javascript">
var worldcatnum = '',
loc_call_num_sort_order = '';
anchor_subject = '',
uniform_id = '',
uniform_count = '',
uid = '2241578',
title = '',
stackheight = $(window).height();

var slurl = '<?php echo $www_root ?>/src/web/sl_funcs.php';
var www_root = '<?php echo $www_root ?>';

var recentlyviewed = '';
var alsoviewed = new Array();

var GBSArray = ['ISBN:<?php echo $isbn_trim ?>'];

google.load("books", "0");

(function(window,undefined){
  var History = window.History;
})(window);

$(document).ready(function() {
	<?php
	foreach(array_reverse($_SESSION['books']) as $id => $past_book){
		if($id != $uid) {
	?>
		recentlyviewed += ('&recently[]=<?php echo $id ?>');
		alsoviewed.push('<?php echo $id ?>');
	<?php }
	} ?>
}); //End document ready
</script>


<style>
.post-it {
	position:absolute;
	width:160px;
	padding:0 10px 10px;
	background:#FCF0AD;
	background:rgba(252, 240, 173, .95);
	background:#FCF0AD;
	z-index:9999;
	font-family:courier;
	color:#444;
	font-size:14px;
	line-height:21px;
   -moz-box-shadow: 0 0 8px #888;
   -webkit-box-shadow: 0 0 8px #888;
	box-shadow: 0px 0px 8px #888;
		color:#444;

}

.post-it p {
	margin-top:10px;
	font-size:14px;
	line-height:20px;
	font-family:courier;
	
}

.note1, .note2, .note3, .note4, .note4a, .note5, .note6, .note7 {
	-webkit-transition: all 150ms ease-in-out;
-moz-transition: all 150ms ease-in-out; 
-o-transition: all 150ms ease-in-out; 
-ms-transition: all 150ms ease-in-out; 
	color:#444;
}

.note1 {
	margin:40px 0 0 -15px;
	width:120px;
		-webkit-transform: rotate(2deg); 
	-moz-transform: rotate(2deg); 
	-o-transform: rotate(2deg);
	-ms-transform: rotate(2deg); 
}

.note2 {
	margin:100px 0 0 -15px;
	width:120px;
		-webkit-transform: rotate(1deg); 
	-moz-transform: rotate(1deg); 
	-o-transform: rotate(1deg);
	-ms-transform: rotate(1deg); 
}

.note3 {
	margin:275px 0 0 250px;
	-webkit-transform: rotate(-1deg); 
	-moz-transform: rotate(-1deg); 
	-o-transform: rotate(-1deg);
	-ms-transform: rotate(-1deg); 
}

.note4 {
	margin:450px 0 0 250px;
		-webkit-transform: rotate(-1deg); 
	-moz-transform: rotate(-1deg); 
	-o-transform: rotate(-1deg);
	-ms-transform: rotate(-1deg); 
}

.note4a {
	margin:127px 0 0 58px;
	-webkit-transform: rotate(-1deg); 
	-moz-transform: rotate(-1deg); 
	-o-transform: rotate(-1deg);
	-ms-transform: rotate(-1deg); 
}

.note5 {
	margin:-110px 0 0 170px;
	-webkit-transform: rotate(-1deg); 
	-moz-transform: rotate(-1deg); 
	-o-transform: rotate(-1deg);
	-ms-transform: rotate(-1deg); 
}

.note6 {
	margin:-65px 0 0 130px;
		-webkit-transform: rotate(2deg); 
	-moz-transform: rotate(2deg); 
	-o-transform: rotate(2deg);
	-ms-transform: rotate(2deg); 
}

.note7 {
	margin:50px 0 0 80px;
		-webkit-transform: rotate(1deg); 
	-moz-transform: rotate(1deg); 
	-o-transform: rotate(1deg);
	-ms-transform: rotate(1deg); 
}

.note1:hover, .note2:hover, .note3:hover, .note4:hover, .note4a:hover, .note5:hover, .note6:hover, .note7:hover {
	-webkit-transform: rotate(0deg); 
	-moz-transform: rotate(0deg); 
	-o-transform: rotate(0deg);
	-ms-transform: rotate(0deg); 
}
</style>

</head>

<!-- /////////////////// BODY ////////////////////////// -->
<body>
  	<div class="container group row">
				
		<div style="display:none;">
			<div id="viewerCanvas" style="width: 610px; height: 725px"></div>
		</div> <!--end hidden viewerCanvas-->


		<div id="contextData" class="group span2">
		
			<?php require_once('includes/logo.php');?>
		
        	<div id="overlay-buttons">
          		<div id="shelves-panel"></div>
          		<div id="tagGraph"></div>
          		
    		</div><!--end overlay-buttons-->
    		
        	<form id="book-tags-form" method="post">
      			<input type="text" id="bookTags" name="bookTags" class="required" onfocus="if (this.value=='tag it') this.value = ''" type="text" value="tag it"/>
            	
            	<input type="submit" name="submit_tags"  id="submit_tags" value="Go!"/>
        	</form>
        		
        	<div class="book-tag-success"><p><span style="display:none;"></span></p></div>
        	
    		
      	</div><!-- end contextData -->


      	<div class="main span8">
      		<!-- <div class="post-it note3">
      			<p>Depth of the color blue indicates amount of use by the Harvard community since 2002</p>
      			
      			<p>We call this &quot;StackScore&quot;</p>
      		</div>-->
      		
      		<div class="post-it note4">
      			<p>Thickness of the book is based on page count, length indicates the actual length</p>
      		</div>
      		<div class="post-it note4a">
      			<p>Navigate the stack by clicking arrows, scrolling or swiping</p>
      		</div>
      	<!-- -->
			<div id="fixedstack"></div>
			
      	</div><!-- end main-->
		
		<div class="span4-negative offset6">
			<?php require_once('includes/searchbox.php');?>
			<div id="item-panel" class="itemData-container"></div>   
		</div> 
                
      <div id="contextData" class="group">
        <div id="overlay-buttons">
          <div id="shelves-panel"></div>
  
          <div id="tagGraph"></div>
        </div><!--end overlay-buttons-->
          
       
        
      <div id="fixedclear"></div>
    </div> <!--end container-content-->
    
  </div><!--end container-->


	<!-- //////////begin templates//////////////////// -->
	

  <script id="gbscript" type="text/javascript" src="http://books.google.com/books?jscmd=viewapi&bibkeys=ISBN:<?php echo $isbn_trim ?>&callback=ProcessGBSBookInfo"></script>
  </div> <!--end wrapper-->
   
  <script id="item-template" type="text/x-handlebars-template">
  <div id="itemData">
    <h1 class="home-stack">
      {{title}}{{#if sub_title}} : {{sub_title}}{{/if}}
    </h1>
    <div id="creator_container">
    {{{creators}}}
    </div>
    <img class="cover-image" src="http://covers.openlibrary.org/b/isbn/{{isbn}}-M.jpg" />
    	<div class="post-it note5">
      		<p>Go to the item&apos;s entry in Noblenet, or find it in Google Books or Amazon</p>
      	</div>
    <ul class="access">
      {{#if url}}
      <li><a href="{{url}}">Online Access</a></li>
      {{/if}}
      <li class="button-google-disabled"><a class="button-google-disabled" href="#viewerCanvas"><img src="<?php echo $www_root ?>/images/gbs_preview_disabled.png" /></a></li>
      <li class="button-google"><a id="gviewer" class="button-google" href="#viewerCanvas" style="display:none;"><img src="<?php echo $www_root ?>/images/gbs_preview.png" border="0" /></a></li>
      <li><a id="amzn" href="http://www.amazon.com/dp/{{isbn}}" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/amazon.png" alt="Amazon"/></a></li>
      <li><a href="http://evergreen.noblenet.org/eg/opac/record/{{id}}" target="_blank"><img src="<?php echo $www_root ?>/images/noble_small.jpg" alt="NobleNet" height="42"/></a></li>
      {{#if wp_url}}          	
       <li><a href="{{wp_url}}" target="_blank" ><img src="<?php echo $www_root ?>/images/wikipedia.png" /></a></li>
      {{/if}}
  
     
    </ul>

		<!-- <div id="availability-panel"></div>
	    <div class="post-it note6">
      		<p>Check availability across libraries in NOBLE</p>
      	</div>	-->    	
    <h3 class="imprint">{{#if pub_location}}{{pub_location}}{{/if}}{{#if publisher}}, {{publisher}}{{/if}}{{#if pub_date}}, {{pub_date}}{{/if}}</h3>
    
    <h3 class="clickable advanced-data slide-more">Advanced Bibliographic Data<span class="arrow"></span></h3>
    
    <div class="advanced-data-box slide-content" style="display:none;">
      <ul>
        <li class="advanced-isbn"><p>ISBN: {{isbn}}</p></li>
        <li class="advanced-oclc"><p>OCLC: {{oclc}}</p></li>
        <li class="advanced-language"><p>Language: {{language}}</p></li>
      </ul>
    </div>
    
    <!--
    	    <div class="post-it note7">
      		    <p>StackScore represents community usage, 1 - 100</p>
      	    </div>
    -->
    <h3 class="clickable slide-more toc-title">Table of Contents<span class="arrow"></span></h3>
    <div class="slide-content" style="display:none;">
      <div id="toc"></div>
    </div>
    </div>
    
    <!--
    <div id="all-rank">	            
    <div id="shelfRankCalc" class="button-shelfRank">
      <span class="unpack">StackScore</span>
      <span class="shelfRank">{{shelfrank}}</span>
    </div><!--end shelfRankCalc-->
  </div><!--end all-rank-->
  
 <!-- <div id="rank-math" class="slide-content" style="display:none;">
    <ul>
      <li><p>Faculty checkouts: {{score_checkouts_fac}}</p></li>
      <li><p>Undergrad checkouts: {{score_checkouts_undergrad}}</p></li>
      <li><p>Graduate checkouts: {{score_checkouts_grad}}</p></li>
      <li><p>Holding libraries: {{score_holding_libs}}</p></li>
    </ul>
  </div>end rank-math-->

  </script> 
  
  <script id="availability-template" type="text/x-handlebars-template">
    <span class="button-availability {{#if any_available}}available-button{{else}}not-available-button{{/if}} slide-more"><span class="icon"></span>Availability<span class="arrow"></span></span>
		<div id="availability" class="slide-content availibility-slide-content" style="display:none;">
		  <ul>
		  {{#items}}
		    <li class="{{#if available}}available{{else}}not-available{{/if}}">
		      <span class="callno">{{library}} [{{call_num}}]</span>
		      {{#if depository}}<a class="small-button" href="{{request}}">REQUEST</a>{{else}}
		      {{#if available}}<span class="small-button sms">SMS</span>{{else}}
		      {{#if request}}<a class="small-button" href="{{request}}">REQUEST</a>{{/if}}{{/if}}{{/if}}<br />
		      {{status}}
		    </li>
		  {{/items}}
		  </ul>
		</div>
	</script>
	<script id="shelves-template" type="text/x-handlebars-template">
	  <ul>
	  	{{#if loc_call_num_sort_order}}
			<li id="callview" class="button stack-button"><span class="reload">Infinite Stack</span></li>
			{{else}}
			<li id="callview" class="button-disabled">No Call Number Stack</li>
			{{/if}}
		</ul>
		 <br/>
    <span class="heading">Subject Stacks</span>
    	<div class="post-it note1">
      		<p>Click to stack items by subject area</p>
    	</div>
    <ul>
      {{#if ut_count}}
			<li id="uniform" class="button stack-button"><span class="reload">All editions</span></li>
			{{/if}}
			{{#lcsh}}
			<li class="subject-button"><span class="reload">{{this}}</span></li>
			{{/lcsh}}
    </ul>
    <br/>
    <span class="heading">Community Stacks</span>
    	<div class="post-it note2">
      		<p>Add your own label (tags can include spaces)</p>
    	</div>
    <ul>
      {{! <li id="alsoviewed" class="button stack-button"><span class="reload">People who viewed this also viewed these</span></li> }}
      <li id="recentlyviewed" class="button stack-button"><span class="reload">Recently Viewed</span></li>
    </ul>
    {{#if wp_categories}}
    <span class="heading">Wikipedia Stacks</span>
      <ul>
        {{#wp_categories}}
        <li class="wp_category-button"><span class="reload">{{this}}</span></li>
        {{/wp_categories}}
      </ul>
    {{/if}}
	</script>
</body>
</html>
