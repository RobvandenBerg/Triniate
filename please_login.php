<?php
include('include_this.php');

?>
<html>
<head>
<meta name='viewport' content='width=device-width'>
<title>Triniate - Log in</title>
<style>
body{padding: 0px; margin: 0px;}
#topscreen,#bottomscreen{width: 310px; background-color: black; color: white; padding: 5px;}
#bottomscreen{
font-size: 10px;
height: 202px;
background-image: url(http://triniate.com/bottomscreen.png);
}
#topscreen{
height: 208px;
font-size: 16px;
text-align: center;
}

.presents
{
position: relative;
top: 50px;
}
.titleimg{
position: relative;
top: 66px;
text-align: center;
}

@font-face {
font-family: trinigan;
src: url('Trinigan.ttf');
}

.paper{
position: relative;
top: 20px;
left: 45px;
color: black;
width: 220px;
height: 160px;
font-size: 12px;
}

.title{
text-align: center;
font-size: 14px;
font-weight: bold;
}

div.gameplay_image
{
text-align: center;
}
img.gameplay_image
{
border: 1px solid white;
}
.slogan
{
position: absolute;
top: 195px;
width: 310px;
text-align: right;
font-size: 12px;
}
</style>
<script>
window.onload = function(){document.body.scrollTop = 218;}
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35888116-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script></head>
<body>
<div id='topscreen'><span class='presents'>3DSPlaza presents...</span><br><img class='titleimg' src='http://triniate.com/project_triniate_title.png'>
<div class='slogan'>Where others see limits, we see challenges</div></div>
<div id='bottomscreen'>
<div class='paper'>
<div class='title'>Please log in</div>
<p>You have to log in to play Triniate.</p>
<p style='text-align: center; position: relative;'><input type='button' value='To login page' oNclick="window.location='<?php echo $triniate_playpage;?>';"></p>
</div>
</div>
</body>
</html>