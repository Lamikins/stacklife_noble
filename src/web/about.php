<?php
  require_once(__DIR__ .  '/../../etc/sl_ini.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StackLife @ NOBLE | About</title>

<?php
include_once('includes/includes.php');
echo <<<EOF
  <script type="text/javascript" src="$www_root/js/jquery.fitvids.js"></script>
EOF;

?>
<script>
  $(document).ready(function(){
    $(".video").fitVids();
  });
</script>
</head>

<body>

    <div class="container group">
    	<div class="row group">
			<div class="span2 middle-position">
			 	<?php require_once('includes/logo.php');?>
			</div>
			<!--<div class="span4 offset1 about-button">
				<a href="<?php echo $www_root ?>/" class="about home">Home</a>
			</div>-->
	
			<div class="span4 offset6">
				<?php require_once('includes/searchbox.php');?>  
			</div> 
		</div>
		
		
		<div class="row group">
			<div class="span5 offset2 text-padding">		
				<div class="call-out">
					<p class="black">StackLife lets you browse all of the items in NOBLE's 28 libraries as if they were on a single shelf.</p>
				
					<p class="black">It’s built on a few core ideas:</p>
				
					<p class="indent"><span class="dark">1. Every book has a context</span></br> StackLife shows you that context as a stack of neighboring books</p>
				
					<p class="indent"><span class="dark">2. Every book has many contexts</span></br>
					StackLife lets you switch contexts just by clicking. The default stack shows the item in its primary subject classification. But since most works have been classified under more than one subject heading, you can click on any of those headings and see it grouped with those neighbors.</p>
				</div><!--end call-out-->
			</div>
			
			 <div class="span5 video text-padding">		
			<!--	<iframe src="http://player.vimeo.com/video/55894472?title=0&amp;byline=0&amp;portrait=0" width="500" height="334" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> -->
				<div class="call-out">
                <!-- <p class="indent"><span class="dark">3. Every book has some community relevance</span></br> 
					StackLife heat-maps books to reflect how often they’ve been checked out, put on reserve, called back early from a loan, etc. Also, it’s not just books. StackLife lets you browse among all of Hollis’ catalog, including DVDs and CDs. </p> -->
					<p class="indent"><span class="dark">3. Every book is visual</span></br> 
					StackLife visualizes library materials as a virtual stack, bringing serendipity back into library searches.  </p>

					<br/>
					
					<div class="">
						<p style="text-align:center">Developed at <a href="http://www.andover.edu/Pages/default.aspx">Phillips Academy Andover</a></p>
						<img class="center" src="images/pa_logo.png" style="width:45%">
					</div>
					<br>
					<div class="about-button">
						<a href="<?php echo $www_root ?>/explainer.php" class="heading">How it works</a>
					</div>
					<br/>
					<div class="about-button">
						<a href="<?php echo $www_root ?>/privacy" class="heading">Read our Privacy Policy</a>
					</div>
					<br/>
					<div class="about-button">
						<a target="_blank" href="https://github.com/Lamikins/stacklife_noble" class="heading">Download on GitHub</a>
					</div>
				</div><!--end call-out-->
			</div>
			
		</div><!--end row-->
    	<br/><br/><br/>
	</div><!--end container-->


</body>
</html>
