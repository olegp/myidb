<html>
<head>
<title>International Times Archive</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
img {  border-style: none; }
.default { font-family: Times New Roman, Times, serif; font-size: 12pt; color: #000000; text-decoration: none; background-color: #ffffff}
.title { font-size: 48px; font-weight: bold; }
-->
</style>
</head>
<body bgcolor=gray class="default">
<a href="." style="padding: 10px; float: left">
<img src="it.png" alt="International Times" />
</a>

<div class="title" style="width: 700px; text-align: center">International Times<br/>Archive</div>
<br>

<?php
Include("config.php");

function MakeThumbnail($imagename, $thumbname)
{
  global $thumbsize;

  if(is_readable($thumbname) == false && function_exists("ImageCreateFromPNG")) {
    $size = GetImageSize($imagename);
    $srcimage = ImageCreateFromPNG($imagename);

    $ratio = $size[0]/$size[1];
    $dstw = $thumbsize * $ratio;
    $dsth = $thumbsize;

    $dstimage = ImageCreateTrueColor($dstw, $dsth);


    ImageCopyResized($dstimage, $srcimage, 0, 0, 0, 0, $dstw, $dsth, $size[0], $size[1]);

    ImagePNG($dstimage, $thumbname);
  }
}

function GetArchive() {
  global $imagedir, $thumbdir;
  $volumes = array();
  $handle = opendir($imagedir);

  while(false != ($file = readdir($handle))) {
    if($file != "." && $file != "..") {
      // extract the name sans extension
      $filebase = basename($file);

      //TODO separate into a function and use regex
      $a = strpos($filebase, "_") + 1;
      $b = strpos($filebase, "_", $a) + 1;
      $c = strpos($filebase, "-", $b) + 1;
      $d = strpos($filebase, "_", $c) + 1;
      $e = strpos($filebase, "-", $d) + 1;
      $f = strpos($filebase, "_", $e) + 1;
      $g = strpos($filebase, ".", $f);

      $date = substr($filebase, $a, $b - $a - 1);
      $volume = substr($filebase, $c, $d - $c - 1);
      $issue = substr($filebase, $e, $f - $e - 1);
      $page = substr($filebase, $f, $g - $f);
	
      if(!array_key_exists($volume, $volumes)) {
	$volumes[$volume] = array();
        	
      }
        
      $issues = &$volumes[$volume];
      
      if(!array_key_exists($issue, $issues)) {
        $issues[$issue] = array("date" => $date);
      }

      $pages = &$issues[$issue];
      $pages[$page] = $filebase;
     }
   }

   //printf("%d <br>", sizeof($volumes));

   ksort($volumes);
   foreach($volumes as $volume => $issues) {      
        ksort($issues);
	foreach($issues as $issue => $pages) {
		printf("<div style=\"clear: both\">");
		printf("<b>%s Volume: %s Issue: %s</b><br/>\n", $pages["date"], $volume, $issue);
                ksort($pages);
		foreach($pages as $page => $file) {
		    if(strcmp($page, "date")) {

                    MakeThumbnail($imagedir . $file, $thumbdir . $file);


		    printf("<a href=\"%s\"><img src=\"%s\" height=\"100\" style=\"margin: 5px\"/></a>", $imagedir . $file, $thumbdir . $file);
		}

		printf("</div>");
		printf("<hr/>");
	}
	
  }
  

  closedir($handle);
 
  return $volumes;
}

?>
<div style="margin: 10px">
<?php GetArchive(); ?>
</div>

</body>
</html>

