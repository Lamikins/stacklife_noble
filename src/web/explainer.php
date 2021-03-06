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
uid = '2603632',
title = '',
stackheight = $(window).height();

var slurl = '<?php echo $www_root ?>/src/web/sl_funcs.php';
var www_root = '<?php echo $www_root ?>';

var recentlyviewed = '';
var alsoviewed = new Array();

var current_school = '<?php echo $_SESSION["school"]?>';    
    
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
	margin:80px 0 0 58px;
	-webkit-transform: rotate(-1deg); 
	-moz-transform: rotate(-1deg); 
	-o-transform: rotate(-1deg);
	-ms-transform: rotate(-1deg); 
}

.note5 {
	margin:370px 0 0 -50px;
	-webkit-transform: rotate(2deg); 
	-moz-transform: rotate(2deg); 
	-o-transform: rotate(2deg);
	-ms-transform: rotate(2deg); 
}

.note6 {
	margin:190px 0 0 -70px;
		-webkit-transform: rotate(0deg); 
	-moz-transform: rotate(0deg); 
	-o-transform: rotate(0deg);
	-ms-transform: rotate(0deg); 
}

.note7 {
	margin:350px 0 0 750px;
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
           
           <br>
            <!--- Change School Panel -->   
            <h3 class="clickable slide-more school-selector">Library: <?php echo $_SESSION["school"]?><span class="arrow"></span></h3>
            <div class="slide-content" style="display:none;">
             
              <div id="school">
                 
                  
              </div>
              
            </div>
            <br>
            
            <span class="heading">Infinite Stack</span>
            <br>
            
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

          <div class="post-it note7">
      			<p>You can check out this book on Google, Amazon, or NOBLE!</p>
      		</div>

      	<div class="main span8">
      		<div class="post-it note3">
      			<p>Depth of the color blue indicates amount of use by the NOBLE community.</p>
      			
      			<p>This is called &quot;StackScore&quot;</p>
      		</div>
      		
      		<div class="post-it note4">
      			<p>Thickness of the book is based on page count, length indicates the actual length</p>
      		</div>
      		
      		<div class="post-it note4a">
      			<p>Navigate the stack by clicking arrows, scrolling or swiping</p>
      		</div>
      		
      		<div class="post-it note5">
      			<p>Click on a subject heading to bring up that stack</p>
      		</div>
      		
      		<div class="post-it note6">
      			<p>If available, Infinite Stack lets you see other books physically located nearby!</p>
      		</div>

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


  <!--<script id="gbscript" type="text/javascript" src="http://books.google.com/books?jscmd=viewapi&bibkeys=OCLC:<?php echo $oclcnum ?>,ISBN:<?php echo $isbn_trim ?>&callback=ProcessGBSBookInfo"></script>-->
  
  <script id="gbscript" type="text/javascript" src="http://books.google.com/books?jscmd=viewapi&bibkeys=ISBN:0738531367&callback=ProcessGBSBookInfo"></script>
  </div> <!--end wrapper-->

  <script id="item-template" type="text/x-handlebars-template">
  <div id="itemData">
    <h1 class="home-stack">
      {{title}}{{#if sub_title}} {{sub_title}}{{/if}} {{#if electronic}}<img src="<?php echo $www_root ?>/images/lightning.png" alt="electronic resource" width="25"/>{{/if}}
    </h1>
    <div id="creator_container">
    {{{creators}}}
    </div>
    <img class="cover-image" src="http://covers.openlibrary.org/b/isbn/{{isbn}}-M.jpg" />
    <ul class="access">
      
      {{#if url}}
      <li><a href="{{url}}">Online Access</a></li>
      {{/if}}
      <li class="button-google-disabled"><a class="button-google-disabled" href="#viewerCanvas"><img src="<?php echo $www_root ?>/images/gbs_preview_disabled.png" /></a></li>
      <li class="button-google"><a id="gviewer" class="button-google" href="#viewerCanvas" style="display:none;"><img src="<?php echo $www_root ?>/images/gbs_preview.png" border="0" /></a></li>
      <li><a id="amzn" href="http://www.amazon.com/dp/{{isbn}}" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/amazon.png" alt="Amazon"/></a></li>
      {{#if noble_link}}<li><a href="{{noble_link}}" target="_blank"><img src="<?php echo $www_root ?>/images/noble_small.jpg" alt="NobleNet" height="42"/></a></li>{{/if}}
      {{#if wp_url}}
       <li><a href="{{wp_url}}" target="_blank" ><img src="<?php echo $www_root ?>/images/wikipedia.png" /></a></li>
      {{/if}}


    </ul>

{{#if npr_url}}
<ul style='clear:both;'>
<div>
<img src="<?php echo $www_root ?>/images/npr.png" alt="Smiley face" width=60>
      <audio controls>
<source src="{{npr_url}}" type="audio/mpeg">
Your browser does not support the audio element.
</audio>
</div>
</ul>
{{/if}}
		<div id="availability-panel"></div>

    <h3 class="imprint">{{#if pub_location}}{{pub_location}}{{/if}}{{#if publisher}} {{publisher}}{{/if}}{{#if pub_date}} {{pub_date}}{{/if}}</h3>
    
    

    <h3 class="clickable advanced-data slide-more">Advanced Bibliographic Data<span class="arrow"></span></h3>

    <div class="advanced-data-box slide-content" style="display:none;">
      <ul>
        <li class="advanced-isbn"><p>ISBN: {{isbn}}</p></li>
        {{#if loc_call_num}} <li class="advanced-isbn"><p>Call Number(s): {{loc_call_num}}</p></li>{{/if}}
        {{#if oclc}}<li class="advanced-oclc"><p>OCLC: {{oclc}}</p></li>{{/if}}
        <li class="advanced-language"><p>Language: {{language}}</p></li>
      </ul>
    </div>

    <h3 class="clickable slide-more toc-title">Table of Contents<span class="arrow"></span></h3>
    <div class="slide-content" style="display:none;">
      <div id="toc"></div>
    </div>
    
    
    <h3 class="clickable slide-more summary-title">Summary<span class="arrow"></span></h3>
    <div class="slide-content" style="display:none;">
      <div id="summary"></div>
    </div>
    
    </div>
    
    
    <div id="all-rank" class="slide-more">
    <div id="shelfRankCalc" class="button-shelfRank">
      <span class="unpack">StackScore</span>
      <span class="shelfRank">{{shelfrank}}</span>
    </div><!--end shelfRankCalc-->
  </div><!--end all-rank-->
  
  <div id="rank-math" class="slide-content" style="display:none;">
    <ul>
      <li><p>Circulation Count: {{circ_count}}</p></li>
    </ul>
  </div>

  </script>
  <script id="availability-template" type="text/x-handlebars-template">
    <span class="button-availability {{#if any_available}}available-button{{else}}not-available-button{{/if}} slide-more"><span class="icon"></span>Availability<span class="arrow"></span></span>
		<div id="availability" class="slide-content availibility-slide-content" style="display:none;">
		  <ul>
		  {{#items}}
		    <li class="{{#if available}}available{{else}}not-available{{/if}}">
		      <span class="callno">{{library}} [{{call_num}}]</span>
		      {{#if available}}<span class="small-button sms">SMS</span>{{/if}}<br />
		      {{status}}
		    </li>
		  {{/items}}
		  </ul>
		</div>
	</script>
	<script id="shelves-template" type="text/x-handlebars-template">
	  <ul>
      
        {{#if loc_call_num_match_school}}
            {{#if not_noble}}
			<li id="callview" class="button stack-button"><span class="reload">Infinite Stack</span></li>
			{{else}}
			<li id="callview" class="button-disabled">No Stack for this Library</li>
            {{/if}}
        {{else}}
            <li id="callview" class="button-disabled">No Call Number Stack</li>
        {{/if}}
        </ul>
		 <br/>
    <span class="heading">Subject Stacks</span>
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
