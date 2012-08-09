<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML lang="en">
<HEAD>
<BODY BGCOLOR="#404040" LINK="#00ff00" ALINK="#ff00ff" VLINK="#00ffff" TEXT="#FFFFFF">
	<META http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<TITLE>File Editor</TITLE>
	<LINK rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ubuntu:300,500,700">
	<LINK rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans+Mono">
	<LINK rel="stylesheet" type="text/css" href="style.css">
<SCRIPT type="text/javascript">
function selectfile() {
	document.location.href="editor.php?file=" + document.getElementById("whichfile").value;
}
</SCRIPT>
</HEAD> 
<h1>File Editor</h1><hr>
<?php

error_reporting(0);
date_default_timezone_set('America/New_York');
session_start(); 

$supported_ext = array('php', 'txt', 'pl', 'html', 'shtml', 'js', 'css');
$path = getcwd();
$selected = '';

if (isset($_REQUEST['file'])) {
	$selected = $_REQUEST['file'];
}

if ( isset($_REQUEST['create']) && $_REQUEST['create'] == 'yes') {
	if (isset($_REQUEST['newfile']) && $_REQUEST['newfile'] != '') {
		$file = $_REQUEST['newfile'];
        $splitted = explode('.', $file);
        $ext = strtolower($splitted[count($splitted)-1]);	
        if (in_array($ext, $supported_ext)) { 			
			fopen($_REQUEST['newfile'], 'w') or die("Can't create file.");
			fclose($_REQUEST['newfile']);
			$selected = $_REQUEST['newfile'];
			echo date("m-d-y H:i:s A"). ": You just created <b>".$_REQUEST['newfile']."</b>.<br>";
		}
		else {
			echo date("m-d-y H:i:s A"). ": The new file does not have a supported file extension</b>.<br>";			
		} 
	} 
	else {
		echo date("m-d-y H:i:s A"). ": You need to supply a new filename.<br>";
	}			
}

if ( isset($_REQUEST['rename']) && $_REQUEST['rename'] == 'yes') {
	if (isset($_REQUEST['newfile']) && $_REQUEST['newfile'] != '') {
			$file = $_REQUEST['newfile'];
            $splitted = explode('.', $file);
            $ext = strtolower($splitted[count($splitted)-1]);	
            if (in_array($ext, $supported_ext)) { 							
				rename($_REQUEST['path']."/".$_REQUEST['filename'], $_REQUEST['path']."/".$_REQUEST['newfile']) or die("Can't rename file.");
				echo date("m-d-y H:i:s A"). "</u>: You just renamed <b>". $_REQUEST['filename'] ."</b> to <b>". $_REQUEST['newfile']."</b>.<br>";
				$selected = $_REQUEST['newfile'];
			} else {
				echo date("m-d-y H:i:s A"). ": The new file does not have a supported file extension</b>.<br>";
			}
	} else {
		echo date("m-d-y H:i:s A"). " You need to supply a new filename.<br>";
	}			
}

if ( isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'yes') {
	unlink($_REQUEST['path']."/".$_REQUEST['filename']) or die("Can't delete file.");
	$selected = '';
	echo date("m-d-y H:i:s A"). ": You just deleted <b>". $_REQUEST['path']."/".$_REQUEST['filename'] ."</b>.<br>";
}

if (isset($_POST['input']) && isset($_REQUEST['filename']) && !isset($_REQUEST['delete']) && !isset($_REQUEST['create']) && !isset($_REQUEST['rename'])) {
	file_put_contents($_REQUEST['path']."/".$_REQUEST['filename'],$_REQUEST['input']);
	$selected = $_REQUEST['filename'];
	echo date("m-d-y H:i:s A"). ": You just edited <b>". $_REQUEST['path']."/".$_REQUEST['filename'] ."</b>.<br>";
}

if (isset($selected)) {
	if ($selected == "..") {
		$path = realpath($_SESSION['path'] . "/..");
		$_SESSION['path'] = $path;
	} elseif ($selected == ".") {
		$path = getcwd();
		$_SESSION['path'] = $path;
	} 
} 

if (isset($_SESSION['path'])) {
	$path = realpath($_SESSION['path']);
} else {
	$path = getcwd();
	$_SESSION['path'] = $path;
}

if (isset($_REQUEST['path'])) {
	$path = realpath($_REQUEST['path']);
	$_SESSION['path'] = $path;
}

?>

<DIV align="left" class="content">
<TABLE BORDER="0" CELLSPACING="10" CELLPADDING="0">
<TR><TD>
<SELECT NAME="whichfile" id="whichfile" size="32" ONCHANGE="selectfile();" class="list">

<?php

$files = array();
$path = $_SESSION['path'];

$dir = @opendir($path);
while ($file = @readdir($dir)){
      if (!is_dir($path.$file)){
            $splitted = explode('.', $file);
            $ext = strtolower($splitted[count($splitted)-1]);
            if (in_array($ext, $supported_ext)) $files[] = $file;
      }
}
@closedir($dir);
sort($files);

echo "<option value=\".\">.</option>\n";
echo "<option value=\"..\">..</option>\n";

for ($i = 0; $i < count($files); $i++) { 
	if (isset($selected) && ($files[$i] == $selected)) {
		echo "<option selected=\"true\" value=\"" . $files[$i] . "\">". $files[$i] . "</option>\n";
		continue;
	}
		echo "<option value=\"" . $files[$i] . "\">". $files[$i] . "</option>\n";
}

echo "</select></TD>";

if (isset($selected) && ($selected !== '') && ($selected != '..') && ($selected != '.')) {
	echo "<TD>";
	echo "<form id=\"edit\" action=\"". $_SERVER['PHP_SELF'] . "\" method=\"post\" name=\"edit\">\n";;
	echo "<input type=\"hidden\" name=\"filename\" value=\"". $selected . "\" />\n";
	echo "<input type=\"hidden\" name=\"path\" value=\"". $path . "\" />\n";
	echo "<textarea name=\"input\" class=\"text\" wrap=\"on\" cols=\"120\" rows=\"36\" onfocus=\"\" onblur=\"\">";
	echo htmlentities(file_get_contents($path . "/" . $selected, true), ENT_QUOTES, "UTF-8");
	echo "</textarea><br>\n";
	echo "<input type=\"checkbox\" name=\"create\" value=\"yes\" /> new \n";
	echo "<input type=\"checkbox\" name=\"rename\" value=\"yes\" /> rename \n";
	echo "<input type=\"checkbox\" name=\"delete\" value=\"yes\" /> delete &nbsp\n";
	echo "<input maxlength=\"128\" name=\"newfile\" value =\"".$selected."\" size=\"16\"/>\n";
	echo "&nbsp&nbsp<input name=\"submit\" type=\"submit\" class=\"submit\" style=\"width: 100px\" value=\"save\"/>";
	echo "</form></TD>\n";
}

if (isset($selected) && ($selected != '') && ($selected != '..') && ($selected != '.')) {
	echo "" . $path . "/" . $selected . "</b><br>";
} else {
	echo "" . $path . "<br>";
}

$args = "ls -1d ". $path ."/*/";
$dirs = `$args`;
$chunks = explode("\n",$dirs);
$i = 1;

foreach ( $chunks as $line ) {
	echo "<a href=\"". $_SERVER['PHP_SELF'] . "?path=".$line."\">".basename($line)."</a>&nbsp&nbsp";
	if ($i % 10 == 0) {
		echo "<br>";
	}
	++$i;
}

?>

</TD>
</TR>
</TABLE>

</DIV>
</FONT>

</BODY>
</HTML>