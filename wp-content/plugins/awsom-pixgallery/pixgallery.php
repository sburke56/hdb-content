<?php
/* -----------------------------------------------------------------------------
Plugin Name: AWSOM PixGallery
Version: 4.7.0
Plugin URI: http://www.awsom.org
Description: Photo/Webcomic Image Gallery for Wordpress
Author: Harknell and Nathan Moinvaziri
Author URI: http://www.awsom.org

Original PixGallery Code Base: .3.7
Original Pixgallery Developer: Nathan Moinvaziri
Original Dev Website: http://nathanm.com/myprojects/#plugin

EXIF library: phpExifReader courtesy of vinayRas Infotech copyright 2004
Gallery Folder Icon source: http://www.fatcow.com/free-icons
 ------------------------------------------------------------------------------- */
/* Get query values
/* ------------------------------------------------------------------------------ */

$QueryPathName	= "px";
$QueryStartName = "pxs";
$CollectionPath	= "/";
$GalleryPath	= "";

if (ini_get('safe_mode') == FALSE)
	set_time_limit(0); // Give enough time for thumbnails to create

/* ------------------------------------------------------------------------------ */
/* Default settings for plugin
/* ------------------------------------------------------------------------------ */
global $wpdb, $awsomcreatecachedir, $awsomcreatedefaultgallerydir, $awsomcreatecustomizationdir, $awsom_pixgallery_db_version;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $awsom_pixgallery_comment_tracking_table_name;
$awsom_pixgallery_db_version = 2;

if ( !defined('WP_CONTENT_URL') )
define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
$PixFolder_Path = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
$PixGlobal_Path = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
$awsomcreatecachedir =  WP_CONTENT_DIR. "/uploads/cache";
$awsomcreatedefaultgallerydir = WP_CONTENT_DIR. "/uploads/awsompixgallery";
$awsomcreatecustomizationdir = WP_CONTENT_DIR. "/uploads/awsom-customizations";
$wp_pxg_url	= get_bloginfo('wpurl');

if ( FORCE_SSL_ADMIN != true){
$wp_pxgadmin_url	= get_bloginfo('wpurl');
$PixGlobal_AdminPath = $PixGlobal_Path;
}
else {
$wp_pxgadmin_url = str_replace("http", "https", $wp_pxg_url);
$PixGlobal_AdminPath = str_replace("http", "https", $PixGlobal_Path);

}
$awsom_pixgallery_caption_table_name = $wpdb->prefix . "awsompxgimagecaptions";
$awsom_pixgallery_gallery_table_name = $wpdb->prefix . "awsompxggalleries";
$awsom_pixgallery_comment_tracking_table_name = $wpdb->prefix . "awsompxgcommenttracking";
$customdirimage = "pxggalleryimage";
$PixGallery_Columns					= '3';
$PixGallery_Rows					= '0';
$PixGallery_ImageWidth				= '150';
$PixGallery_ImageHeight				= '100';
$PixGallery_ImageHeightLarge		= '0';
$PixGallery_ImageWidthLarge			= '600';
$PixGallery_RootCache				= "wp-content/uploads/cache";
$PixGallery_ReduceOriginalsWidth	= '0';
$PixGallery_ReduceOriginalsHeight	= '0';
$PixGallery_PopupMethod				= 'none';
$PixGallery_SquareThumbnails		= FALSE;

/* Mod added for AWSOM options */
$PixGallery_HideSidebar             = '0';
$PixGallery_LinkToNewsPost          = '0';
$PixGallery_SortType = '0';
$PixGallery_RemoveUnderscoresFromName = '0';
$PixGallery_ImageCaption = '1';
$PixGallery_UseVisualEditor = '1';
$PixGallery_UseAbsPath		= '0';
$PixGallery_OpenInNewWindow = '0';
$PixGallery_MaxThumbnailsOnIndex = '0';
$PixGallery_NoThumbnailText = '0';
$PixGallery_WatermarkImages = '0';
$PixGallery_WatermarkStyle = '0';
$PixGallery_WatermarkColor = '0';
$PixGallery_AddFooterCredit = '1';
$PixGallery_ExtraNavBars = '0';
$PixGallery_CustomCSS = '';
$PixGallery_LegacyMode = '0';
$PixGallery_IndividualComments = '0';
$PixGallery_Displayexif = '0';
$PixGallery_DebugMode = '0';
$PixGallery_DebugModeType = '0';
$PixGallery_LimitPaginationLinks = '0';
$PixGallery_CustomRel = '';
$PixGallery_CustomPopup = '0';
$PixGallery_ImageViewNums = '0';
$PixGallery_Displayiptc = '0';
$PixGallery_ScanPosts = '0';
$PixGallery_VersionNumber = '4.7.0';
$PixGallery_ShowBreadCrumb = '0';
$PixGallery_RSSRightsEntry = 'Copyright';
$PixGallery_DisplayRSS = '0';
$PixGallery_RSSFeedNumber = '10';
$PixGallery_RSSCreatorEntry = 'My Name';
$PixGallery_DisplayRSSLink = '0';
$PixGallery_AdminFix = '0';
$PixGallery_RSSFeedLinkType = '0';
$PixGallery_RSSFeedFileInterval = '0';
$PixGallery_RSSPhotoFeed = '0';
$PixGallery_MetaKeywords = '0';
$PixGallery_SEOTitlePlace = '0';
$PixGallery_SEOTitleContent = '0';
$PixGallery_ImageClickBehavior = '0';
$PixGallery_UseFolderIcon = '0';
$PixGallery_ShareThisConnector = '0'; 
$PixGallery_ExcludeNewsAuthor = '0';

/* End Mod */
//$PixGlobal_Path						= get_option('siteurl')."/wp-content/plugins/awsompixgallery/";

require WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/support_files/exifReader.inc';


$WebsiteRoot = realpath("./")."/";


if (isset($_SERVER['REQUEST_URI']))
	$WebsitePath = PixGallery_UrlGetPath($_SERVER['REQUEST_URI']);
else{
   if (!isset($_SERVER['REQUEST_URI']))
    {
           $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
           if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
    }
}
	//$WebsitePath = PixGallery_UrlGetPath($_SERVER['PHP_SELF']);

$WebsiteActualPath = PixGallery_UrlGetPath($_SERVER['PHP_SELF']);

/* ------------------------------------------------------------------------------ */
/* Load settings from database
/* ------------------------------------------------------------------------------ */

PixGallery_Options_Load();

/* ------------------------------------------------------------------------------ */
/* General purpose functions
/* ------------------------------------------------------------------------------ */

function Pixgallery_CleanupCacheFolder($dir, $DeleteMe) {

    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) Pixgallery_CleanupCacheFolder($dir.'/'.$obj, true);
    }

    closedir($dh);
    if ($DeleteMe){
        @rmdir($dir);
    }
   }

function Pixgallery_CleanupTracking($commentid){
global $wpdb, $awsom_pixgallery_comment_tracking_table_name;

$PixgalleryDeleteTrackedComment = $wpdb->get_var("SELECT apgctid FROM $awsom_pixgallery_comment_tracking_table_name WHERE commentid = '$commentid'");
	if ($PixgalleryDeleteTrackedComment !=""){
	$wpdb->query("DELETE FROM $awsom_pixgallery_comment_tracking_table_name WHERE apgctid='$PixgalleryDeleteTrackedComment'");
	}
}

function Pixgallery_Comment_Passvars()
{
global $PixgalleryDisplayingImage, $Pixgalleryitempath, $PixgalleryReturnPath;

	echo  "<input type=\"hidden\" name=\"pixgalleryisimagedisplayed\" value=\"$PixgalleryDisplayingImage\" />";
	echo  "<input type=\"hidden\" name=\"pixgallerywhatimage\" value=\"$Pixgalleryitempath\" />";
	echo  "<input type=\"hidden\" name=\"pixgallerywheredoireturn\" value=\"$PixgalleryReturnPath\" />";
}

function Pixgallery_Comment_Proper_Return($location){
global $wpdb;
$pixgalleryisonimagepage = $_POST['pixgalleryisimagedisplayed'];
$pixgalleryisonimagepage = $wpdb->escape($pixgalleryisonimagepage);
$mypathback = $_POST['pixgallerywheredoireturn'];
$mypathback = $wpdb->escape($mypathback);
if ($pixgalleryisonimagepage != ""){
$location = $mypathback;
}
return $location;
}

function Pixgallery_Comment_Tracking($commentid)
{
global $wpdb, $awsom_pixgallery_comment_tracking_table_name, $PixgalleryDisplayingImage, $Pixgalleryitempath, $wpdb;
$itempath = $_POST['pixgallerywhatimage'];
$itempath = $wpdb->escape($itempath);
$pixgalleryisonimagepage = $_POST['pixgalleryisimagedisplayed'];
$pixgalleryisonimagepage = $wpdb->escape($pixgalleryisonimagepage);

if ($pixgalleryisonimagepage == "image"){

$thePostID = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = '$commentid'");
$wpdb->query("
		INSERT INTO $awsom_pixgallery_comment_tracking_table_name (apgctid, commentid, itempath, postid )
		VALUES (null, '$commentid', '$itempath', '$thePostID')");
		}
elseif ($pixgalleryisonimagepage == "gallery"){
$thePostID = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = '$commentid'");
$wpdb->query("
		INSERT INTO $awsom_pixgallery_comment_tracking_table_name (apgctid, commentid, itempath, postid )
		VALUES (null, '$commentid', '$itempath', '$thePostID')");
		}
}

function Pixgallery_Comment_Show($comments)
{
      global $wpdb, $awsom_pixgallery_comment_tracking_table_name, $PixgalleryDisplayingImage, $Pixgalleryitempath, $wp_query, $GalleryPath;
	
		$Pixgallery_Custom_Language_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if (file_exists($Pixgallery_Custom_Language_Location)){
	require WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';}
	else {
	require WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/pixgallery_lang.php';}
	
	
		if ($PixgalleryDisplayingImage == "image" ){
        $thePostID = $wp_query->post->ID;
        foreach( $comments as $comment ) {
        $onlyPixgallerycomments = $wpdb->get_var("SELECT apgctid FROM $awsom_pixgallery_comment_tracking_table_name WHERE itempath ='$Pixgalleryitempath' and postid = '$thePostID' and commentid = '$comment->comment_ID'");
                if($onlyPixgallerycomments != ""  ) {
                        $comments_skip[] = $comment;
                }
        }
        }
		elseif ($PixgalleryDisplayingImage == "gallery" && $Pixgalleryitempath == $GalleryPath ){
		$thePostID = $wp_query->post->ID;
				        foreach( $comments as $comment ) {
				        $onlyPixgallerycomments = $wpdb->get_var("SELECT apgctid FROM $awsom_pixgallery_comment_tracking_table_name WHERE itempath ='$Pixgalleryitempath' and postid = '$thePostID' and commentid = '$comment->comment_ID'");
				        $preexistingcomment = $wpdb->get_var("SELECT apgctid FROM $awsom_pixgallery_comment_tracking_table_name WHERE postid = '$thePostID' and commentid = '$comment->comment_ID'");
				               if($onlyPixgallerycomments != ""  ) {
				                        $comments_skip[] = $comment;
				                }
				                if($onlyPixgallerycomments == "" && $preexistingcomment == "" ) {
				                $comments_skip[] = $comment;
				                }
				        }
        }
		elseif ($PixgalleryDisplayingImage == "gallery" ){
		$thePostID = $wp_query->post->ID;
		        foreach( $comments as $comment ) {
		        $onlyPixgallerycomments = $wpdb->get_var("SELECT apgctid FROM $awsom_pixgallery_comment_tracking_table_name WHERE itempath ='$Pixgalleryitempath' and postid = '$thePostID' and commentid = '$comment->comment_ID'");
		                if($onlyPixgallerycomments != ""  ) {
		                        $comments_skip[] = $comment;
		                }
		        }
        }
        else {
         return $comments;
        }


if (empty($comments_skip) && $PixgalleryDisplayingImage == "image"){
$comments_skip[0]->comment_content = $PixGallery_Lang_Output_Frontend['no_comment_for_image_yet_text'];
$comments_skip[0]->comment_author = $PixGallery_Lang_Output_Frontend['comments_author_display_name'];
}
elseif (empty($comments_skip) && $PixgalleryDisplayingImage == "gallery"){
$comments_skip[0]->comment_content = $PixGallery_Lang_Output_Frontend['no_comment_for_gallery_yet_text'];
$comments_skip[0]->comment_author = $PixGallery_Lang_Output_Frontend['comments_author_display_name'];
}
return $comments_skip;
}


function PixGallery_WatermarkImagejpg($ImageNameToWatermark)
{
global $PixGallery_RootCache,$WebsiteRoot, $WebsiteActualPath, $PixGallery_WatermarkStyle,$PixGallery_WatermarkColor,$PixGallery_UseAbsPath,$PixFolder_Path, $wp_pxg_url;

$text_width = imagefontwidth(5);
$text_height = imagefontheight(5);
//$NewImageTitle		= PixGallery_UrlGetFileTitle($ImageNameToWatermark);
$NewImageExtension	= PixGallery_UrlGetFileExtension($ImageNameToWatermark);
$NewImageTitle = $ImageNameToWatermark;
//$NewImageTitle		= rawurldecode($NewImageTitle);
$NewImageTitle	= str_replace($WebsiteRoot, "", $NewImageTitle);
$NewImageTitle = $WebsiteActualPath.$NewImageTitle;
if ($PixGallery_UseAbsPath == "1" ){
//$NewImageTitle   = get_option( "siteurl" ) . "/" . $NewImageTitle;
}
$NewImageTitle	= str_replace("\\", "/", $NewImageTitle); // Firefox path fix
$NewImageTitle	= str_replace("//", "/", $NewImageTitle);

$NewImageTitle		= md5($NewImageTitle);
$NewImageFile		= $WebsiteRoot.$PixGallery_RootCache."/".$NewImageTitle.$NewImageExtension;
$imagemade = $NewImageFile;

if ($PixGallery_WatermarkStyle == "0"){
	$image = imagecreatefromjpeg($ImageNameToWatermark);

	if ($PixGallery_WatermarkColor == "0"){
	$text_color = imagecolorallocate($image, 0x00, 0x00, 0x00);
	}
	elseif ($PixGallery_WatermarkColor == "1"){
	$text_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	}
	elseif ($PixGallery_WatermarkColor == "2"){
	$text_color = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	}
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagestring($image, 5, $desttext_x, $desttext_y, $wp_pxg_url, $text_color);
	}
elseif ($PixGallery_WatermarkStyle == "1"){
$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.png";
	$watermark = imagecreatefrompng($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromjpeg($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
elseif ($PixGallery_WatermarkStyle == "2"){
	$image = imagecreatefromjpeg($ImageNameToWatermark);
	$imwidth = strlen($wp_pxg_url) * $text_width;
	$imheight = $text_height;
	$im = imagecreate($imwidth , $imheight);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	imagestring($im, 5, $desttext_x, $desttext_y, $wp_pxg_url, $black);
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagecopymerge($image, $im, $desttext_x, $desttext_y, 0, 0, $imwidth, $imheight, 100);
	}
elseif ($PixGallery_WatermarkStyle == "3"){
$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.gif";
	$watermark = imagecreatefromgif($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromjpeg($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
 else {
 }

imagejpeg($image, $imagemade);
imagedestroy($image);
if (PixGallery_WatermarkStyle == "1"){
	imagedestroy($watermark);
	}
if (PixGallery_WatermarkStyle == "2"){
	imagedestroy($im);
	}

}

function PixGallery_WatermarkImagepng($ImageNameToWatermark)
{
global $PixGallery_RootCache,$WebsiteRoot, $WebsiteActualPath, $PixGallery_WatermarkStyle, $PixGallery_WatermarkColor, $PixGallery_UseAbsPath, $PixFolder_Path, $wp_pxg_url, $wp_pxgadmin_url;

$text_width = imagefontwidth(5);
$text_height = imagefontheight(5);
//$NewImageTitle		= PixGallery_UrlGetFileTitle($ImageNameToWatermark);
$NewImageExtension	= PixGallery_UrlGetFileExtension($ImageNameToWatermark);
$NewImageTitle = $ImageNameToWatermark;
//$NewImageTitle		= rawurldecode($NewImageTitle);
$NewImageTitle	= str_replace($WebsiteRoot, "", $NewImageTitle);
$NewImageTitle = $WebsiteActualPath.$NewImageTitle;
if ($PixGallery_UseAbsPath == "1" ){
//$NewImageTitle   = get_option( "siteurl" ) . "/" . $NewImageTitle;
}
$NewImageTitle	= str_replace("\\", "/", $NewImageTitle); // Firefox path fix
$NewImageTitle	= str_replace("//", "/", $NewImageTitle);

$NewImageTitle		= md5($NewImageTitle);
$NewImageFile		= $WebsiteRoot.$PixGallery_RootCache."/".$NewImageTitle.$NewImageExtension;
$imagemade = $NewImageFile;

if ($PixGallery_WatermarkStyle == "0"){
	$image = imagecreatefrompng($ImageNameToWatermark);
	if ($PixGallery_WatermarkColor == "0"){
		$text_color = imagecolorallocate($image, 0x00, 0x00, 0x00);
		}
		elseif ($PixGallery_WatermarkColor == "1"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$text_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		}
		elseif ($PixGallery_WatermarkColor == "2"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		$text_color = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	}
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagestring($image, 5, $desttext_x, $desttext_y, $wp_pxg_url, $text_color);
	}
elseif ($PixGallery_WatermarkStyle == "1"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.png";
	$watermark = imagecreatefrompng($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefrompng($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
elseif ($PixGallery_WatermarkStyle == "2"){
	$image = imagecreatefrompng($ImageNameToWatermark);
	$imwidth = strlen($wp_pxg_url) * $text_width;
	$imheight = $text_height;
	$im = imagecreate($imwidth , $imheight);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	imagestring($im, 5, $desttext_x, $desttext_y, $wp_pxg_url, $black);
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagecopymerge($image, $im, $desttext_x, $desttext_y, 0, 0, $imwidth, $imheight, 100);
	}
elseif ($PixGallery_WatermarkStyle == "3"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.gif";
	$watermark = imagecreatefromgif($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefrompng($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
else {
}


imagepng($image, $imagemade);
imagedestroy($image);
if (PixGallery_WatermarkStyle == "1"){
	imagedestroy($watermark);
	}
if (PixGallery_WatermarkStyle == "2"){
	imagedestroy($im);
	}

}

function PixGallery_WatermarkImagegif($ImageNameToWatermark)
{
global $PixGallery_RootCache,$WebsiteRoot, $WebsiteActualPath, $PixGallery_WatermarkStyle, $PixGallery_WatermarkColor, $PixGallery_UseAbsPath, $PixFolder_Path, $wp_pxg_url;

$text_width = imagefontwidth(5);
$text_height = imagefontheight(5);
//$NewImageTitle		= PixGallery_UrlGetFileTitle($ImageNameToWatermark);
$NewImageExtension	= PixGallery_UrlGetFileExtension($ImageNameToWatermark);
$NewImageTitle = $ImageNameToWatermark;
//$NewImageTitle		= rawurldecode($NewImageTitle);
$NewImageTitle	= str_replace($WebsiteRoot, "", $NewImageTitle);
$NewImageTitle = $WebsiteActualPath.$NewImageTitle;
if ($PixGallery_UseAbsPath == "1" ){
//$NewImageTitle	= str_replace($WebsiteActualPath, "", $NewImageTitle);
//$NewImageTitle   = "/". $NewImageTitle;
}
$NewImageTitle	= str_replace("\\", "/", $NewImageTitle); // Firefox path fix
$NewImageTitle	= str_replace("//", "/", $NewImageTitle);

$NewImageTitle		= md5($NewImageTitle);
$NewImageFile		= $WebsiteRoot.$PixGallery_RootCache."/".$NewImageTitle.$NewImageExtension;
$imagemade = $NewImageFile;

if ($PixGallery_WatermarkStyle == "0"){
	$image = imagecreatefromgif($ImageNameToWatermark);
	if ($PixGallery_WatermarkColor == "0"){
		$text_color = imagecolorallocate($image, 0x00, 0x00, 0x00);
		}
		elseif ($PixGallery_WatermarkColor == "1"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$text_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		}
		elseif ($PixGallery_WatermarkColor == "2"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		$text_color = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	}
	$text_color = $red;
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagestring($image, 5, $desttext_x, $desttext_y, $wp_pxg_url, $text_color);
	}
elseif ($PixGallery_WatermarkStyle == "1"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.png";
	$watermark = imagecreatefrompng($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromgif($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
elseif ($PixGallery_WatermarkStyle == "2"){
	$image = imagecreatefromgif($ImageNameToWatermark);
	$imwidth = strlen($wp_pxg_url) * $text_width;
	$imheight = $text_height;
	$im = imagecreate($imwidth , $imheight);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	imagestring($im, 5, $desttext_x, $desttext_y, $wp_pxg_url, $black);
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagecopymerge($image, $im, $desttext_x, $desttext_y, 0, 0, $imwidth, $imheight, 100);
	}
elseif ($PixGallery_WatermarkStyle == "3"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.gif";
	$watermark = imagecreatefromgif($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromgif($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
else {
}

imagegif($image, $imagemade);
imagedestroy($image);
if (PixGallery_WatermarkStyle == "1"){
	imagedestroy($watermark);
	}
if (PixGallery_WatermarkStyle == "2"){
	imagedestroy($im);
	}

}

function PixGallery_WatermarkImagewbmp($ImageNameToWatermark)
{
global $PixGallery_RootCache,$WebsiteRoot, $WebsiteActualPath, $PixGallery_WatermarkStyle, $PixGallery_WatermarkColor, $PixGallery_UseAbsPath, $PixFolder_Path, $wp_pxg_url;

$text_width = imagefontwidth(5);
$text_height = imagefontheight(5);
//$NewImageTitle		= PixGallery_UrlGetFileTitle($ImageNameToWatermark);
$NewImageExtension	= PixGallery_UrlGetFileExtension($ImageNameToWatermark);
$NewImageTitle = $ImageNameToWatermark;
//$NewImageTitle		= rawurldecode($NewImageTitle);
$NewImageTitle	= str_replace($WebsiteRoot, "", $NewImageTitle);
$NewImageTitle = $WebsiteActualPath.$NewImageTitle;
if ($PixGallery_UseAbsPath == "1" ){
//$NewImageTitle   = get_option( "siteurl" ) . "/" . $NewImageTitle;
}
$NewImageTitle	= str_replace("\\", "/", $NewImageTitle); // Firefox path fix
$NewImageTitle	= str_replace("//", "/", $NewImageTitle);

$NewImageTitle		= md5($NewImageTitle);
$NewImageFile		= $WebsiteRoot.$PixGallery_RootCache."/".$NewImageTitle.$NewImageExtension;
$imagemade = $NewImageFile;

if ($PixGallery_WatermarkStyle == "0"){
	$image = imagecreatefromwbmp($ImageNameToWatermark);
	if ($PixGallery_WatermarkColor == "0"){
		$text_color = imagecolorallocate($image, 0x00, 0x00, 0x00);
		}
		elseif ($PixGallery_WatermarkColor == "1"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$text_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		}
		elseif ($PixGallery_WatermarkColor == "2"){
		$text_colorbac = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		$text_color = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	}
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagestring($image, 5, $desttext_x, $desttext_y, $wp_pxg_url, $text_color);
	}
elseif ($PixGallery_WatermarkStyle == "1"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.png";
	$watermark = imagecreatefrompng($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromwbmp($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
elseif ($PixGallery_WatermarkStyle == "2"){
	$image = imagecreatefromwbmp($ImageNameToWatermark);
	$imwidth = strlen($wp_pxg_url) * $text_width;
	$imheight = $text_height;
	$im = imagecreate($imwidth , $imheight);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	imagestring($im, 5, $desttext_x, $desttext_y, $wp_pxg_url, $black);
	$size = getimagesize($ImageNameToWatermark);
	$desttext_x = $size[0] - ((strlen($wp_pxg_url) * $text_width) + 5);
	$desttext_y = $size[1] - ($text_height + 5);
	imagecopymerge($image, $im, $desttext_x, $desttext_y, 0, 0, $imwidth, $imheight, 100);
	}
elseif ($PixGallery_WatermarkStyle == "3"){
	$mypixgallerywatermarkfile = $PixFolder_Path."images/watermark.gif";
	$watermark = imagecreatefromgif($mypixgallerywatermarkfile);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$image = imagecreatetruecolor($watermark_width, $watermark_height);
	$image = imagecreatefromwbmp($ImageNameToWatermark);
	$size = getimagesize($ImageNameToWatermark);
	$dest_x = $size[0] - $watermark_width - 5;
	$dest_y = $size[1] - $watermark_height - 5;
	//imagealphablending($image,true);
	//imagealphablending($watermark,true);
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
	}
else {
}

imagewbmp($image, $imagemade);
imagedestroy($image);
if (PixGallery_WatermarkStyle == "1"){
	imagedestroy($watermark);
	}
if (PixGallery_WatermarkStyle == "2"){
	imagedestroy($im);
	}

}


function PixGallery_PrintError($ErrorMessage)
{

	print("<font color=red>".$ErrorMessage."</font><br />");
}

function PixGallery_UrlGetFileExtension($Url)
{
	$FileName	= PixGallery_UrlGetFileName($Url);
	$LastDot	= strrpos($FileName, ".");

	if ($LastDot === FALSE)
		return "";

	return substr($FileName, $LastDot);
}

function PixGallery_UrlGetFileTitle($Url)
{
	$FileName = PixGallery_UrlGetFileName($Url);

	if (strrpos($FileName, ".") === FALSE)
		return $FileName;

	return substr($FileName, 0, strrpos($FileName, "."));
}

function PixGallery_UrlGetFileName($Url)
{
	$Url = str_replace("\\", "/", $Url);

	if (strrpos($Url, "/") === FALSE)
		return $Url;

	return substr($Url, strrpos($Url, "/") + 1);
}

function PixGallery_UrlGetPath($Url)
{
	$PathSplit = "/";

	if (strpos($Url, "/") === FALSE)
		$PathSplit = "\\";

	$PathEnd		= strrpos($Url, $PathSplit);
	$ProtocolStart	= strpos($Url, "://");

	if ($PathEnd === FALSE)
		return $PathSplit;

	$PathEnd += 1;

	if ($ProtocolStart != FALSE)
		{
		$ProtocolStart += 3;
		$Url = substr($Url, $ProtocolStart);
		}

	$QueryStart = strpos($Url, "?");

	if ($QueryStart !== FALSE)
		$Url = substr($Url, 0, $QueryStart);

	$PathStart = strpos($Url, $PathSplit);

	if ($PathStart === FALSE)
		return $PathSplit;

	$Url = substr($Url, $PathStart, $PathEnd - $PathStart);

	return $Url;
}

function PixGallery_UrlBuild($Name, $Value = "")
{
	global $WebsitePath, $QueryStartName;
	global $post;

	$PermalinkPath = "";

	if (isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] != "/")
		$PermalinkPath = $_SERVER['ORIG_PATH_INFO'];

	$Url		= $WebsitePath.$PermalinkPath."?".$_SERVER['QUERY_STRING'];
	$Query		= "";
	$NewQuery	= "";
	$NewUrl		= $Url;
	$QueryStart = strpos($Url, "?");
	$Added		= FALSE;
	$AddedPost	= FALSE;

	if ($QueryStart !== FALSE)
		{
		$Query = substr($Url, $QueryStart + 1);
		$NewUrl = substr($Url, 0, $QueryStart);
		}

	$Pairs = explode("&", $Query."&");

	foreach ($Pairs as $Pair)
		{
		if (strpos($Pair, "=") === FALSE)
			continue;

		list($PairName, $PairValue) = explode("=", $Pair);

		if (($Name != $QueryStartName) && ($PairName == $QueryStartName))
			continue;
		if (!is_page() && $PairName == "p")
			$AddedPost = TRUE;

		if ($PairName == $Name)
			{
			if ($Value != "")
				$NewQuery .= "&$Name=".urlencode($Value);

			$Added = TRUE;
			}
		else if ($PairValue != "")
			$NewQuery .= "&$PairName=".$PairValue;
		}

	if ($Added == FALSE)
		$NewQuery .= "&$Name=".urlencode($Value);
	if (!is_page() && $AddedPost == FALSE){
		//$NewQuery .= "&p=".$post->ID;
		$NewQuery = "&p=".$post->ID.$NewQuery;}

	$NewQuery	= "?".trim($NewQuery, "&");
	$NewUrl		.= $NewQuery;
	//***Brute force attempt to compensate for the index.php injection problem on some systems ****//
    $NewUrl = str_replace("index.php","",$NewUrl);
    //***end mod***//
	return str_replace("//", "/", $NewUrl);
}

/* ------------------------------------------------------------------------------ */
/* Image functions, html parsing
/* ------------------------------------------------------------------------------ */

function PixGallery_ImageCalculateSizeEx($ImageFile, $LargeSize = FALSE)
{
	global $PixGallery_ImageWidth, $PixGallery_ImageHeight;
	global $PixGallery_ImageWidthLarge, $PixGallery_ImageHeightLarge;

	if ($LargeSize == TRUE)
		return PixGallery_ImageCalculateSize($ImageFile, $PixGallery_ImageWidthLarge, $PixGallery_ImageHeightLarge);

	return PixGallery_ImageCalculateSize($ImageFile, $PixGallery_ImageWidth, $PixGallery_ImageHeight);
}

function PixGallery_ImageCalculateSize($ImageFile, $MaximumWidth, $MaximumHeight)
{
	global $PixGallery_SquareThumbnails;


	if ((file_exists($ImageFile) === FALSE) && (@getimagesize($ImageFile) === FALSE))
		return array('Width' => 0, 'Height' => 0);

	$ImageObject = getimagesize($ImageFile);

	$X = 0;
	$Y = 0;

	if ($ImageObject === FALSE)
		{
		echo "Invalid image or bad permissions [$ImageFile]<BR>\n";
		return FALSE;
		}

	$ImageWidth			= $MaximumWidth;
	$ImageHeight		= $MaximumHeight;

	$ImageRealWidth		= $ImageObject[0];
	$ImageRealHeight	= $ImageObject[1];

	$ImagePercentWidth	= ($ImageWidth / $ImageRealWidth);
	$ImagePercentHeight = ($ImageHeight / $ImageRealHeight);

	if ($MaximumWidth == 0)
		$MaximumWidth = FALSE;
	if ($MaximumHeight == 0)
		$MaximumHeight = FALSE;

	if (strpos($MaximumWidth, "px") !== FALSE)
		$MaximumWidth = str_replace("px", "", $MaximumWidth);
	if (strpos($MaximumHeight, "px") !== FALSE)
		$MaximumHeight = str_replace("px", "", $MaximumHeight);

	if ($MaximumWidth !== FALSE && $MaximumHeight === FALSE)
		{
		if (strpos($MaximumWidth, "%") !== FALSE)
			{
			$ImagePercentWidth	= (intval(str_replace("%", "", $MaximumWidth)) / 100);
			$ImageWidth			= round($ImageRealWidth * $ImagePercentWidth);
			}
		else
			$ImageWidth	= round($ImageRealWidth * $ImagePercentWidth);

		if ($ImageRealWidth < $ImageWidth)
			{
			$ImageWidth		= $ImageRealWidth;
			$ImageHeight	= $ImageRealHeight;
			}
		else
			$ImageHeight	= round($ImageRealHeight * $ImagePercentWidth);
		}
	else if ($MaximumWidth === FALSE && $MaximumHeight !== FALSE)
		{
		if (strpos($MaximumHeight, "%") !== FALSE)
			{
			$ImagePercentHeight	= (intval(str_replace("%", "", $MaximumHeight)) / 100);
			$ImageHeight		= round($ImageRealHeight * $ImagePercentHeight);
			}
		else
			$ImageHeight	= round($ImageRealHeight * $ImagePercentHeight);

		if ($ImageRealHeight < $ImageHeight)
			{
			$ImageWidth		= $ImageRealWidth;
			$ImageHeight	= $ImageRealHeight;
			}
		else
			$ImageWidth		= round($ImageRealWidth * $ImagePercentHeight);
		}
	else if ($MaximumWidth !== FALSE && $MaximumHeight !== FALSE)
		{
		if ($MaximumWidth != $MaximumHeight && $PixGallery_SquareThumbnails == FALSE)
			{
			if ($ImageRealWidth < $ImageWidth)
				$ImageWidth = $ImageRealWidth;

			$ImageHeight = round(($ImageWidth / $ImageRealWidth) * $ImageRealHeight);

			if ($ImageHeight > $MaximumHeight)
				{
				$ImageHeight	= $MaximumHeight;
				$ImageWidth		= round(($ImageHeight / $ImageRealHeight) * $ImageRealWidth);
				}
			}
		}
	else if ($MaximumWidth === FALSE && $MaximumHeight === FALSE)
		{
		$ImageWidth		= $ImageRealWidth;
		$ImageHeight	= $ImageRealHeight;
		}

	return array('Width' => $ImageWidth, 'Height' => $ImageHeight, 'X' => $X, 'Y' => $Y, 'RealWidth' => $RealWidth, 'RealHeight' => 0);
}

/* Set or get the an html tag attribute */

function PixGallery_HtmlAttribute($TagContents, $AttributeName, $AttributeValue = FALSE)
{
	$TagContentsLower	= strtolower($TagContents);
	$AttributeNameLower	= strtolower($AttributeName);

	$AttributeStart = strpos($TagContentsLower, $AttributeNameLower);

	if ($AttributeStart === FALSE)
		return FALSE;

	$AttributeStart = strpos($TagContentsLower, "=", $AttributeStart);

	if ($AttributeStart === FALSE)
               return FALSE;
	while ($TagContents[$AttributeStart++] == ' ');

	$FindEndChar = ' ';

	if ($TagContents[$AttributeStart] == '\'')
		$FindEndChar = '\'';
	if ($TagContents[$AttributeStart] == '"')
		$FindEndChar = '"';

	$AttributeEnd = strpos($TagContentsLower, $FindEndChar, $AttributeStart + 1);

	if ($FindEndChar != ' ')
		{
		while ($TagContents[$AttributeEnd - 1] == '\\')
			$AttributeEnd = strpos($TagContentsLower, $FindEndChar, $AttributeEnd + 1);

		$AttributeStart++;
		}

	if ($AttributeEnd == FALSE)
		$AttributeEnd = strlen($TagContents);

	if ($AttributeValue == FALSE)
		{
		$AttributeValue = substr($TagContents, $AttributeStart, $AttributeEnd - $AttributeStart);

		return $AttributeValue;
		}

	$NewTagContents = substr($TagContents, 0, $AttributeStart);
	$NewTagContents.= $AttributeValue;
	$NewTagContents.= substr($TagContents, $AttributeEnd);

	return $NewTagContents;
}

function PixGallery_ImageReduce($ImageFileName)
{
	global $PixGallery_ReduceOriginalsWidth, $PixGallery_ReduceOriginalsHeight;

	$MaximumWidth	= $PixGallery_ReduceOriginalsWidth;
	$MaximumHeight	= $PixGallery_ReduceOriginalsHeight;

	if ($MaximumWidth == 0)
		return FALSE;

	$ImageInfo		= getimagesize($ImageFileName);
	$ImageObject	= FALSE;

	$ImageWidth		= $ImageInfo[0];
	$ImageHeight	= $ImageInfo[1];
	$ImageFileSize	= @filesize($ImageFileName);

	if ($ImageFileSize == FALSE)
		{
		PixGallery_PrintError("Error: Your Image Folder is not Set to the 777 permissions needed to allow Global Resize function to operate");
		return FALSE;
		}

	if ($ImageWidth > $MaximumWidth)
		{
		$ImageSize		= PixGallery_ImageCalculateSize($ImageFileName, $MaximumWidth, $MaximumHeight);

		$ImageWidth		= $ImageSize['Width'];
		$ImageHeight	= $ImageSize['Height'];

		PixGallery_ImageResize($ImageFileName, $ImageWidth, $ImageHeight, $ImageFileName.".tmp");

		unlink($ImageFileName);
		copy($ImageFileName.".tmp", $ImageFileName);
		touch($ImageFileName, filemtime($ImageFileName.".tmp"));
		unlink($ImageFileName.".tmp");
		}

	return TRUE;
}

function PixGallery_ImageResize($ImageFileName, $NewImageWidth, $NewImageHeight, $NewImageFile = FALSE)
{
	global $PixGallery_RootCache, $PixGallery_SquareThumbnails;
	global $WebsiteRoot, $PixGallery_WatermarkImages;


	$ImageInfo		= @getimagesize($ImageFileName);
	$ImageObject	= FALSE;

	if ($NewImageFile == FALSE)
		{
		//****mod to add in new MD5 thumbnail naming system (thank you Nathan for the Help)****//
		$NewImageExtension    = PixGallery_UrlGetFileExtension($ImageFileName);
		$NewImageTitle        = md5($ImageFileName.$NewImageWidth.'x'.$NewImageHeight);
		$NewImageFile        =  $WebsiteRoot.$PixGallery_RootCache."/".$NewImageTitle.$NewImageExtension;
		//****end md5 mod *****//
		if ((file_exists($NewImageFile)) && (filemtime($NewImageFile) == @filemtime($ImageFileName)))
			return $NewImageFile;
		}

	switch ($ImageInfo['mime'])
		{
		case 'image/gif':
			if (imagetypes() & IMG_GIF)
				$ImageObject = imagecreatefromgif($ImageFileName);
			break;
		case 'image/jpeg':
			if (imagetypes() & IMG_JPG)
				$ImageObject = imagecreatefromjpeg($ImageFileName);
			break;
		case 'image/png':
			if (imagetypes() & IMG_PNG)
				$ImageObject = imagecreatefrompng($ImageFileName);
			break;
		case 'image/wbmp':
			if (imagetypes() & IMG_WBMP)
				$ImageObject = imagecreatefromwbmp($ImageFileName);
			break;
		default:
			echo "Image format not supported [".$ImageInfo['mime']."]";
			break;
		}

	if ($ImageObject == FALSE)
		return FALSE;

	$RealImageWidth	= $ImageInfo[0];
	$RealImageHeight = $ImageInfo[1];

	$X = 0;
	$Y = 0;

	if ($PixGallery_SquareThumbnails == TRUE)
	if ($NewImageWidth == $NewImageHeight)
	if ($RealImageWidth > $RealImageHeight)
		{
		$X = ceil(($RealImageWidth - $RealImageHeight) / 2);
		$RealImageWidth = $RealImageHeight;
		}
	else
		{
		$Y = ceil(($RealImageHeight - $RealImageWidth) / 2);
		$RealImageHeight = $RealImageWidth;
		}

	if ($RealImageWidth < $NewImageWidth)
		return $ImageFileName;

	$NewImageObject = imagecreatetruecolor($NewImageWidth, $NewImageHeight);
	imagecopyresampled($NewImageObject, $ImageObject, 0, 0, $X, $Y, $NewImageWidth, $NewImageHeight, $RealImageWidth, $RealImageHeight);

	switch ($ImageInfo['mime'])
		{
		case 'image/gif':
			imagegif($NewImageObject, $NewImageFile);
			if ($PixGallery_WatermarkImages == 1){
				PixGallery_WatermarkImagegif($ImageFileName);
			}
			break;
		case 'image/jpeg':
			imagejpeg($NewImageObject, $NewImageFile, 100);
			if ($PixGallery_WatermarkImages == 1){
				PixGallery_WatermarkImagejpg($ImageFileName);
			}
			break;
		case 'image/png':
			imagepng($NewImageObject, $NewImageFile);
			if ($PixGallery_WatermarkImages == 1){
				PixGallery_WatermarkImagepng($ImageFileName);
			}
			break;
		case 'image/wbmp':
			imagewbmp($NewImageObject, $NewImageFile);
			if ($PixGallery_WatermarkImages == 1){
				PixGallery_WatermarkImagewbmp($ImageFileName);
			}
			break;
		default:
			echo "Image format not supported [".$ImageInfo['mime']."]";
			break;
		}

	imagedestroy($ImageObject);
	imagedestroy($NewImageObject);

	@touch($NewImageFile, filemtime($ImageFileName));

	return $NewImageFile;
}

function PixGallery_Image($Html)
{
	global $PixGallery_PopupMethod, $PixGallery_RootCache;
	global $WebsiteRoot, $WebsitePath, $WebsiteActualPath, $PixGallery_UseAbsPath, $PixGallery_CustomPopup, $PixGallery_CustomRel, $PixGallery_ScanPosts;
// Mod to prevent posts being scanned
if ( $PixGallery_ScanPosts == "1" && !is_page()) {
 return $Html;
 }
 
// end mod

if ($PixGallery_CustomPopup == "1"){
	$PixGalleryUseRel = $PixGallery_CustomRel;
	}
	else {
	$PixGalleryUseRel = "PixGallery";
	}
	/* Begin parsing html looking for images */

	$HtmlLower		= strtolower($Html);
	$NewHtml		= "";

	$TagName		= "img";
	$TagBeginPrev   = 0;
	$TagBegin		= 0;
	$TagEnd			= 0;

	$TagNamePrev	= "";

	while (TRUE)
		{
		$TagBegin = strpos($HtmlLower, "<$TagName", $TagEnd);

		if ($TagBegin === FALSE)
			break;

		$TagEnd	= strpos($HtmlLower, ">", $TagBegin);

		if ($TagEnd	=== FALSE)
			break;

		$NewHtml		.= substr($Html, $TagBeginPrev, $TagBegin - $TagBeginPrev);
		$TagBegin		+= (strlen($TagName) + 1);
		$TagContents	= substr($Html, $TagBegin, $TagEnd - $TagBegin);
		$TagBeginPrev	= $TagBegin;
		$TagEnd			+= 1;

		$ImageSource	= urldecode(PixGallery_HtmlAttribute($TagContents, 'src'));

		if ($ImageSource === FALSE)
			continue;

		$ImageWidth		= PixGallery_HtmlAttribute($TagContents, 'width');
		$ImageHeight	= PixGallery_HtmlAttribute($TagContents, 'height');
		$ImageClass		= PixGallery_HtmlAttribute($TagContents, 'class');

		if ($ImageWidth == "")
			$ImageWidth	= FALSE;
		else
			$ImageWidth = str_replace("px", "", $ImageWidth);

		if ($ImageHeight == "")
			$ImageHeight = FALSE;
		else
			$ImageHeight = str_replace("px", "", $ImageHeight);

		$ImageNewPath	= "";
		$ImagePath		= $ImageSource;

		if (strpos($ImagePath, "://") === FALSE)
			{
			if ($WebsiteActualPath != "/")
				$ImagePath = str_replace($WebsiteActualPath, "", $ImagePath);

			$ImagePath = $WebsiteRoot.$ImagePath;
			}
		PixGallery_ImageReduce($ImagePath); /* Reduce original image size if need */

		$ImageRealInfo		= @getimagesize($ImagePath);

		$ImageRealWidth		= $ImageRealInfo[0];
		$ImageRealHeight	= $ImageRealInfo[1];

		$ImageResizeWidth	= FALSE;
		$ImageResizeHeight	= FALSE;

		$ImageNewSize	= PixGallery_ImageCalculateSize($ImagePath, $ImageWidth, $ImageHeight);

		if (($ImageNewSize['Width'] != $ImageRealWidth) || ($ImageNewSize['Height'] != $ImageRealHeight))
			{
			$ImageNewPath = PixGallery_ImageResize($ImagePath, $ImageNewSize['Width'], $ImageNewSize['Height']);
			}

		$ImageNewPath	= str_replace($WebsiteRoot, "", $ImageNewPath);
		$ImageNewPath	= str_replace("\\", "/", $ImageNewPath); // Firefox path fix
		$ImageNewPath	= str_replace("//", "/", $ImageNewPath);
		//****Mod to add Absolute URL path support****//
		$ImageAbsNewPath   = get_option( "siteurl" ) . "/" . $ImageNewPath;
		//****end mod****//
		/* Set image's new width/height, exspecially if it was previously a % */

		if ($ImageWidth !== FALSE)
			$TagContents = PixGallery_HtmlAttribute($TagContents, 'width', $ImageNewSize['Width']);
		if ($ImageHeight !== FALSE)
			$TagContents = PixGallery_HtmlAttribute($TagContents, 'height', $ImageNewSize['Height']);

		if ($ImageNewPath != "")
			{
			//****Mod to add Absolute URL path support****//
			if ($PixGallery_UseAbsPath == "1") {
			$TagContents		= PixGallery_HtmlAttribute($TagContents, 'src', $ImageAbsNewPath);}
			else {
			$TagContents		= PixGallery_HtmlAttribute($TagContents, 'src', $ImageNewPath);}
			//****end mod****//
			$ImageOnClick		= PixGallery_HtmlAttribute($TagContents, 'onclick');
			$ImageAlt			= PixGallery_HtmlAttribute($TagContents, 'alt');
			$ImageNoPopup		= (strpos($TagContents, "nopopup") !== FALSE);

			$PreviousTagIsLink	= FALSE;
			$PreviousTagHtml	= substr($Html, 0, ($TagBegin - strlen($TagName) - 1));
			$PreviousTagBegin	= strrpos($PreviousTagHtml, "<");

			if ($PreviousTagBegin !== FALSE)
				{
				if (strtolower($PreviousTagHtml[$PreviousTagBegin + 1]) == 'a')
					$PreviousTagIsLink = TRUE;
				}

			if ($ImageClass == FALSE)
				$TagContents .= " class='PxgGlobalImage'";

			$TagContents = trim($TagContents);

			if (($ImageOnClick == FALSE) && ($PreviousTagIsLink == FALSE) && ($ImageNoPopup == FALSE))
				{
				$NewHtml .= "<a href=\"$ImageSource\" rel='$PixGalleryUseRel' title=\"$ImageAlt\"><$TagName $TagContents></a>";
				}
			else
				{
				$NewHtml .= "<$TagName $TagContents>";
				}

			$TagBeginPrev	= $TagEnd;
			}
		else
			{
			$NewHtml .= "<$TagName ";

			if ($ImageClass == FALSE)
				$NewHtml .= "class='PxgGlobalImage' ";
			}
		}

	$TagNamePrev = $TagName;

	$NewHtml .= substr($Html, $TagBeginPrev);

	return $NewHtml;
}

/* ------------------------------------------------------------------------------ */
/* Photo gallery functions
/* ------------------------------------------------------------------------------ */



# Patch sbilbeau : enable feed support
function PixGallery_Feed($lists, $feedname,$ContentParentUrl) {

	global $GalleryPath,$wp_pxgadmin_url,$PixGallery_RootCache, $PixGallery_RSSFeedNumber, $PixGallery_RSSCreatorEntry, $PixGallery_RSSRightsEntry,$QueryPathName, $PixGallery_RSSFeedLinkType, $PixGallery_RSSFeedFileInterval, $PixGallery_RSSPhotoFeed;

If (is_array($lists)){
	
			$ReversedLists = $lists;
			usort($ReversedLists, create_function('$a,$b','return strnatcasecmp($a[FileTimeCreated],$b[FileTimeCreated]);'));
			//$howmanyimages = count($lists);
			//if ($howmanyimages > 1) {
				//if ($lists[0][FileTimeCreated] > $lists[$howmanyimages-1][FileTimeCreated]){
					$ReversedLists = array_reverse($ReversedLists);
					//}
				//}
			$i = 0;
			$x = count($ReversedLists);
			unset($ReversedLists[$i]['Previous']);
			unset($ReversedLists[$x - 1]['Next']);
			while ($i < $x){
			if (isset($ReversedLists[$i - 1]['Link'])){
			$ReversedLists[$i]['Previous'] = $ReversedLists[$i - 1]['Link'];
			$ReversedLists[$i - 1]['Next'] = $ReversedLists[$i]['Link'];
			}
			$i++;
			}
		$lists = $ReversedLists;	
}


	$RSSconvertedpath = str_replace('/','-', $feedname);
	$RSSprettyname = str_replace('/',' ', $feedname);
	$pxgthisgalfeedname = 'Pixgallery'.$RSSconvertedpath.'imagefeed.xml';

	
	$cachefile = $PixGallery_RootCache.'/'.$pxgthisgalfeedname;
	if (file_exists($cachefile)) {
    	$whenwasfeedmade = filemtime($cachefile);
    	}
    $checkrightnow = time();
     if ($PixGallery_RSSFeedFileInterval == 0 || $PixGallery_RSSFeedFileInterval == ""){
     	$feedcheckinterval = 86400;
     	}
     elseif ($PixGallery_RSSFeedFileInterval == 1){
    	 $feedcheckinterval = 43200;
     	}
     elseif ($PixGallery_RSSFeedFileInterval == 2){
    	 $feedcheckinterval = 21600;
     	}
     elseif ($PixGallery_RSSFeedFileInterval == 3){
    	 $feedcheckinterval = 10800;
     	}
     elseif ($PixGallery_RSSFeedFileInterval == 4){
    	 $feedcheckinterval = 3600;
     	}
     elseif ($PixGallery_RSSFeedFileInterval == 5){
    	 $feedcheckinterval = 0;
     	}
     else {
     	$feedcheckinterval = 0;
     	}
     
     if ($whenwasfeedmade <= ($checkrightnow - $feedcheckinterval)) {
   	
	$file = fopen($cachefile, 'w');

	fwrite($file, '<?xml version="1.0" encoding="UTF-8" ?>'."\n");

	fwrite($file, '<rdf:RDF
			
			xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
			xmlns:dc="http://purl.org/dc/elements/1.1/"
			xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
			xmlns:admin="http://webns.net/mvcb/"
			xmlns:cc="http://web.resource.org/cc/"
			xmlns:content="http://purl.org/rss/1.0/modules/content/"
			xmlns="http://purl.org/rss/1.0/">


		<channel rdf:about="'.$wp_pxgadmin_url.'">
		<title>'.$wp_pxgadmin_url.' Gallery: '.$RSSprettyname.'RSS Feed</title>
		<description></description>
		<link>'.$wp_pxgadmin_url.'</link>
		<dc:language>en</dc:language>
		<dc:creator>'.$PixGallery_RSSCreatorEntry.'</dc:creator>
		<dc:rights>'.$PixGallery_RSSRightsEntry.'</dc:rights>
		<dc:date>'.date("Y-m-d\\TH:i:s+00:00").'</dc:date>
		<admin:generatorAgent rdf:resource="'.$wp_pxgadmin_url.'" />

		<items>
		<rdf:Seq>');

	$cpt = 0;
	if (strlen($GalleryPath) - 1 != '/') $GalleryPath .= "/";
	foreach($lists as $img) {
		if ($img['Type'] == "Directory"){
		continue;
		}
		# Flv support
		if(preg_match("/photos\/videos/i", $img['Imagesortpath'])) {
			$link = $wp_pxgadmin_url.preg_replace('/\..+$/', '.flv', $img['Imagesortpath']);
		} else {
			if ($PixGallery_RSSFeedLinkType == "1"){
			$link = $wp_pxgadmin_url.$img['Imagesortpath'];
			$enclosurelink = $wp_pxgadmin_url.$img['Imagesortpath'];
				}
			else {
			$link = $img['RSSPageLink'];
			$enclosurelink = $wp_pxgadmin_url.$img['Imagesortpath'];
			}
			
		}

		fwrite($file, '<rdf:li rdf:resource="'.$link.'"/>'."\n");
		$cpt++;
		if($cpt >= $PixGallery_RSSFeedNumber) break;
	}
		
	fwrite($file,'</rdf:Seq>
		      </items>
		      </channel>');

	$cpt = 0;
	foreach($lists as $img) {
		if ($img['Type'] == "Directory"){
		continue;
		}
		# Flv support
		if(preg_match("/photos\/videos/i", $img['Imagesortpath'])) {
			$link = $wp_pxgadmin_url.preg_replace('/\..+$/', '.flv', $img['Imagesortpath']);
		} else {
			if ($PixGallery_RSSFeedLinkType == "1"){
			$link = $wp_pxgadmin_url.$img['Imagesortpath'];
			$enclosurelink = $wp_pxgadmin_url.$img['Imagesortpath'];
			}
			else {
			$link = $img['RSSPageLink'];
			$enclosurelink = $wp_pxgadmin_url.$img['Imagesortpath'];
			}
		}

		$name = str_replace("-", " ", $img['Name']); 
		$name = preg_replace('/\..+$/', '', $name);
		$name = strtoupper(substr($name,0,1)).substr($name,1,strlen($name));

		fwrite($file,  '<item rdf:about="'.$link.'">
				<title>'.$name.'</title>
				<link>'.$link.'</link>
				<dc:date>'.date('Y-m-d\\TH:i:s+00:00',$img['FileTimeCreated']).'</dc:date>
				<dc:language>en</dc:language>
				<dc:creator>'.$PixGallery_RSSCreatorEntry.'</dc:creator>
				<dc:subject></dc:subject>
				
				<content:encoded><![CDATA[<img src="'.$enclosurelink.'" alt="'.$name.'" />]]></content:encoded>');
		if ($img['mimetype'] == ".gif") {
			$enclosuretype = "image/gif";
			}
		elseif ($img['mimetype'] == ".jpg") {
			$enclosuretype = "image/jpeg";
			}
		elseif ($img['mimetype'] == ".png") {
			$enclosuretype = "image/png";
			}
		else {
		}
		if ($PixGallery_RSSPhotoFeed != 1){
		fwrite($file, '<description>&lt;a href="'.$link.'" &gt;
              &lt;img align="left" src="'.$enclosurelink.'" &gt;</description>');
              }
         else {
         fwrite($file, '<description></description>');
              }
		fwrite($file,  '<enclosure url="'.$enclosurelink.'" type="'.$enclosuretype.'" size="'.$img['filesize'].'" />');
		fwrite($file,  '</item>');
		$cpt++;
		if($cpt >= $PixGallery_RSSFeedNumber) break;
	}
	fwrite($file, "</rdf:RDF>");
	fclose($file);
   }
}




function PixGallery_PhotoGallery_CollectionGetContent($Path)
{
	global $WebsiteRoot, $GalleryPath, $CollectionPath, $awsom_pixgallery_gallery_table_name, $awsom_pixgallery_caption_table_name;
	global $QueryPathName, $WebsiteActualPath, $PixGallery_SortType, $customdirimage, $wpdb;

	if ($Path[strlen($Path) - 1] != '/')
		$Path .= "/";

	$ContentList	= null;
	$RealPath		= $WebsiteRoot.$GalleryPath.$Path;
	$RealPath		= str_replace("//", "/", $RealPath);

	if (is_dir($RealPath))
		{
		if ($DirHandle = opendir($RealPath))
			{
			while (($FileEntry = readdir($DirHandle)) !== FALSE)
				{

				if ($FileEntry == '.' || $FileEntry == '..')
					continue;



				$Content				= null;
				$Content['Name']		= $FileEntry;
		// ***** Awsom Mod create File creation time index ***** //
				$Content['FileTimeCreated'] = filemtime($RealPath.$FileEntry);
		// ***** end Awsom File Create time index ***** //
				if (is_dir($RealPath.$FileEntry))
					$FileEntry .= '/';
				$pxg_page_link = get_permalink();
				$pxg_querystring_test = strpos($pxg_page_link, '?');
				if ($pxg_querystring_test === false) {
				$pxg_page_link .= "?";
					}
				$Content['RSSPageLink'] = $pxg_page_link."&amp;px=".$CollectionPath.$FileEntry;
				$Content['ShareThisLink'] = $pxg_page_link."&amp;px=".$CollectionPath;
				$Content['Path']		= $Path.$FileEntry;
				$Content['FullPath']	= $RealPath.$FileEntry;
				$Content['Link']		= $FileEntry;
//Debug zone //
//PixGallery_PrintError("Error: Unable to open path [$Path]!");
//PixGallery_PrintError("Error: Unable to open Realpath [$RealPath]!");
//PixGallery_PrintError("Error: Unable to open gallerypath [$GalleryPath]!");
//PixGallery_PrintError("Error: Unable to open fullpath [$RealPath$FileEntry]!");
//PixGallery_PrintError("Error: Unable to open imagepath [$GalleryPath$Path$FileEntry]!");
//debug zone end //

				$Extension = PixGallery_UrlGetFileExtension($Content['FullPath']);
				$Extension = strtolower($Extension);

				if (is_dir($Content['FullPath'])){
					$Content['Type'] = 'Directory';
					$imagesortpath = $GalleryPath.$Path.$FileEntry;
					$imagesortpath = str_replace("//", "/", $imagesortpath);
					$Content['Imagesortpath'] = $imagesortpath;
					$Content['sortorder'] = $wpdb->get_var("SELECT sortid FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$imagesortpath'");
					}
				else if ($Extension == '.gif' || $Extension == '.jpg' || $Extension == '.png'){
					$Content['Type'] = 'Picture';
					//$imagesortpath	= $Content['Name'];
					$imagesortpath = $GalleryPath.$Path.$FileEntry;
					$imagesortpath = str_replace("//", "/", $imagesortpath);
					$Content['Imagesortpath'] = $imagesortpath;
					$Content['filesize'] = filesize ($RealPath.$FileEntry);
					$Content['mimetype'] = $Extension;
					$Content['sortorder'] = $wpdb->get_var("SELECT sortid FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$imagesortpath'");
					}
				else
					continue;

				//if ( $Content['sortorder'] == ""){
				//$Content['sortorder'] = 0;
				//}


				$ContentList[]	= $Content;
				$Count			= count($ContentList) - 1;

				if (isset($ContentList[$Count - 1]['Link']))
					{
					$ContentList[$Count]['Previous'] = $ContentList[$Count - 1]['Link'];
					$ContentList[$Count - 1]['Next'] = $ContentList[$Count]['Link'];
					}
				}
			}
		else
			PixGallery_PrintError("Error: Unable to open path [$Path]!");

		closedir($DirHandle);
		}
// ***** AWSOM Sort Mod start ***** //
// ***** Note: I could have put this sort in a function elsewhere to make things more like how the plugin itself is set up, but by keeping //
// ***** things together here it is easier to move this area to a new version of this plugin if it gets updated. //



If (is_array($ContentList)){
	If ($PixGallery_SortType == 0){
			$ReversedContentList = $ContentList;
			usort($ReversedContentList, create_function('$a,$b','return strnatcasecmp($a[FileTimeCreated],$b[FileTimeCreated]);'));
			$ReversedContentList = array_reverse($ReversedContentList);
			$i = 0;
			$x = count($ReversedContentList);
			unset($ReversedContentList[$i]['Previous']);
			unset($ReversedContentList[$x - 1]['Next']);
			while ($i < $x){
			if (isset($ReversedContentList[$i - 1]['Link'])){
			$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
			$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
			}
			$i++;
			}
		$ContentList = $ReversedContentList;
	}
	If ($PixGallery_SortType == 1){
		$ReversedContentList = $ContentList;
		usort($ReversedContentList, create_function('$a,$b','return strnatcasecmp($a[FileTimeCreated],$b[FileTimeCreated]);'));
		//$ReversedContentList = array_reverse($ReversedContentList);
		$i = 0;
		$x = count($ReversedContentList);
		unset($ReversedContentList[$i]['Previous']);
		unset($ReversedContentList[$x - 1]['Next']);
		while ($i < $x){
		if (isset($ReversedContentList[$i - 1]['Link'])){
		$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
		$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
		}
		$i++;
		}
	$ContentList = $ReversedContentList;
	}
	If ($PixGallery_SortType == 2){
			$ReversedContentList = $ContentList;
			usort($ReversedContentList, create_function('$a,$b','return strcasecmp($a[Name],$b[Name]);'));
			//usort($ReversedContentList, create_function('$a,$b','return strcasecmp(filemtime($a[2]),filemtime($b[2]));'));
			//natsort($ReversedContentList );
			$i = 0;
			$x = count($ReversedContentList);
			unset($ReversedContentList[$i]['Previous']);
			unset($ReversedContentList[$x - 1]['Next']);
			while ($i < $x){
			if (isset($ReversedContentList[$i - 1]['Link'])){
			$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
			$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
			}
			$i++;
			}
		$ContentList = $ReversedContentList;
	}
	If ($PixGallery_SortType == 3){
			$ReversedContentList = $ContentList;
			usort($ReversedContentList, create_function('$a,$b','return strcasecmp($a[Name],$b[Name]);'));
			$ReversedContentList = array_reverse($ReversedContentList);
			$i = 0;
			$x = count($ReversedContentList);
			unset($ReversedContentList[$i]['Previous']);
			unset($ReversedContentList[$x - 1]['Next']);
			while ($i < $x){
			if (isset($ReversedContentList[$i - 1]['Link'])){
			$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
			$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
			}
			$i++;
			}
		$ContentList = $ReversedContentList;
	}
	If ($PixGallery_SortType == 4){
				$ReversedContentList = $ContentList;
				usort($ReversedContentList, create_function('$a,$b','return strnatcasecmp($a[sortorder],$b[sortorder]);'));
				//$ReversedContentList = array_reverse($ReversedContentList);
				$i = 0;
				$x = count($ReversedContentList);
				unset($ReversedContentList[$i]['Previous']);
				unset($ReversedContentList[$x - 1]['Next']);
				while ($i < $x){
				if (isset($ReversedContentList[$i - 1]['Link'])){
				$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
				$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
				}
				$i++;
				}
			$ContentList = $ReversedContentList;
	}

If ($PixGallery_SortType == 5){
				$ReversedContentList = $ContentList;
				usort($ReversedContentList, create_function('$a,$b','return strnatcasecmp($a[sortorder],$b[sortorder]);'));
				$ReversedContentList = array_reverse($ReversedContentList);
				$i = 0;
				$x = count($ReversedContentList);
				unset($ReversedContentList[$i]['Previous']);
				unset($ReversedContentList[$x - 1]['Next']);
				while ($i < $x){
				if (isset($ReversedContentList[$i - 1]['Link'])){
				$ReversedContentList[$i]['Previous'] = $ReversedContentList[$i - 1]['Link'];
				$ReversedContentList[$i - 1]['Next'] = $ReversedContentList[$i]['Link'];
				}
				$i++;
				}
			$ContentList = $ReversedContentList;
	}
}
// ***specific mod to remove custom directory image from view***//
	$RemoveDirectoryImageList = $ContentList;
	$hidecustomimage = "";
	$hidecustomimage = $customdirimage;

	$i = 0;
	$x = count($RemoveDirectoryImageList);
	$ImageCountNum = 0;
	unset($RemoveDirectoryImageList[$i]['Previous']);
	unset($RemoveDirectoryImageList[$x - 1]['Next']);
	while ($i < $x){
	if (stristr($RemoveDirectoryImageList[$i]['Name'], $hidecustomimage)){
	$ImageCountNum = 1;
	}
	else{
	$RemoveDirectoryImageList[$i]['ImageCountNumber'] = ($i + 1) - $ImageCountNum;
	}
	$hidecustomimage = "";
	$hidecustomimage = $customdirimage;
	if (isset($RemoveDirectoryImageList[$i - 1]['Link'])){
		if (stristr($RemoveDirectoryImageList[$i]['Name'], $hidecustomimage)){
		unset($RemoveDirectoryImageList[$i - 1]['Next']);
		}
		else {
			if ((stristr($RemoveDirectoryImageList[$i - 1]['Name'], $hidecustomimage)) && isset($RemoveDirectoryImageList[$i - 2]['Link'])) {
				$RemoveDirectoryImageList[$i]['Previous'] = $RemoveDirectoryImageList[$i - 2]['Link'];
				$RemoveDirectoryImageList[$i - 2]['Next'] = $RemoveDirectoryImageList[$i]['Link'];
			}
			elseif ((stristr($RemoveDirectoryImageList[$i - 1]['Name'], $hidecustomimage)) && !isset($RemoveDirectoryImageList[$i - 2]['Link'])) {
			unset($RemoveDirectoryImageList[$i]['Previous']);
			}
			else {
			$RemoveDirectoryImageList[$i]['Previous'] = $RemoveDirectoryImageList[$i - 1]['Link'];
			$RemoveDirectoryImageList[$i - 1]['Next'] = $RemoveDirectoryImageList[$i]['Link'];
			}
		}
	}
	$i++;
	}
	$ContentList =	$RemoveDirectoryImageList;
// **end specific mod to remove directory image from view***//
// ***** end AWSOM sort mod ***** //
return $ContentList;
}

function PixGallery_PhotoGallery_CollectionGetContentParent($Path)
{
	if ($Path[strlen($Path) - 1] == '/')
		$Path = substr($Path, 0, strlen($Path) - 1);

	$LastSlash = strrpos($Path, "/");

	if ($LastSlash == FALSE)
		return "/";

	return substr($Path, 0, $LastSlash + 1);
}

function PixGallery_PhotoGallery_CollectionGetDirectoryImage($Path)
{
	global $CollectionPath, $customdirimage;

	$Content = PixGallery_PhotoGallery_CollectionGetContent($CollectionPath.$Path);

	if ($Content === FALSE)
		return FALSE;

// ****AWSOM mod for custom Directory Image file selection version 1.0, better one to follow soon**** //

	for ($i = 0; $i != count($Content); $i += 1)
		{
		$ThisImageName = PixGallery_UrlGetFileTitle($Content[$i]['Name']);
			if ($ThisImageName == $customdirimage)
				return $Content[$i]['Path'];
		}

// *****AWSOM mod end***** //
	for ($i = 0; $i != count($Content); $i += 1)
		{
		if ($Content[$i]['Type'] == 'Picture')
			return $Content[$i]['Path'];
		}

	for ($i = 0; $i != count($Content); $i += 1)
		{
		if ($Content[$i]['Type'] == 'Directory')
			{
			$DirContent = PixGallery_PhotoGallery_CollectionGetContent($CollectionPath.$Content[$i]['Path']);

			if ($DirContent !== FALSE)
				{
				if (is_array($DirContent) == FALSE)
					return $DirContent;


				for ($x = 0; $x != count($DirContent); $x += 1)
					{
					if ($DirContent[$x]['Type'] == 'Picture')
						return $DirContent[$i]['Path'];
					}
				}
			}
		}

	return FALSE;
}

function PixGallery_PhotoGallery_Collection()
{
	global $CollectionPath, $GalleryPath, $QueryPathName;
	global $PixGallery_ImageWidth, $PixGallery_ImageHeight, $PixGallery_Columns;
	global $PixGallery_Rows, $PixGlobal_Path, $PixFolder_Path,$PixGlobal_AdminPath;
	global $WebsiteRoot, $WebsitePath, $WebsiteActualPath, $QueryPathName, $QueryStartName;
	global $PixGallery_SquareThumbnails, $PixGallery_RootCache, $PixGallery_UseAbsPath, $PixGallery_ImageViewNums, $wp_pxg_url, $wp_pxgadmin_url;
//**Mod for AWSOM News Link and Remove _ from file name addition//
	global $PixGallery_LinkToNewsPost, $PixGallery_RemoveUnderscoresFromName, $PixGallery_ImageCaption, $PixGallery_HideSidebar, $PixGallery_MaxThumbnailsOnIndex, $wpdb, $PixGallery_CustomCSS, $PixGallery_LegacyMode, $post, $PixGallery_DebugModeType;
	global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $PixGallery_OpenInNewWindow, $customdirimage, $PixGallery_NoThumbnailText, $PixGallery_WatermarkImages, $PixGallery_ExtraNavBars, $PixGallery_ShowBreadCrumb, $PixGallery_ExcludeNewsAuthor;
	global $PixgalleryDisplayingImage, $Pixgalleryitempath, $PixgalleryReturnPath, $PixGallery_Displayexif, $PixGallery_DebugMode, $PixGallery_LimitPaginationLinks, $PixGallery_CustomPopup, $PixGallery_CustomRel, $PixGallery_Displayiptc, $PixGallery_DisplayRSSLink, $PixGallery_DisplayRSS,$PixGallery_AdminFix,$PixGallery_ImageClickBehavior,$PixGallery_UseFolderIconCustom,$PixGallery_UseFolderIcon,$PixGallery_ShareThisConnector;
//**end Mod //

//***** Fix For Admin error in some conflicting plugins //
    if (is_admin() && $PixGallery_AdminFix == 1) {
    return;
   }
//***** end Admin error fix //

//***** Get "All Image Text" if it exists //
$Pixgallery__All_Image_Text = get_option( 'awsom_pixgallery_all_images_text');
//***** 
	$Pixgallery_Custom_Language_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if (file_exists($Pixgallery_Custom_Language_Location)){
	require WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if ($PixGallery_Lang_File_Version < 2) {
	PixGallery_PrintError("Error: Pixgallery has new language elements, please copy and update the newest pixgallery_lang.php file to your awsom_customizations folder to display all text properly in your galleries.");
		}
	}
	else {
	require WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/pixgallery_lang.php';}
	
	if (isset($_REQUEST[$QueryPathName]))
		$CollectionPath = urldecode($_REQUEST[$QueryPathName]);
	if ($PixGallery_CustomPopup == "1"){
	$PixGalleryUseRel = $PixGallery_CustomRel;
	}
	else {
	$PixGalleryUseRel = "PixGallery";
	}

	$FullPath = $WebsiteRoot.$GalleryPath.$CollectionPath;
	$FullPath = str_replace("//", "/", $FullPath);
	
	if (strpos($CollectionPath, "..") !== FALSE || !file_exists($FullPath))
		{
		$FullPath = htmlspecialchars($FullPath);
		if ( function_exists('current_user_can') && current_user_can('manage_options') ) {
			$errortodisplay = "Error: Path does not exist: ".$FullPath;
			}
		else {
			$errortodisplay = "Error: Path does not exist";
			}
		//PixGallery_PrintError("Error: Path does not exist [$FullPath]!");
		PixGallery_PrintError($errortodisplay);
		return $Output;
		}

	$Output					= "";
	$ContentIsFile			= FALSE;
	$ContentParentIndex		= 0;
	$ContentParentPath		= PixGallery_PhotoGallery_CollectionGetContentParent($CollectionPath);
	$ContentParent			= PixGallery_PhotoGallery_CollectionGetContent($ContentParentPath);
	$ContentParentUrl		= PixGallery_PhotoGallery_CollectionGetContentParent($CollectionPath);
	$Content				= PixGallery_PhotoGallery_CollectionGetContent($CollectionPath);
	$CollectionRootPath		= PixGallery_UrlGetPath($CollectionPath);
	$ContentParentUrl		= PixGallery_UrlBuild($QueryPathName, $ContentParentUrl);

		if (is_file($FullPath))
		{
		$ContentIsFile	= TRUE;
		// ***AWSOM mod for hide sidebar Option****//
		if ($PixGallery_HideSidebar == 1) {
		$Output			.= "<style>#sidebar { display: none; visibility: hidden; }</style>";
			}
		else{
		$Output			.= "<style>#sidebar ul li { text-align: left; }</style>";
		}
		// ****end mod*** //
		}

	$ContentList = null;

	for ($i = 0; $i != count($ContentParent); $i += 1)
		{
		if ($ContentParent[$i]['Path'] == $CollectionPath)
			{
			$ContentParentIndex = $i;

			if ($ContentIsFile == TRUE)
				$ContentList[] = $ContentParent[$i];

			break;
			}
		}
//****AWSOM Mod to exclude Custom Directory image Name from view****//
	for ($i = 0; $i != count($Content); $i += 1){
		$ThisImageName = PixGallery_UrlGetFileTitle($Content[$i]['Name']);
			if ($ThisImageName !== $customdirimage){
			$ContentList[] = $Content[$i];
			}
		}

//****End AWSOM mod****//

	if ($ContentIsFile == TRUE){
		$Output .= "<div id=\"PxgGalleryWrapper\" style='$PixGallery_CustomCSS'><div class='PxgNavigation'>";
		}
	else {
		$Output .="<div id=\"PxgGalleryWrapper\" style='$PixGallery_CustomCSS'>";
		}

	if ($CollectionPath != "/")
		{
		$ParentName = PixGallery_UrlGetFileTitle($GalleryPath);

		if ($ParentName == "")
			{
			$ParentName = PixGallery_UrlGetFileTitle(trim(trim($GalleryPath, "/"), "\\"));

			if ($ParentName == "")
				$ParentName = "Root";
			}
	// *****AWSOM mod to get Gallery Custom name if exist ******//
		$getgallerycustomnameforbreadcrumb = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galpath ='$GalleryPath'");
		if ($getgallerycustomnameforbreadcrumb){
		$getgallerycustomnameforbreadcrumb = stripslashes($getgallerycustomnameforbreadcrumb);
		$ParentName = $getgallerycustomnameforbreadcrumb;
		}
		//******End Mod *******//
		$ParentName = ucwords(strtolower($ParentName));

		//$Output	.= "<a href='$ContentParentUrl'>$ParentName</a> &rsaquo; ";
		$ContentOverallParent = get_permalink();
		if ($PixGallery_ShowBreadCrumb !=="1"){
		$Output	.= "<a href='$ContentOverallParent'>$ParentName</a> ".$PixGallery_Lang_Output_Frontend['breadcrumb_divider']." ";
		}
		$PathParts = explode('/', $CollectionPath);
		$AccumPath = "";

		for ($i = 0; $i != count($PathParts); $i += 1)
			{

			$AccumPath .= $PathParts[$i];

			if (($i + 1) != count($PathParts))
				
				$AccumPath .= "/";
			$Link = PixGallery_UrlBuild($QueryPathName, $AccumPath);
			//******* AWSOM mod to add Custom Image/gallery Name in Breadcrumb *******//
			$customnamepathmatch = $GalleryPath.$CollectionPath;
			$customnamepathmatch = str_replace("//", "/", $customnamepathmatch);
			
			if ($PixGallery_LegacyMode == "1"){
			$ShowCustomImageNameInBreadcrumb = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE name ='$PathParts[$i]'");
			$ShowCustomSubGalNameInBreadcrumb = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galleryname ='$PathParts[$i]'");
			}
			else {
		    $ShowCustomImageNameInBreadcrumb = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE imagepath ='$customnamepathmatch' and name ='$PathParts[$i]'");
			
			}

			if ($ShowCustomImageNameInBreadcrumb !="" ) {
			$ShowCustomImageNameInBreadcrumb = stripslashes($ShowCustomImageNameInBreadcrumb);
				if ($PixGallery_ShowBreadCrumb !=="1"){
			$Output .=  "<a href='$Link'>".ucwords(strtolower($ShowCustomImageNameInBreadcrumb))."</a>";
			
				}
			
			}
			
			else {
				if ($PixGallery_ShowBreadCrumb !=="1"){
				$pxgnoncustomnameoutput = $PathParts[$i];
				
			if ($PathParts[$i] !== ""){
			$customnamesearchpath = $GalleryPath.$AccumPath;
			$customnamesearchpath = str_replace("//", "/", $customnamesearchpath);
			
			$ShowCustomSubGalNameInBreadcrumb = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$customnamesearchpath'");
			if ($ShowCustomSubGalNameInBreadcrumb !== null){
			$ShowCustomSubGalNameInBreadcrumb = stripslashes($ShowCustomSubGalNameInBreadcrumb);	
			$pxgnoncustomnameoutput = $ShowCustomSubGalNameInBreadcrumb;
							
				}
			$Output .=  "<a href='$Link'>".ucwords(strtolower($pxgnoncustomnameoutput))."</a>";
				}
				}
			}
			if (($i != 0) && ($i + 1) != count($PathParts)){
					if ($PixGallery_ShowBreadCrumb !=="1"){
				$Output .= " ".$PixGallery_Lang_Output_Frontend['breadcrumb_divider']." ";
					}
				}
			}
			if ($PixGallery_ShowBreadCrumb !=="1"){
		$Output .= "<br />";
				}
		}

	if (($CollectionPath != "/") && ($ContentParentUrl != ""))
		{
		if ($ContentIsFile == TRUE)
			{
			if (isset($ContentList[0]['Previous']))
				$PreviousLink = PixGallery_UrlBuild($QueryPathName, $CollectionRootPath.$ContentList[0]['Previous']);
			if (isset($ContentList[0]['Next']))
				$NextLink = PixGallery_UrlBuild($QueryPathName, $CollectionRootPath.$ContentList[0]['Next']);
			}
		else
			{
			if (isset($ContentParent[$ContentParentIndex]['Previous']))
				{
				$PreviousLink = PixGallery_UrlBuild($QueryPathName, $ContentParentPath.$ContentParent[$ContentParentIndex]['Previous']);
				$PreviousPath = $WebsiteRoot.$GalleryPath.$ContentParent[$ContentParentIndex]['Previous'];
				}
			if (isset($ContentParent[$ContentParentIndex]['Next']))
				{
				$NextLink = PixGallery_UrlBuild($QueryPathName, $ContentParentPath.$ContentParent[$ContentParentIndex]['Next']);
				$NextPath = $WebsiteRoot.$GalleryPath.$ContentParent[$ContentParentIndex]['Next'];
				}
			}

		if (is_file($FullPath))
			{

			if (isset($PreviousLink))
				$Output .= " <center><a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_image']."</a> ";
			else
				$Output .= " <center>".$PixGallery_Lang_Output_Frontend['previous_image']." ";

			$ThisImageInCount = $ContentList[0]['ImageCountNumber'];
			$Output .= $PixGallery_Lang_Output_Frontend['next_previous_image_divider'].$PixGallery_Lang_Output_Frontend['image_number_in_gallery']." $ThisImageInCount ".$PixGallery_Lang_Output_Frontend['next_previous_image_divider'];
			if (isset($NextLink)){
			//*****AWSOM MOD ADDITION******
				$Output .= " <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_image']."</a>";
				}
				else {
				$Output .= " ".$PixGallery_Lang_Output_Frontend['next_image']."";
				}


			$Output .= "</center>";
			//****END AWSOM MOD ADDITION******
			}
		else
			{

			if (isset($PreviousLink) && is_dir($PreviousPath))
				$Output .= "<center> <a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_archive']."</a>";
			if (isset($NextLink) && is_dir($NextPath)){
					if (isset($PreviousLink) && is_dir($PreviousPath)){
					$Output .= $PixGallery_Lang_Output_Frontend['next_previous_archive_divider']."<a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
					}
					else {
					$Output .= "<center> <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
					}
				}
			$Output .= "</center>";
			}
		}

	if ($ContentIsFile == TRUE)
		$Output .= "&nbsp;</div><br />";
	// *****AWSOM mod to read Gallery Caption from DB instead of file ******//
	//$ReadmeFile = $WebsiteRoot."/".$GalleryPath.$CollectionPath."/readme.txt";

	$ReadmeFile = $GalleryPath. $CollectionPath;
	$ReadmeFile = str_replace("//", "/", $ReadmeFile);
	$ReadMeFromDB = $wpdb->get_var("SELECT galcaptiontext FROM $awsom_pixgallery_gallery_table_name WHERE galpath ='$ReadmeFile'");
	//$ReadmeText = "";

	//if (file_exists($ReadmeFile))
		//$ReadmeText = file_get_contents($ReadmeFile);

	if ($ReadMeFromDB != ""){
	$ReadMeFromDB = stripslashes($ReadMeFromDB);
		$Output .= $ReadMeFromDB."<br /><br />";
		}

		# Patch sbilbeau : generate rss feed
			if ($ContentIsFile !== TRUE && $PixGallery_DisplayRSS == 1){
				PixGallery_Feed($Content, $ReadmeFile,$ContentParentUrl); }

	$Output			.= "\n<!-- Start AWSOM Pixgallery Main Table --><table class='PxgMasterTable'><tr><td><table class='PxgGalleryTable'><tr>\n";
	$Rows			= 0;
	$Columns		= 0;
	$ColumnWidth	= floor(100 / $PixGallery_Columns)."%";
	$StartImage		= 0;

	if (isset($_REQUEST[$QueryStartName]))
		$StartImage = intval(urldecode($_REQUEST[$QueryStartName]));

	if ($StartImage < 0 || $StartImage > count($ContentList))
		$StartImage = 0;

	$MaxColumns = $PixGallery_Columns;

	//*****AWSOM mod for limited number of thumbnails displayed in post *****//
	$displayNumberOfThumbnails = count($ContentList);
	if (!is_page() && !is_single()){
		if ($PixGallery_MaxThumbnailsOnIndex !=="" && $PixGallery_MaxThumbnailsOnIndex !=="0"){
		 	if ($displayNumberOfThumbnails > $PixGallery_MaxThumbnailsOnIndex){
			$displayNumberOfThumbnails = $PixGallery_MaxThumbnailsOnIndex;
			}
		}
	}
	//*****End mod *****//
if ($ContentIsFile !== TRUE){
			$PixgalleryDisplayingImage = "gallery";
			$Pixgalleryitempath = $ReadmeFile;
			if ( function_exists('current_user_can') && current_user_can('manage_options') ) {
						$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/themes.php?page=pixgallery.php\">";
				}
				}


	for ($i = $StartImage; $i != $displayNumberOfThumbnails; $i += 1)
		{
		$ContentName		= $ContentList[$i]['Name'];
		$addimagepathincaption = $ContentList[$i]['Imagesortpath'];
		$ContentSortNumber = $ContentList[$i]['sortorder'];
		$ContentTitle		= ucfirst(PixGallery_UrlGetFileTitle($ContentName));
		$ContentSize		= "";
		$ContentExtension	= "";

		if ($ContentList[$i]['Type'] == 'Directory')
			{
			$Image				= PixGallery_PhotoGallery_CollectionGetDirectoryImage($ContentName);
			$ImagePath			= $WebsiteActualPath.$GalleryPath.$Image;
			$ImageRealPath		= $WebsiteRoot.$GalleryPath.$Image;
			$ContentType		= "Gallery";

			if ($Image === FALSE)
				continue;
			}
		else
			{
			$Image				= $ContentList[$i]['Path'];
			$ImagePath			= $WebsiteActualPath.$GalleryPath.$Image;
			$ImageRealPath		= $WebsiteRoot.$GalleryPath.$Image;

			$ContentExtension	= str_replace(".", "", PixGallery_UrlGetFileExtension($ContentName));
			$ContentSize		= @filesize($ImageRealPath)." B";
			$ContentType		= "Photo";
			}

		$ImageUrl		= PixGallery_UrlBuild($QueryPathName, $ContentList[$i]['Path']);
		$ImageNewSize	= PixGallery_ImageCalculateSizeEx($ImageRealPath, $ContentIsFile);
		$ImageWidth		= $ImageNewSize['Width'];
		$ImageHeight	= $ImageNewSize['Height'];
		$ImageAlt		= $ContentTitle;

//***** mod to remove _ from image file name *****//
		if ($PixGallery_RemoveUnderscoresFromName == 1){
		$titleforimage = str_replace ("_", " ", $ImageAlt);
		$ContentTitle = $titleforimage;
		}
		else {
		$titleforimage = $ImageAlt;
		}
		//***** end remove _ from image file name mod ****//
//*** AWSOM Image view tracking mod ***//
		if ($ContentIsFile == TRUE) {
		$ImageViewNumberData = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'");
		if ($ImageViewNumberData){
		$wpdb->query("UPDATE $awsom_pixgallery_caption_table_name SET viewnum=viewnum+1 WHERE capid ='$ImageViewNumberData'");
			}
		if ($PixGallery_ImageViewNums == 1 && $ImageViewNumberData){
		$ShowCurrentImageViewCount = "<br />".$PixGallery_Lang_Output_Frontend['image_view_count_text'];
		$ShowCurrentImageViewCount .= $wpdb->get_var("SELECT viewnum FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'")."<br />";
			}
		}
//***** mod to use Custom Name for image if it exists *****//
		if ($ContentType != "Gallery"){
		if ($PixGallery_LegacyMode == "1"){
		$ImageCustomName = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE name = '$ContentName'");

		}
		else {
		$ImageCustomName = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'");
		}
				if ($ImageCustomName !="") {
				$titleforimage = stripslashes($ImageCustomName);
		$ContentTitle = stripslashes($ImageCustomName);
		$ShowAlteredNameAnyway = "1";
				}
		}
//***** end mod for Custom Name for image ******//
//***** mod to use Custom Name for directory if it exists *****//
		if ($ContentType != "Picture"){
		
		$galleryCustomName = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$addimagepathincaption'");
	
			if ($galleryCustomName !="") {
				$titleforimage = stripslashes($galleryCustomName);
		$ContentTitle = stripslashes($galleryCustomName);
		$ShowAlteredNameAnyway = "1";
				}
		}
//***** end mod for Custom Name for directory ******//
//***** mod for image captions *****//
if ($PixGallery_ImageCaption == 1){
	if ($PixGallery_LegacyMode == "1"){
	$ImageCaptionData = $wpdb->get_var("SELECT captiontext FROM $awsom_pixgallery_caption_table_name WHERE name = '$ContentName'");

}
else{
$ImageCaptionData = $wpdb->get_var("SELECT captiontext FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'");
}
		if ($ImageCaptionData !="") {
		$ImageCaptionData = stripslashes($ImageCaptionData);
		$captionforimage = "<br />". $ImageCaptionData;

		}
		}
//PixGallery_GetCaption ($ImageAlt);
//***** end image captions mod *****//

		$ImageUrl		= str_replace("//", "/", $ImageUrl);
		$ImagePath		= str_replace("//", "/", $ImagePath);

		$PopupOnThumbnails = isset($GLOBALS["PixGallery_PopupOnThumbnail"]);
//**Mod for AWSOM News Link  //
		if ($PixGallery_LinkToNewsPost == 1){

			if ($PixGallery_ExcludeNewsAuthor !="" && $PixGallery_ExcludeNewsAuthor !="0" && $PixGallery_ExcludeNewsAuthor != NULL){
				$newspostlinks = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_date LIKE '%$ImageAlt%' and post_status = 'publish' and post_author NOT IN ($PixGallery_ExcludeNewsAuthor) and post_type = 'post'");
				}
			else {
				$newspostlinks = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_date LIKE '%$ImageAlt%' and post_status = 'publish' and post_type = 'post'");
				}
		if ($newspostlinks) {
		$newslink .= "<br />".$PixGallery_Lang_Output_Frontend['news_post_link_text']."<br />";
		foreach ($newspostlinks as $newspostlink) {
		$newslink .= "<a href=\"$newspostlink->guid\"> $newspostlink->post_title</a><br />";
		}
		}
		}
		//**End  News Link Mod**//
		//**Mod for Watermark redirect link to cache folder for images**//
		if ($ContentIsFile == TRUE && $PixGallery_WatermarkImages == 1){
			$ImagePath = $PixGallery_RootCache."/".md5($ImagePath).".".$ContentExtension;
			if ($PixGallery_UseAbsPath == "1"){
				$ImagePath = get_bloginfo('wpurl')."/".$ImagePath;
				}
			}
		//** End Watermark mod **//
		//** EXIF/IPTC Data Mod **//
		if ($ContentIsFile == TRUE && $PixGallery_Displayexif == 1){
		$er = new phpExifReader($ImageRealPath);
		$pxgexifdata = $er->getImageInfo();
		$pxgexifdataoutput = $PixGallery_Lang_Output_Frontend['exif_display_text']."<br /><p style='text-align:left;'>";
		while (list ($key, $val) = each ($pxgexifdata)) {
		$pxgexifdataoutput.= "$key : $val <br>";
		}
		$pxgexifdataoutput.= "</p>";
		}

		if ($ContentIsFile == TRUE && $PixGallery_Displayiptc == 1){
		$size = GetImageSize ($ImageRealPath, $iptcinfo);
		$iptc = iptcparse($iptcinfo["APP13"]);
$pxgiptcdataoutput .= $PixGallery_Lang_Output_Frontend['iptc_display_text']."<br /><p style='text-align:left;'>";
foreach($iptc as $key => $value)
{
	if ($key == "2#116" || $key == "2#120" || $key == "2#080"){
	if ($key == "2#116"){
	$key = "<b>Copyright:</b>";
	}
	if ($key == "2#120"){
	$key = "<b>Caption:</b>";
	}
	if ($key == "2#080"){
	$key = "<b>Writer:</b>";
	}

    $pxgiptcdataoutput.= "$key ";
    foreach($value as $innerkey => $innervalue)
    {
        if( ($innerkey+1) != count($value) )
            $pxgiptcdataoutput.= "$innervalue <br />";
        else
            $pxgiptcdataoutput.= "$innervalue <br />";
    }
    }
}
$pxgiptcdataoutput.= "</p>";
		}
		//**end EXIF mod **//

		//**next line modded for AWSOM to add $newslink to output and do popup window if activated//
		if ($ContentIsFile == TRUE && $PixGallery_OpenInNewWindow == "1")
			{
			if ($PixGallery_ImageCaption != "1" && $ShowAlteredNameAnyway !="1"){
			$titleforimage = "";
			}
				$ImagePathOutput = $ImagePath;
				
			$Output .= "<td><center><a href=\"$ImagePathOutput\" title=\"$ImageAlt\" rel='$PixGalleryUseRel' target=\"_blank\"><img border=0 src=\"$ImagePath\" width='$ImageWidth' class='PxgImage' alt=\"$ImageAlt\"></a><p style=''> $titleforimage</center> $captionforimage <center>$newslink $ShowCurrentImageViewCount $pxgexifdataoutput $pxgiptcdataoutput</p></center>";
			if ($Pixgallery__All_Image_Text !=""){
			$Pixgallery__All_Image_Text = stripslashes($Pixgallery__All_Image_Text);
			$Output .= "<br /><p style='text-align:left;'>".$Pixgallery__All_Image_Text."</p>";
			}
			$PixgalleryDisplayingImage = "image";
			$Pixgalleryitempath = $ContentList[$i]['Imagesortpath'];
				//**Display bottom Nav bar if setting is turned on **//
		if ($PixGallery_ExtraNavBars == "1"){
				if (is_file($FullPath))
				{
				if (isset($PreviousLink))
				$Output .= " <center><a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_image']."</a> ";
			else
				$Output .= " <center>".$PixGallery_Lang_Output_Frontend['previous_image']." ";

			$ThisImageInCount = $ContentList[0]['ImageCountNumber'];
			$Output .= $PixGallery_Lang_Output_Frontend['next_previous_image_divider'].$PixGallery_Lang_Output_Frontend['image_number_in_gallery']." $ThisImageInCount ".$PixGallery_Lang_Output_Frontend['next_previous_image_divider'];
			if (isset($NextLink)){
			//*****AWSOM MOD ADDITION******
				$Output .= " <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_image']."</a>";
				}
				else {
				$Output .= " ".$PixGallery_Lang_Output_Frontend['next_image'];
				}
			$Output .= "</center>";
			//****END AWSOM MOD ADDITION******
			}
		else
			{
			if (isset($PreviousLink) && is_dir($PreviousPath))
				$Output .= "<center> <a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_archive']."</a>";
			if (isset($NextLink) && is_dir($NextPath)){
					if (isset($PreviousLink)){
					
					$Output .= $PixGallery_Lang_Output_Frontend['next_previous_archive_divider']."<a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
					}
					else {
					$Output .= "<center> <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
					}
				}
			$Output .= "</center></p></td>\n";
			}
		}
		else {
		$Output .="</td>\n";
		}
			//** end bottom nav bar display area **//

				if ( function_exists('current_user_can') && current_user_can('manage_options') ) {

								if ($PixGallery_LegacyMode == "1"){
								$PxgImageEditID = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE name = '$ContentName'");
								}
								else {
								$PxgImageEditID = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'");
								}
									if (!isset($PxgImageEditID)){
									$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/edit.php?page=pixgallery.php\">";
									$Output .="<input type=\"hidden\" name=\"editlinkimagename\" value=\"$ContentName\" />";
									$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
									$Output .="<input type=\"hidden\" name=\"editlinkimagepath\" value=\"$addimagepathincaption\" />";
									$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['add_image_caption_text']."\" name=\"admin_edit_link_on_page\"></form></td></tr>";
									}
									else {
									$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/edit.php?page=pixgallery.php\">";
									$Output .="<input type=\"hidden\" name=\"imagetochange\" value=\"$PxgImageEditID\" />";
									$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
									$Output .="<input type=\"hidden\" name=\"editlinkimagepathupdate\" value=\"$addimagepathincaption\" />";
									$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['edit_image_caption_text']."\" name=\"caption_update_edit\"></form></td></tr>";
									}
				}
			}

		else if ($ContentIsFile == TRUE && $PixGallery_OpenInNewWindow !== "1") {
				if ($PixGallery_ImageCaption != "1" && $ShowAlteredNameAnyway != "1"){
			$titleforimage = "";
			}
			if ($PixGallery_ImageClickBehavior != 1){
				$ImagePathOutput = $ImagePath;
				}
			else {
				if (isset($NextLink)){
				$ImagePathOutput = $NextLink;
				}
				else {
				$ImagePathOutput = $ContentParentUrl;
				}
				}
				$Output .= "<td><center><a href=\"$ImagePathOutput\" title=\"$ImageAlt\" rel='$PixGalleryUseRel'><img border=0 src=\"$ImagePath\" width='$ImageWidth' class='PxgImage' alt=\"$ImageAlt\"></a><p style=''>$titleforimage</center> $captionforimage <center>$newslink $ShowCurrentImageViewCount $pxgexifdataoutput $pxgiptcdataoutput</p></center>";
				if ($Pixgallery__All_Image_Text !=""){
				$Pixgallery__All_Image_Text = stripslashes($Pixgallery__All_Image_Text);
				$Output .= "<br /><p style='text-align:left;'>".$Pixgallery__All_Image_Text."</p>";
				}
				$PixgalleryDisplayingImage = "image";
				$Pixgalleryitempath = $ContentList[$i]['Imagesortpath'];
						//**Display bottom Nav bar if setting is turned on **//
						if ($PixGallery_ExtraNavBars == "1"){
								if (is_file($FullPath))
								{
								if (isset($PreviousLink))
				$Output .= " <center><a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_image']."</a> ";
			else
				$Output .= " <center>".$PixGallery_Lang_Output_Frontend['previous_image']." ";

			$ThisImageInCount = $ContentList[0]['ImageCountNumber'];
			$Output .= $PixGallery_Lang_Output_Frontend['next_previous_image_divider']." ".$PixGallery_Lang_Output_Frontend['image_number_in_gallery']." $ThisImageInCount ".$PixGallery_Lang_Output_Frontend['next_previous_image_divider'];
			if (isset($NextLink)){
			//*****AWSOM MOD ADDITION******
				$Output .= " <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_image']."</a>";
				}
				else {
				$Output .= " ".$PixGallery_Lang_Output_Frontend['next_image'];
				}
							$Output .= "</center>";
							//****END AWSOM MOD ADDITION******
							}
						else
							{
							if (isset($PreviousLink) && is_dir($PreviousPath))
								$Output .= "<center> <a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_archive']."</a>";
							if (isset($NextLink) && is_dir($NextPath)){
									if (isset($PreviousLink) && is_dir($PreviousPath)){
									$Output .= $PixGallery_Lang_Output_Frontend['next_previous_archive_divider']."<a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
									}
									else {
									$Output .= "<center> <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
									}
								}
							$Output .= "</center></p></td>\n";
							}
						}
						else {
						$Output .="</td>\n";
						}
			//** end bottom nav bar display area **//
				if ( function_exists('current_user_can') && current_user_can('manage_options') ) {

				if ($PixGallery_LegacyMode == "1"){
					$ImageEditID = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE name = '$ContentName'");
					}
					else {
					$ImageEditID = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$addimagepathincaption'");
					}
					if (!isset($ImageEditID)){
					$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/edit.php?page=pixgallery.php\">";
					$Output .="<input type=\"hidden\" name=\"editlinkimagename\" value=\"$ContentName\" />";
					$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
					$Output .="<input type=\"hidden\" name=\"editlinkimagepath\" value=\"$addimagepathincaption\" />";
					$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['add_image_caption_text']."\" name=\"admin_edit_link_on_page\"></form></td></tr>";
					}
					else {
					$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/edit.php?page=pixgallery.php\">";
					$Output .="<input type=\"hidden\" name=\"imagetochange\" value=\"$ImageEditID\" />";
					$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
					$Output .="<input type=\"hidden\" name=\"editlinkimagepathupdate\" value=\"$addimagepathincaption\" />";
					$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['edit_image_caption_text']."\" name=\"caption_update_edit\"></form></td></tr>";
					}
				}

			}
		else
			{
			//***AWSOM mod to add (Folder) to name of Folders in thumbnail view***//
			if ($ContentType == "Gallery"){
			$pxg_itemname = $ContentTitle;
			$ContentTitle .= $PixGallery_Lang_Output_Frontend['add_note_is_folder_in_thumbnail_view'];
				if ($PixGallery_UseFolderIcon != 0 ){
				$Pixgallery_Custom_Folder_Icon_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/'.$PixGallery_UseFolderIconCustom;
				//$Pixgallery_Custom_Folder_Icon_Location =  $wp_pxgadmin_url.'wp-content/uploads/awsom-customizations/'.$PixGallery_UseFolderIconCustom;
					if (is_file($Pixgallery_Custom_Folder_Icon_Location)){
					$ContentTitle .= "<img src=\"".$wp_pxgadmin_url."/wp-content/uploads/awsom-customizations/".$PixGallery_UseFolderIconCustom."\"";
					}
					else{
				$ContentTitle .= "<img src=\"".$PixGlobal_AdminPath."images/folder-image-icon.png\"";
					}
				}
			//$ContentTitle.= " (folder)";
			}

			//***end mods***//
			//**Mod for Watermark redirect link to cache folder for images**//
					if ($ContentType != "Gallery" && $PixGallery_WatermarkImages == "1"){

						//$ImagePath	= str_replace(get_bloginfo('wpurl'), "", $ImagePath);
						$ImagePathpopup = $PixGallery_RootCache."/".md5($ImagePath).".".$ContentExtension;
						if ($PixGallery_UseAbsPath == "1"){
						//$abspathimagepath = get_bloginfo('wpurl')."/".$ImagePath;
							$ImagePathpopup = get_bloginfo('wpurl')."/".$PixGallery_RootCache."/".md5($ImagePath).".".$ContentExtension;
								}
						//$ImagePathpopup = $ImagePath;
						}
					elseif ($ContentType !== "Gallery" && $PixGallery_WatermarkImages !== "1"){
					$ImagePathpopup = $ImagePath;
					if ($PixGallery_UseAbsPath == "1"){
					$ImagePathpopup = get_bloginfo('wpurl').$ImagePathpopup;
					}
					}
		//** End Watermark mod **//
		if ($PixGallery_UseAbsPath == "1"){
		$ImageUrl = get_bloginfo('wpurl').$ImageUrl;
		}
			$Output .= "<td align=center><table class='PxgGalleryInnerTable'><thead  class='PxgGalleryInnerTableHead'><tr><td>";

			if ($PopupOnThumbnails == TRUE && $ContentType != "Gallery"){
				if ($PixGallery_OpenInNewWindow == "1"){
				$Output .= "<a href=\"$ImagePathpopup\" title=\"$ImageAlt\" rel='$PixGalleryUseRel' target=\"_blank\">";
				}
				else {
				$Output .= "<a href=\"$ImagePathpopup\" title=\"$ImageAlt\" rel='$PixGalleryUseRel'>";
				}
			}
			else{
				$Output .= "<a href=\"$ImageUrl\" title=\"$ImageAlt\">";
				}

			$Output .= "<img border=0 src=\"$ImagePath\" width='$ImageWidth' ";

			if ($PixGallery_SquareThumbnails == TRUE)
				$Output .= " height='$ImageWidth' ";
			else
				$Output .= " height='$ImageHeight' ";
			if ($ContentType !== "Gallery"){
			$Output .= "class='PxgImage' alt=\"$ImageAlt\"></a>";
			}
			else{
			$Output .= "class='PxgSubGaleryImage' alt=\"$ImageAlt\"></a>";
			}
			$Output .= "</td></tr></thead><tbody><tr><td>";
			//*****AWSOM mod to remove titles, links text from thumbnails*****//
			if ($PixGallery_DebugMode == "1" && $PixGallery_DebugModeType != "1" && function_exists('current_user_can') && current_user_can('manage_options')){
			$displaysadminfunctions = "<br />".$PixGallery_Lang_Output_Frontend['numerical_sort_number']."(".$ContentSortNumber.")";
			}
			elseif ($PixGallery_DebugMode == "1" && $PixGallery_DebugModeType == "1" && function_exists('current_user_can') && current_user_can('manage_options')){
			if ($ContentType !== "Gallery"){
			$pxg_itemname = $ContentTitle;
			}
			if ($ContentSortNumber == "" | $ContentSortNumber ==NULL){
			$ContentSortNumber = 0;
			}
			
			$displaysadminfunctions = "<br /><input type='hidden' name='FEitemtype".$i."' value='".$ContentType."'><input type='hidden' name=\"FErealitemname".$i."\" value=\"".$ContentList[$i]['Name']."\"><input type='hidden' name='FEdbmatch".$i."' value='".$addimagepathincaption."'><input type='text' name=\"FECustomName".$i."\" style='width: 100px;' value=\"".$pxg_itemname."\"><br />".$PixGallery_Lang_Output_Frontend['numerical_sort_number']."<input type='text' name='FESortNumber".$i."' style='width: 40px;' value='".$ContentSortNumber."'>";
			if ($ContentType !== "Gallery"){
			$displaysadminfunctions .= "<br />".$PixGallery_Lang_Output_Frontend['advanced_edit_caption_label']."<textarea name=\"FEimagecaption".$i."\" rows=\"2\" cols=\"20\" >".$ImageCaptionData."</textarea>";
			}
			}
			else{
			}
			if ($PixGallery_NoThumbnailText !== "1"){
			if ($PopupOnThumbnails == TRUE && $ContentType != "Gallery"){
							if ($PixGallery_OpenInNewWindow == 1){
							$Output .= "<a href=\"$ImagePathpopup\" rel='$PixGalleryUseRel' title=\"$ImageAlt\" target=\"_blank\">$ContentTitle</a>$displaysadminfunctions";
							}
							else {
							$Output .= "<a href=\"$ImagePathpopup\" rel='$PixGalleryUseRel' title=\"$ImageAlt\">$ContentTitle</a>$displaysadminfunctions";
							}
						}


			else {
				$Output .= "<a href=\"$ImageUrl\">$ContentTitle </a>$displaysadminfunctions";
				}
				}
			else {
				if ($ContentType == "Gallery"){
				$Output .= "<a href=\"$ImageUrl\">$ContentTitle</a>$displaysadminfunctions";
				}
			//**** no link output, no title output *****//
			}
			//*****end remove text mod*****//
			
			$Output .= "</td></tr></tbody></table>";

			$Output .= "</center></td>\n";
			}

		if (($Columns + 1) == $MaxColumns)
			{
			$Output	.= "</tr><tr>\n";
			$Rows	+= 1;

			if ((($Rows + 1) > $PixGallery_Rows) && ($PixGallery_Rows > 0))
				break;
			else
				$Columns = 0;
			}
		else if (($i + 1) != count($ContentList))
			$Columns += 1;

		}

	/*if ($ContentIsFile == FALSE)
		{
		while (($Columns + 1) != $PixGallery_Columns)
			{
			$Output .= "<td width=$ColumnWidth>&nbsp;</td>\n";
			$Columns += 1;
			}
		}*/

	$Output .= "</tr></table>\n";
	//***** AWSOM mod to show see entire gallery link if using restircted thumbnails on index page *****//
	if (!is_page() && !is_single()){
					if ($PixGallery_MaxThumbnailsOnIndex !== "0" && $PixGallery_MaxThumbnailsOnIndex !==""){
					$Output .= "<p align=\"center\"><a href=\"".$ContentParentUrl."\" rel=\"bookmark\">".$PixGallery_Lang_Output_Frontend['view_entire_gallery_text']."</a></p>";
					}
			}
	//***** end AWSOM mod *****//
	if ($PixGallery_Rows && !is_home())
		{
		$ImagesPerPage	= ($PixGallery_Columns * $PixGallery_Rows);
		$NextStartImage = ($ImagesPerPage + $StartImage);
		$PrevStartImage = ($StartImage - $ImagesPerPage);
		$TotalImages	= (count($ContentList));
		$TotalPages		= (ceil($TotalImages / $ImagesPerPage));
		$CurrentPage	= (ceil($i / $ImagesPerPage));
		$LastPage		= $ImagesPerPage * ($TotalPages - 1);
		
		if ($PixGallery_ExtraNavBars == "1"){
				if (isset($PreviousLink) && is_dir($PreviousPath))
						$Output .= "<center> <a href='".$PreviousLink."'>".$PixGallery_Lang_Output_Frontend['previous_archive']."</a>";
				if (isset($NextLink) && is_dir($NextPath)){
						if (isset($PreviousLink) && is_dir($PreviousPath)){
								$Output .= $PixGallery_Lang_Output_Frontend['next_previous_archive_divider']."<a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
								}
						else {
							$Output .= "<center> <a href='".$NextLink."'>".$PixGallery_Lang_Output_Frontend['next_archive']."</a>";
								}
					}
						$Output .= "</center></p>\n";
						}
		
			
		
		
		if ($TotalPages > 1)
			{
			$Output .= "<table class='PxgPageTable'><tr><td align=left>";

			if (($PrevStartImage >= 0) && ($PrevStartImage != $NextStartImage))
				{
				$Output .= "<a href='".PixGallery_UrlBuild($QueryStartName)."'>".$PixGallery_Lang_Output_Frontend['first_link_in_thumbnail_view']."</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href='".PixGallery_UrlBuild($QueryStartName, $PrevStartImage)."'>".$PixGallery_Lang_Output_Frontend['previous_link_in_thumbnail_view']."</a>";
				}

			$Output .= "</td><td align=center>$CurrentPage".$PixGallery_Lang_Output_Frontend['current_page_overall_page_divider']."$TotalPages</td><td align=right>";

			if ($NextStartImage < $TotalImages)
				{
				$Output .= "<a href='".PixGallery_UrlBuild($QueryStartName, $NextStartImage)."'>".$PixGallery_Lang_Output_Frontend['next_link_in_thumbnail_view']."</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href='".PixGallery_UrlBuild($QueryStartName, $LastPage)."'>".$PixGallery_Lang_Output_Frontend['last_link_in_thumbnail_view']."</a>";
				}
			if ( $PixGallery_LimitPaginationLinks == "0"){
			for ($pi = 0; $pi != $TotalPages; $pi += 1){
				$thispaginationlink = ($ImagesPerPage * $pi);
				$thispaginationnumber = ($pi + 1);
				if ($thispaginationnumber == $CurrentPage){
				$PaginationLink .= "<a href='".PixGallery_UrlBuild($QueryStartName, $thispaginationlink)."' class='PxgPaginationCurrentLink'>".$thispaginationnumber."</a>&nbsp;";
				$ReturnLinkFromEdit = PixGallery_UrlBuild($QueryStartName, $thispaginationlink);
				}
				else{
				$PaginationLink .= "<a href='".PixGallery_UrlBuild($QueryStartName, $thispaginationlink)."' class='PxgPaginationLink'>".$thispaginationnumber."</a>&nbsp;";
				}

			  }
			$Output .= "</td></tr><tr><td align=left>&nbsp;</td><td align=center>".$PaginationLink."</td><td align=right>&nbsp;</td></tr></table>";
			}
			else {
			for ($pi = 0; $pi != $TotalPages; $pi += 1){
							$thispaginationlink = ($ImagesPerPage * $pi);
							$thispaginationnumber = ($pi + 1);
							if ($thispaginationnumber == $CurrentPage){
							$PaginationLink .= "<a href='".PixGallery_UrlBuild($QueryStartName, $thispaginationlink)."' class='PxgPaginationCurrentLink'>".$thispaginationnumber."</a>&nbsp;";
							$ReturnLinkFromEdit = PixGallery_UrlBuild($QueryStartName, $thispaginationlink);
							}
							else{
							$lowestpoint = $CurrentPage - ($PixGallery_LimitPaginationLinks / 2);
							$highestpoint = $CurrentPage + ($PixGallery_LimitPaginationLinks / 2);
							if ($lowestpoint < 0){
							$highestpoint = $highestpoint - $lowestpoint;
							$lowestpoint = 0;
							  }
							if ($highestpoint > $TotalPages){
							$lowestpoint = $lowestpoint - ($highestpoint - $TotalPages);
							$highestpoint = $TotalPages;
							  }
							if ($thispaginationnumber >= $lowestpoint && $thispaginationnumber <= $highestpoint){

							$PaginationLink .= "<a href='".PixGallery_UrlBuild($QueryStartName, $thispaginationlink)."' class='PxgPaginationLink'>".$thispaginationnumber."</a>&nbsp;";
							  }
							}
						  }
						$Output .= " </td></tr><tr><td align='left'>&nbsp;</td><td align='center'>&nbsp;";
						if ($lowestpoint > 1){
						$PreviousLinkSpecial = ($lowestpoint - 2) * $ImagesPerPage;
						if ($PreviousLinkSpecial < 0){
						$PreviousLinkSpecial = 0;
						}
						$Output .= "<a href='".PixGallery_UrlBuild($QueryStartName, $PreviousLinkSpecial)."' class='PxgPaginationLink'>".$PixGallery_Lang_Output_Frontend['elipsed_pages_in_thumbnail_view_divider']."</a>&nbsp;";
						}
						$Output .= "$PaginationLink";
						if ($highestpoint < $TotalPages){
						$NextLinkSpecial = $highestpoint * $ImagesPerPage;
						$Output .= "<a href='".PixGallery_UrlBuild($QueryStartName, $NextLinkSpecial)."' class='PxgPaginationLink'>".$PixGallery_Lang_Output_Frontend['elipsed_pages_in_thumbnail_view_divider']."</a>";
						}
						$Output .= "</td><td align='right'>&nbsp;</td></tr></table>";
			}

			//if (($PixGallery_LimitPaginationLinks != "0") && ($PixGallery_LimitPaginationLinks != "")){
				//$breakuppagination = ceil($TotalPages / 10);
				// if ($breakuppagination >= 3) {

				 //for ($opi = 0; $opi < $TotalPages; $opi += $breakuppagination){
				//				$thisopaginationlink = ($ImagesPerPage * $opi);
							//	$thisopaginationnumber = ($opi + 1);
								//$OPaginationLink .= "<a href='".PixGallery_UrlBuild($QueryStartName, $thisopaginationlink)."' class='PxgPaginationLink'>".$thisopaginationnumber."</a>&nbsp;";
			 	//	}
			 // $Output .= "<tr><td><br /></td></tr>";
			 // $Output .= "<tr><td align=left>&nbsp;</td><td align=center>".$OPaginationLink."</td><td align=right>&nbsp;</td></tr>";
		        //}
		     //}
		   }
		  // $Output .="</table>";

		}

			if ($ContentIsFile !== TRUE){
			$PixgalleryDisplayingImage = "gallery";
			$Pixgalleryitempath = $ReadmeFile;
			if ( function_exists('current_user_can') && current_user_can('manage_options') ) {
			
								$PxgGalleryEditID = $wpdb->get_var("SELECT apgid FROM $awsom_pixgallery_gallery_table_name WHERE galpath ='$ReadmeFile'");
								if ($Link == "" && $PaginationLink ==""){
								$Link = $ContentParentUrl;
								}
								elseif ($Link == "" && $PaginationLink !=""){
								$Link = $ReturnLinkFromEdit;
								$pxg_paginated_status = "1";
								}
								elseif ($Link != "" && $PaginationLink !=""){
								$Link = $ReturnLinkFromEdit;
								$pxg_paginated_status = "1";
								}
									if ($PixGallery_DebugModeType == "1"){
										$Output .="<input type=\"hidden\" name=\"pxg_paginated\" value=\"$pxg_paginated_status\" /><input type=\"hidden\" name=\"editlinkgallerypath\" value=\"$ReadmeFile\" /><input type=\"hidden\" name=\"takemeback\" value=\"$Link\" /><input type='hidden' name='FEtotaledits' value='".$i."'><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['mass_edit_gallery_caption_text']."\" name=\"mass_edit_gallery_captions_onpage\"></form>";
									}
								
								
									if (!isset($PxgGalleryEditID)){
									$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/themes.php?page=pixgallery.php\">";
									$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
									$Output .="<input type=\"hidden\" name=\"editlinkgallerypath\" value=\"$ReadmeFile\" />";
									$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['add_gallery_caption_text']."\" name=\"admin_edit_gallerylink_on_page\"></form></td></tr>";
									}
									else {
									$Output .="<form method=\"post\" action=\"$wp_pxgadmin_url/wp-admin/themes.php?page=pixgallery.php\">";
									$Output .="<input type=\"hidden\" name=\"gallerytochange\" value=\"$PxgGalleryEditID\" />";
									$Output .="<input type=\"hidden\" name=\"takemeback\" value=\"$Link\" />";
									$Output .="<input type=\"hidden\" name=\"editlinkgallerypathupdate\" value=\"$ReadmeFile\" />";
									$Output .="<tr><td><input type=\"submit\" value=\"".$PixGallery_Lang_Output_Frontend['edit_gallery_caption_text']."\" name=\"gallery_update_edit\"></form></td></tr>";
									}

				}
				}


	$Output .= "</td></tr></table>";
		if ($ContentIsFile == TRUE && $PixGallery_ShareThisConnector == 1){
			if (function_exists('st_request_handler')){
			$Output .="<span class=\"st_sharethis\" st_url=\"".$ContentList[0]['ShareThisLink']."\" st_title=\"".$PixGallery_Lang_Output_Frontend['share_this_link_title_text']."\">".$PixGallery_Lang_Output_Frontend['sharethis_image_text']."</span>";
			}
			else{
			$Output .="<a href=\"".$ContentList[0]['ShareThisLink']."\" title=\"".$PixGallery_Lang_Output_Frontend['share_this_link_title_text']."\">".$PixGallery_Lang_Output_Frontend['sharethis_image_text']."</a>";
			}
		}
		if ($ContentIsFile !== TRUE && $PixGallery_ShareThisConnector == 1){
		$mybaseurl = "http://".$_SERVER['HTTP_HOST'];
			if (function_exists('st_request_handler')){
			$Output .="<span class=\"st_sharethis\" st_url=\"".$mybaseurl.$Link."\" st_title=\"".$PixGallery_Lang_Output_Frontend['share_this_link_title_text']."\">".$PixGallery_Lang_Output_Frontend['sharethis_gallery_text']."</span>";
			}
			else {
			$Output .="<a href=\"".$mybaseurl.$Link."\" title=\"".$PixGallery_Lang_Output_Frontend['share_this_link_title_text']."\">".$PixGallery_Lang_Output_Frontend['sharethis_gallery_text']."</a>";
			}
		}
	$Output .="</div><!-- End AWSOM Pixgallery Main Table -->";
	if ($Link == "" && $PaginationLink ==""){
									$Link = $ContentParentUrl;
									}
									elseif ($Link == "" && $PaginationLink !=""){
									$Link = $ReturnLinkFromEdit;
									}
									elseif ($Link != "" && $PaginationLink !=""){
									$Link = $ReturnLinkFromEdit;
								}
	$PixgalleryReturnPath = $Link;
	if ($PixGallery_DebugMode == "1" && function_exists('current_user_can') && current_user_can('manage_options')){
	$Output .= "fullpath = $FullPath<br />";
	$Output .= "gallery path = $GalleryPath<br />";
	$Output .= "Collection path = $CollectionPath<br />";
	$Output .= "ContentParentPath = $ContentParentPath<br />";
	//$Output .= "ContentParent = $ContentParent<br />";
	$Output .= "ContentParentUrl = $ContentParentUrl<br />";
	$Output .= "CollectionRootPath = $CollectionRootPath<br />";
	$Output .= "Pixgalleryitempath = $Pixgalleryitempath<br />";
	$Output .= "PathParts = $PathParts[$i] <br />";
	$Output .= "Image name path =   $customnamepathmatch";
	
	}
	if ($ContentIsFile !== TRUE && $PixGallery_DisplayRSSLink == 1 && $PixGallery_DisplayRSS == 1){
	$RSSconvertedpath = str_replace('/','-', $ReadmeFile);
	$pxgdisplayfeedlink = $wp_pxg_url.'/'.$PixGallery_RootCache.'/Pixgallery'.$RSSconvertedpath.'imagefeed.xml';
	
	
	$Output .="<br />".$PixGallery_Lang_Output_Frontend['rss_gallery_description_text']."<a href=\"$pxgdisplayfeedlink\">".$PixGallery_Lang_Output_Frontend['rss_gallery_link_text']."</a>.";
	
	}
	
	return $Output;
}

/* Main photo gallery function */

function PixGallery_PhotoGallery($Html)
{
	global $CollectionPath, $GalleryPath;
	global $post;


	$StartIndex		= 0;
	$TagName		= "pixgallery";
	
	$TagBegin		= strpos($Html, "<$TagName");
	$TagBeginEnd	= strpos($Html, ">", $TagBegin);
	$TagEnd			= strpos($Html, "</$TagName>");

	if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
		{
		$TagBeginEnd	+= 1;
		$TagContents	= substr($Html, $TagBegin, $TagBeginEnd - $TagBegin);
		$GalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));

		if (!$GalleryPath)
			{
			$GalleryPath	= "";
			$PostTitle		= strtolower($post->post_title);

			for ($i = 0; $i != strlen($PostTitle); $i += 1)
				{
				if (($PostTitle[$i] >= 'a' && $PostTitle[$i] <= 'z') ||
					($PostTitle[$i] >= '0' && $PostTitle[$i] <= '9') ||
					($PostTitle[$i] == '-'))
					{
					$GalleryPath .= $PostTitle[$i];
					}
				if ($PostTitle[$i] == ' ')
					$GalleryPath .= '-';
				}

			$GalleryPath = "/images/posts/$GalleryPath/";
			}

		$Text	 = substr($Html, $StartIndex, $TagBegin - $StartIndex);
		$Content = substr($Html, $TagBeginEnd, $TagEnd - $TagBeginEnd);

		$Output = PixGallery_PhotoGallery_Collection($Content);

		$TagEnd += strlen($TagName) + 3;

		$StartHtml = substr($Html, $StartIndex, $TagBegin - $StartIndex);
		$EndHtml = substr($Html, $TagEnd);

		if ($ContentIsFile == TRUE)
			$Html = $Output;

		else
			$Html = $StartHtml.$Output.$EndHtml;


		if ($CollectionPath != "/")
			$Html = str_replace($Text, "", $Html);

		}

	return $Html;
}

function PixGallery_PhotoGalleryNewTag($Html)
{
	global $CollectionPath, $GalleryPath;
	global $post;

	$StartIndex		= 0;
	$TagName		= "pixgallery";
	
	$TagBegin		= strpos($Html, "[$TagName");
	$TagBeginEnd	= strpos($Html, "]", $TagBegin);
	$TagEnd			= strpos($Html, "[/$TagName]");

	if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
		{
		$TagBeginEnd	+= 1;
		$TagContents	= substr($Html, $TagBegin, $TagBeginEnd - $TagBegin);
		$GalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));

		if (!$GalleryPath)
			{
			$GalleryPath	= "";
			$PostTitle		= strtolower($post->post_title);

			for ($i = 0; $i != strlen($PostTitle); $i += 1)
				{
				if (($PostTitle[$i] >= 'a' && $PostTitle[$i] <= 'z') ||
					($PostTitle[$i] >= '0' && $PostTitle[$i] <= '9') ||
					($PostTitle[$i] == '-'))
					{
					$GalleryPath .= $PostTitle[$i];
					}
				if ($PostTitle[$i] == ' ')
					$GalleryPath .= '-';
				}

			$GalleryPath = "/images/posts/$GalleryPath/";
			}

		$Text	 = substr($Html, $StartIndex, $TagBegin - $StartIndex);
		$Content = substr($Html, $TagBeginEnd, $TagEnd - $TagBeginEnd);

		$Output = PixGallery_PhotoGallery_Collection($Content);

		$TagEnd += strlen($TagName) + 3;

		$StartHtml = substr($Html, $StartIndex, $TagBegin - $StartIndex);
		$EndHtml = substr($Html, $TagEnd);

		if ($ContentIsFile == TRUE)
			$Html = $Output;

		else
			$Html = $StartHtml.$Output.$EndHtml;


		if ($CollectionPath != "/")
			$Html = str_replace($Text, "", $Html);

		}

	return $Html;
}






/* ------------------------------------------------------------------------------ */
/* Administration panel, setting related, filters, etc
/* ------------------------------------------------------------------------------ */

function PixGallery_Options_Save()
{
	$PixGalleryArray = null;

	$Keys = array_keys($_REQUEST);

	for ($i = 0; $i != count($_REQUEST); $i++)
		{
		$Value = stripslashes($_REQUEST[$Keys[$i]]);

		if (strpos($Keys[$i], "PixGallery_") !== FALSE)
			$PixGalleryArray[str_replace("PixGallery_", "", $Keys[$i])] = $Value;
		}

	update_option("PixGallery", $PixGalleryArray);
}

function PixGallery_Options_Load()
{
	$PixGalleryArray = get_option("PixGallery");

	if (is_array($PixGalleryArray) == FALSE)
		{
		PixGallery_Options_Save();
		return;
		}

	$Keys = array_keys($GLOBALS);

	for ($i = 0; $i != count($Keys); $i += 1)
		{
		if (strpos($Keys[$i], "PixGallery_") !== FALSE)
			unset($GLOBALS[$Keys[$i]]);
		}

	$Keys = array_keys($PixGalleryArray);

	for ($i = 0; $i != count($PixGalleryArray); $i++)
		{
		$GLOBALS["PixGallery_".$Keys[$i]] = $PixGalleryArray[$Keys[$i]];
		}

}


function PixGallery_Options_Panel()
{
	global $PixGlobal_Path, $PixGlobal_AdminPath, $wpdb, $awsomcreatecachedir;
	if (isset($_POST['info_update']))
		{
		/* Deal with checkboxes */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));
		check_admin_referer('apg_update_pixgallery_options');

		PixGallery_Options_Save();
		PixGallery_Options_Load();

		echo "<div class='updated'><p><strong>Settings saved successfully!</strong></p></div>";
		}
		// *****AWSOM mod to add create default cache for upgraders ****** //
	if (isset($_POST['createdefaultcache']))
		{
			/* Deal with checkboxes */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));
      	check_admin_referer('apg_update_pixgallery_options');
			$PixGalleryArray = null;

				$Keys = array_keys($_REQUEST);

				for ($i = 0; $i != count($_REQUEST); $i++)
					{
					$Value = stripslashes($_REQUEST[$Keys[$i]]);

					if (strpos($Keys[$i], "PixGallery_") !== FALSE)
						$PixGalleryArray[str_replace("PixGallery_", "", $Keys[$i])] = $Value;
					}
			$PixGalleryArray[RootCache] = get_option( "upload_path" ) . "/cache";
			update_option("PixGallery", $PixGalleryArray);
			PixGallery_Options_Load();
			if (wp_mkdir_p($awsomcreatecachedir)){
			     update_option ("awsom_pixgallery_cache_created", 1);
			  echo "<div class='updated'><p><strong>Successfully Created Default Upload Area Cache Folder!</strong></p></div>";
      		}
		else 	{
			echo "<div class='updated'><p><strong>Could Not Create Default Upload Area Cache Folder!</strong></p></div>";
			}


		}
		// ***** End mod ****** //
?>

<div class="wrap">
  <form method="post"><?php if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_update_pixgallery_options'); } ?>
  
<input type='hidden' name="PixGallery_VersionNumber" value="4.7.0">
    <h1>AWSOM PixGallery <?php echo $GLOBALS["PixGallery_VersionNumber"]; ?> Settings </h1>
<!--- begin AWSOM mod section -->
	<hr>
	Please read the <a href="<?php echo $PixGlobal_AdminPath; ?>awsom_pixgallery_mod_helpfile.txt" target="_blank">Helpfile</a> for more information on the plugin. For support or to check for newer versions visit <a href="http://www.awsom.org" target="_blank">AWSOM.org</a> or the support forum at <a href="http://www.harknell.com" target="_blank">Harknell.com</a>
	<br /><a href="http://www.awsom.org/tutorialsvideo/" target="_blank">Click Here</a> To Watch Video Tutorials or <a href="http://www.awsom.org/awsom-pixgallery/" target="_blank">Click Here</a> to View Online Documentation.<br /><br /> To add a gallery to your site use the following code in a post or a page:<b> [pixgallery path="/Path/To/Your_image_directory_name/"][/pixgallery]</b><br />
	Replace the "Your_image_directory_name" with your image folder starting from the root directory of your website. You can go multiple folders in, just make sure the last folder
	has a trailing / at the end.<br /> <b>EXAMPLE</b>: Your site is in the www folder with your image folder set to www/myimages, you would place <b>[pixgallery path="/myimages/"][/pixgallery]</b> in your post or page to display the images in your gallery.
	<br />
	<b>New Customization features:</b> You can now customize the language used in the plugin. The file pixgallery_lang.php (in the awsom-pixgallery plugin folder) contains an array that can be edited to change the display of things like "Previous Image" to whatever you want. You can copy this file to a new folder in your uploads directory (which should have automatically been created for you) called "awsom-customizations" to retain these changes between plugin upgrades. This is also true for the file pixgallery.css (again, in the awsom-pixgallery plugin folder), make changes and copy the file to the awsom-customizations folder.
	<hr>
<fieldset class="options">
<h2>Theme Options</h2>
<h3>Global WordPress Effects</h3>
	<blockquote>
	<!--- Global image rewrite setting -->
	If you often upload pictures straight from your digital camera to your server, you can use this option to reduce the actual size of the images in Wordpress (Note: this is an actual file rewrite of your image file to make it smaller and can't be reversed). Specify the image height/width you want your images to be reduced to.<br /> <b>Note:</b> requires 777 permissions on all directories that hold images including the wp-content/uploads area and all sub-folders.
	<br /><br />
	Globally manage image size for all images in Wordpress (even those not in a gallery)(0 = disabled):
	<br />
	Width:<input type='text' name='PixGallery_ReduceOriginalsWidth' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ReduceOriginalsWidth"]; ?>'> px &nbsp;&nbsp;Height:<input type='text' name='PixGallery_ReduceOriginalsHeight' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ReduceOriginalsHeight"]; ?>'> px <a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/global_image_reduce_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on Global Image Reduce settings" alt="Get Help on the Global Image Reduce Setting"></a>
	<br /><br />
	
	<!--- End global rewrite setting -->
	<!--- Global post scanning setting -->
	<b>New Feature</b>: Disable Post scanning for images. In some cases people have reported index page slowdown with Pixgallery active. By turning this setting on you will eliminate Pixgallery's post scanning for images, which usually fixes this issue. 
	<br /><br />
	Turn off Scanning for images in Posts? (Pages will still be checked) :<br />
	<select name='PixGallery_ScanPosts'>

						<option value='0'	<?php if ($GLOBALS["PixGallery_ScanPosts"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_ScanPosts"] == '1') echo "selected"; ?>>Yes (Stop Scanning Posts)</option>
										</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/post_scanning_settings.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Post Scanning setting" alt="Get Help on the Post Scanning Setting"></a>


	<br /><br />
	
	<!--- End global post scanning setting -->
	
	
	</blockquote>
<h3>Gallery Layout/CSS</h3>
<blockquote>
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Number of columns per gallery page (0 = unlimited)</td>
			<td align=right><input type='text' name='PixGallery_Columns' style='width: 20px;' value='<?php echo $GLOBALS["PixGallery_Columns"]; ?>'></td>
		</tr>
	</table>
	<br />
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Number of rows per gallery page (0 = unlimited)</td>
			<td align=right><input type='text' name='PixGallery_Rows' style='width: 20px;' value='<?php echo $GLOBALS["PixGallery_Rows"]; ?>'></td>
		</tr>
	</table>
	<!--- ***** Sort Order Mod ***** -->
	<br />
		How do you want PixGallery to sort your images on the Thumbnail page? (<b>NOTE</b>: On some servers the Last File and First File sorting types may be reversed due to the way the server is set up, if the images are not sorting the way you expect, try reversing the sort type. Alphabetical is unaffected by this and will work fine on all server types)
				<br />
					<select name='PixGallery_SortType'>

						<option value='0'	<?php if ($GLOBALS["PixGallery_SortType"] == '0') echo "selected"; ?>>Last File Added First (default)</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_SortType"] == '1') echo "selected"; ?>>Start From First File Added</option>
						<option value='2'	<?php if ($GLOBALS["PixGallery_SortType"] == '2') echo "selected"; ?>>Alphabetical Ascending</option>
						<option value='3'	<?php if ($GLOBALS["PixGallery_SortType"] == '3') echo "selected"; ?>>Alphabetical Descending</option>
						<option value='4'	<?php if ($GLOBALS["PixGallery_SortType"] == '4') echo "selected"; ?>>Ascending Numerical Sort</option>
						<option value='5'	<?php if ($GLOBALS["PixGallery_SortType"] == '5') echo "selected"; ?>>Descending Numerical Sort</option>
				</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/sort_order_settings.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on Sort Order setting" alt="Get Help on the Sort Order Setting"></a>

			<br />
	<!--- ***** end Sort Order mod ***** -->
	<!--- ****Mod added for Custom CSS ***** -->
	<br />
	Do you want to add Custom CSS to your Gallery? Enter your CSS entries in the following box; These entries will affect the HTML div box that contains your gallery.
	<br />
	<input type='text' name='PixGallery_CustomCSS' style='width: 550px;' value='<?php echo $GLOBALS["PixGallery_CustomCSS"]; ?>'><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/custom_css_settings.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on CSS settings" alt="Get Help on CSS Settings"></a>
	<br /><br />
	<!--- *****end Custom CSS Mod ***** -->
</blockquote>
<h3>Gallery Image Options</h3>
<blockquote>
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Maximum thumbnail page image size (0 = no max):<br />
			Width:<input type='text' name='PixGallery_ImageWidth' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ImageWidth"]; ?>'> px &nbsp;&nbsp;Height:<input type='text' name='PixGallery_ImageHeight' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ImageHeight"]; ?>'> px</td>
		</tr>
	</table>
	<br />
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Maximum image size for viewing full size images within the Wordpress page (use this setting if you need to scale full size images to fit your theme)(0 = no max)<br />
			Width:<input type='text' name='PixGallery_ImageWidthLarge' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ImageWidthLarge"]; ?>'> px &nbsp;&nbsp;Height:<input type='text' name='PixGallery_ImageHeightLarge' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ImageHeightLarge"]; ?>'> px </td>
		</tr>
	</table>
	<br />
	<?php if (isset($GLOBALS["PixGallery_SquareThumbnails"])) { ?>
		<input type="checkbox" name="PixGallery_SquareThumbnails" checked>
	<?php } else { ?>
		<input type="checkbox" name="PixGallery_SquareThumbnails">
	<?php } ?> Make all thumbnails squared on thumbnail page
	<br /><br />
	<!--- ***** Watermark Images mod ***** -->
					Would you like Pixgallery to Watermark your Images Automatically?
					<br />
							<select name='PixGallery_WatermarkImages'>
						<option value='0'	<?php if ($GLOBALS["PixGallery_WatermarkImages"] == '0') echo "selected"; ?>>No</option>
							<option value='1'	<?php if ($GLOBALS["PixGallery_WatermarkImages"] == '1') echo "selected"; ?>>Yes</option>
							</select>
							&nbsp;&nbsp;Watermark Style: <select name='PixGallery_WatermarkStyle'>
													<option value='0'	<?php if ($GLOBALS["PixGallery_WatermarkStyle"] == '0') echo "selected"; ?>>Word Overlay</option>
														<option value='1'	<?php if ($GLOBALS["PixGallery_WatermarkStyle"] == '1') echo "selected"; ?>>Custom Image Watermark with .png file</option>
														<option value='2'	<?php if ($GLOBALS["PixGallery_WatermarkStyle"] == '2') echo "selected"; ?>>Image Word Overlay</option>
														<option value='3'	<?php if ($GLOBALS["PixGallery_WatermarkStyle"] == '3') echo "selected"; ?>>Custom Image Watermark with .gif file</option>
							</select>
							&nbsp;&nbsp;Watermark Word Overlay Color: <select name='PixGallery_WatermarkColor'>
																				<option value='0'	<?php if ($GLOBALS["PixGallery_WatermarkColor"] == '0') echo "selected"; ?>>Black</option>
																					<option value='1'	<?php if ($GLOBALS["PixGallery_WatermarkColor"] == '1') echo "selected"; ?>>White</option>
																					<option value='2'	<?php if ($GLOBALS["PixGallery_WatermarkColor"] == '2') echo "selected"; ?>>Red</option>
							</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/watermarking_settings.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Watermarking settings" alt="Get Help on the Watermarking Settings"></a>

						<br /><b>Note:</b> You *Must* delete all image files in your Pixgallery cache folder whenever you change your Watermark settings. This includes turning it on or off, or changing watermark types.<br />
	<!--- ***** end Watermark images mod ***** -->
	</blockquote>
	<h3>Image Information Display Options</h3>
	<blockquote>
	<!--- ****Mod added for News Link in Gallery***** -->
	<b>ComicPress Theme Support</b>: Do you want to automatically add a link below your images to the news post(s) that corresponds to that image? (requires images to be named according to ComicPress y/m/d format)
	<br />
		<select name='PixGallery_LinkToNewsPost'>

			<option value='0'	<?php if ($GLOBALS["PixGallery_LinkToNewsPost"] == '0') echo "selected"; ?>>No</option>
			<option value='1'	<?php if ($GLOBALS["PixGallery_LinkToNewsPost"] == '1') echo "selected"; ?>>Yes</option>

		</select> Exclude Authors (comma seperated by their user number): <input type='text' name='PixGallery_ExcludeNewsAuthor' style='width: 40px;' value='<?php echo $GLOBALS["PixGallery_ExcludeNewsAuthor"]; ?>'><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/news_interlink_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on News Interlink settings" alt="Get Help on the News Interlink Setting"></a>
		<br /><br />
	<!--- *****end News Link Mod ***** -->	
	<!--- ****Mod added for Viewing Image Counts***** -->
			Pixgallery Show Image View Count: Set this to yes if you want to display the number of times an image has been viewed. (requires Image to have a caption in the database)
		<br />
				<select name='PixGallery_ImageViewNums'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_ImageViewNums"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_ImageViewNums"] == '1') echo "selected"; ?>>Yes</option>

				</select><br /><br />
	<!--- *****end Viewing Image counts mod ***** -->
	<!--- ****Mod added for Remove _ from File name function ***** -->
	Remove _ (underscores) from file name in Full Image and thumbnail view? (<b>EXAMPLE:</b> my_image_name.gif would appear as "my image name" as opposed to "my_image_name")
		<br />
			<select name='PixGallery_RemoveUnderscoresFromName'>

				<option value='0'	<?php if ($GLOBALS["PixGallery_RemoveUnderscoresFromName"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_RemoveUnderscoresFromName"] == '1') echo "selected"; ?>>Yes</option>

		</select>
		<br /><br />
	<!--- ***** end Remove _ mod ***** -->
	<!--- ***** Add Caption to Image mod ***** -->
	Do you want to add Captions to your images? (use the Pixgallery Image Captions area under the Admin "Write" tab to input your captions)
			<br />
				<select name='PixGallery_ImageCaption'>

				<option value='0'	<?php if ($GLOBALS["PixGallery_ImageCaption"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_ImageCaption"] == '1') echo "selected"; ?>>Yes</option>

			</select>

		<br /><br />
	<!--- ***** end Caption mod ***** -->
	<!--- ***** Add EXIF display to Image mod ***** -->
		Do you want to add the Display of EXIF data to your images?
				<br />
					<select name='PixGallery_Displayexif'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_Displayexif"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_Displayexif"] == '1') echo "selected"; ?>>Yes</option>

				</select>

			<br /><br />
	<!--- ***** end Exif mod ***** -->
	<!--- ***** Add IPTC display to Image mod ***** -->
		Do you want to add the Display of IPTC data to your images?
				<br />
					<select name='PixGallery_Displayiptc'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_Displayiptc"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_Displayiptc"] == '1') echo "selected"; ?>>Yes</option>

				</select>

			<br /><br />
	<!--- ***** end IPTC mod ***** -->
	</blockquote>
	<h3>Image/Gallery Effects</h3>
	<blockquote>
	In some cases Wordpress and web browsers will scale the display of the full size image in your gallery to be smaller than it really is (or you can set the option above to limit the maximum size of the full size image to match your theme)--if you want your users to be able to view the actual image file in a popup window or popup layer when clicking on these scaled images then this option is for you:
	<br /><br />

	PixGallery's Built In Javascript Image Display method (special effect when Image/Thumbnail is clicked)
		<br />
		<select name='PixGallery_PopupMethod'>

			<option value='None'	<?php if ($GLOBALS["PixGallery_PopupMethod"] == 'None') echo "selected"; ?>>No Javascript</option>
			<option value='Window'	<?php if ($GLOBALS["PixGallery_PopupMethod"] == 'Window') echo "selected"; ?>>Display in Separate Floating Window</option>
			<option value='Layer'	<?php if ($GLOBALS["PixGallery_PopupMethod"] == 'Layer') echo "selected"; ?>>Lightbox Effect Overlay</option>

		</select>
		<br /><br />
	<!--- ***** Use New Window for Full Images mod ***** -->
	If "No Javascript" is selected, do you want full size images to open in a new window instead of within the same window when they are clicked?
	<br />
							<select name='PixGallery_OpenInNewWindow'>
						<option value='0'	<?php if ($GLOBALS["PixGallery_OpenInNewWindow"] == '0') echo "selected"; ?>>No</option>
							<option value='1'	<?php if ($GLOBALS["PixGallery_OpenInNewWindow"] == '1') echo "selected"; ?>>Yes</option>
							</select>
						
	<!--- ***** end New Window for Full Images mod ***** -->
	
	<br /><br />
	<!--- ***** Click Image to advance mod ***** -->
	What do you want to happen when someone clicks on an image on it's own page? (note: setting this to "advance to the next image" while also using a lightbox effect can lead to unexpected behavior, also if the previous setting is set to "Yes" this setting will be ignored)
	<br />
							<select name='PixGallery_ImageClickBehavior'>
						<option value='0'	<?php if ($GLOBALS["PixGallery_ImageClickBehavior"] == '0') echo "selected"; ?>>Show The Full Size Image</option>
							<option value='1'	<?php if ($GLOBALS["PixGallery_ImageClickBehavior"] == '1') echo "selected"; ?>>Advance To The Next Image In The Gallery</option>
							</select>
						
	<!--- ***** end Click Image to advance mod ***** -->
	<br /><br />
	If you want to use a 3rd party javascript plugin and turn off the built in system, set the following setting to "custom" and input the custom rel code needed to add the effect to your images in the following box.
		<br />
		<select name='PixGallery_CustomPopup'>

			<option value='0'	<?php if ($GLOBALS["PixGallery_CustomPopup"] == '0') echo "selected"; ?>>Use Built In Effects</option>
			<option value='1'	<?php if ($GLOBALS["PixGallery_CustomPopup"] == '1') echo "selected"; ?>>Use Custom Effects Plugin</option>

		</select>
	<br />
	Custom Image code for effect (typical example: lightbox) <input type='text' name='PixGallery_CustomRel' style='width: 100px;' value='<?php echo $GLOBALS["PixGallery_CustomRel"]; ?>'>
	<br /><br />
	
	To have the full size image popup occur when clicking on the thumbnail image rather than when clicking on the scaled images check off this option.
	<br />
	<?php if (isset($GLOBALS["PixGallery_PopupOnThumbnail"])) { ?>
			<input type="checkbox" name="PixGallery_PopupOnThumbnail" checked>
		<?php } else { ?>
			<input type="checkbox" name="PixGallery_PopupOnThumbnail">
		<?php } ?>
	Thumbnail popup
</blockquote>
	<h3>Navigation/Page Display Elements</h3>
	<blockquote>
	<!--- ***** Share This Integration Option mod ***** -->
				<b>ShareThis Integration or Sharing Links</b>: Would you like Pixgallery to Automatically integrate with ShareThis? (requires ShareThis Plugin to be installed on your website). If you are not using "ShareThis" turning this option on will display clickable links instead.
				<br />
						<select name='PixGallery_ShareThisConnector'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_ShareThisConnector"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_ShareThisConnector"] == '1') echo "selected"; ?>>Yes</option>
						</select>
					<br /><br />
	<!--- ***** end Share This Integration Option mod ***** -->
	<!--- ****Mod added for Limiting Pagination Links ***** -->
				Pixgallery Limit Pagination Links for Thumbnail View: This Setting lets you limit the number of Pagination links that display under your Gallery when you have images on multiple pages.
				<br />
					Number of Links to Display (0 = Unlimited):<input type='text' name='PixGallery_LimitPaginationLinks' style='width: 40px;' value='<?php if ($GLOBALS["PixGallery_LimitPaginationLinks"] == ""){ $GLOBALS["PixGallery_LimitPaginationLinks"] = "0";} echo $GLOBALS["PixGallery_LimitPaginationLinks"]; ?>'><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/limit_pagination_links_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Limit Pagination Links setting" alt="Get Help on the Limit Pagination Links Setting"></a>
					<br /><br />
	<!--- *****end Limiting Pagination Links mod ***** -->
	<!--- ***** Set Maximum Thumbnails to display on index page view of gallery ***** -->
					Do you want to limit the number of Thumbnails displayed on the index page for a gallery (set to 0 to disable this feature)? If so input the max number to display in the following box.
					<br />
						<input type='text' name='PixGallery_MaxThumbnailsOnIndex' style='width: 20px;' value='<?php echo $GLOBALS["PixGallery_MaxThumbnailsOnIndex"]; ?>'>
						<br /><br />
	<!--- ***** end Maximum Thumbnails to display on index page view of gallery mod ***** -->
	<!--- ***** Use Turn off Links in Thumbails page mod ***** -->
				Would you like to turn off titles and link text under Thumbnails?
				<br />
						<select name='PixGallery_NoThumbnailText'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_NoThumbnailText"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_NoThumbnailText"] == '1') echo "selected"; ?>>Yes</option>
						</select>
					<br /><br />
	<!--- ***** end Turn off links in Thumbnails mod ***** -->
	<!--- ****Mod added for Extra Nav Bars in Gallery***** -->
		Do you want to display Previous/Next Links under your Images as well as above them?
		<br />
			<select name='PixGallery_ExtraNavBars'>

				<option value='0'	<?php if ($GLOBALS["PixGallery_ExtraNavBars"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_ExtraNavBars"] == '1') echo "selected"; ?>>Yes</option>

			</select>
			<br /><br />
	<!--- *****end Extra Nav Bars Mod ***** -->
	<!--- ****Mod added for Individual Comments***** -->
			Pixgallery Individual Comments: set to yes to turn on the individual commenting system. (Comments added are "sticky" to the image/gallery being displayed when entered.)
			<br />
				<select name='PixGallery_IndividualComments'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_IndividualComments"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_IndividualComments"] == '1') echo "selected"; ?>>Yes</option>

				</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/individual_comments_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Individual Comments setting" alt="Get Help on the Individual Comments Setting"></a>
				<br /><br />
	<!--- *****end Individual Comments mod ***** -->
	<!--- ***** Hide Sidebar Option mod ***** -->
		Would you like to hide the display of the Sidebar on Image Gallery Large Thumbnail Pages (only works with Themes that call sidebar.php as the sidebar)?
		<br />
				<select name='PixGallery_HideSidebar'>
			<option value='0'	<?php if ($GLOBALS["PixGallery_HideSidebar"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_HideSidebar"] == '1') echo "selected"; ?>>Yes</option>
				</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/hide_sidebar_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on Hide Sidebar setting" alt="Get Help on the Hide Sidebar Setting"></a>
				<br /><br />
	<!--- ***** end Hide Sidebar mod ***** -->
	<!--- ***** Breadcrumb display Option mod ***** -->
		Would you like to hide the display of the Breadcrumb in galleries and image pages?
		<br />
				<select name='PixGallery_ShowBreadCrumb'>
			<option value='0'	<?php if ($GLOBALS["PixGallery_ShowBreadCrumb"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_ShowBreadCrumb"] == '1') echo "selected"; ?>>Yes</option>
				</select><br /><br />
	<!--- ***** endBreadcrumb display mod ***** -->
	<!--- ***** Use Folder Icon for Galleries Mod ***** -->
		Would you like to have a Folder icon Added To your Galleries in the Thumbnail view? (this is the default icon: <img src="<?php echo $PixGlobal_AdminPath; ?>images/folder-image-icon.png"> )
		<br />
				<select name='PixGallery_UseFolderIcon'>
			<option value='0'	<?php if ($GLOBALS["PixGallery_UseFolderIcon"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_UseFolderIcon"] == '1') echo "selected"; ?>>Yes</option>
				</select><br />
				You can use your own icon, type the name of the file here: <input type='text' name='PixGallery_UseFolderIconCustom' style='width: 150px;' value='<?php echo $GLOBALS["PixGallery_UseFolderIconCustom"]; ?>'> (put the file in the awsom-customizations folder in wp-content/uploads/)
				<br />
	<!--- ***** end Use Folder Icon for Galleries mod ***** -->
	
	</blockquote>
	
	<h3>RSS and Header SEO Options</h3>
	<blockquote>
	<!--- ****Mod for displaying RSS and SEO Header links for your galleries ***** -->
				
				Would you like to have Pixgallery add your Image/Gallery Name to the Title Tag in the header of your site when viewing an image/gallery?
				<br />
						<select name='PixGallery_SEOTitle'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_SEOTitle"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_SEOTitle"] == '1') echo "selected"; ?>>Yes</option>
						</select>
						What Do You Want Displayed?: <select name='PixGallery_SEOTitleContent'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_SEOTitleContent"] == '0') echo "selected"; ?>>Image/Gallery Title</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_SEOTitleContent"] == '1') echo "selected"; ?>>Full Path To Image/Gallery</option>
						</select>
						Where To Display (in Relation To Page Title): <select name='PixGallery_SEOTitlePlace'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_SEOTitlePlace"] == '0') echo "selected"; ?>>After</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_SEOTitlePlace"] == '1') echo "selected"; ?>>Before</option>
						<option value='2'	<?php if ($GLOBALS["PixGallery_SEOTitlePlace"] == '2') echo "selected"; ?>>Totally Replace Title</option>
						</select>
					<br /><br />
				
				Would you like to have Pixgallery add your Image/Gallery Tags as Keywords to the header of your site when viewing an image/gallery?
				<br />
						<select name='PixGallery_MetaKeywords'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_MetaKeywords"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_MetaKeywords"] == '1') echo "selected"; ?>>Yes</option>
						</select>
					<br />
												
				Would you like to have RSS feeds for all of your Galleries?
				<br />
						<select name='PixGallery_DisplayRSS'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_DisplayRSS"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_DisplayRSS"] == '1') echo "selected"; ?>>Yes</option>
						</select>
					<br />
					Would you like to have a link to the RSS feed appear under the Gallery on the page?
				<br />
						<select name='PixGallery_DisplayRSSLink'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_DisplayRSSLink"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_DisplayRSSLink"] == '1') echo "selected"; ?>>Yes</option>
						</select>
					<br />
					What do you want the RSS to Link to (default is to view in Wordpress page):
				<br />
						<select name='PixGallery_RSSFeedLinkType'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_RSSFeedLinkType"] == '0') echo "selected"; ?>>View in WordPress Page</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_RSSFeedLinkType"] == '1') echo "selected"; ?>>Go directly to the Image File</option>
						</select>	
					
					<br />
					How Often Do you want to update your Feeds? (default is every 24 hours, make time shorter only if you are constantly adding new images):
				<br />
						<select name='PixGallery_RSSFeedFileInterval'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '0') echo "selected"; ?>>Every 24 hours</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '1') echo "selected"; ?>>Every 12 hours</option>
						<option value='2'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '2') echo "selected"; ?>>Every 6 hours</option>
						<option value='3'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '3') echo "selected"; ?>>Every 3 hours</option>
						<option value='4'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '4') echo "selected"; ?>>Every hour</option>
						<option value='5'	<?php if ($GLOBALS["PixGallery_RSSFeedFileInterval"] == '5') echo "selected"; ?>>Immediately (constant updating)</option>
						</select>	
					
					<br />
					Do You Want To Make Your Feeds Into Photo Feeds?
				<br />
						<select name='PixGallery_RSSPhotoFeed'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_RSSPhotoFeed"] == '0') echo "selected"; ?>>Yes</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_RSSPhotoFeed"] == '1') echo "selected"; ?>>No</option>
						</select>	
					
					<br />
					Number of Items in the RSS feed (Default is 10): <input type='text' name='PixGallery_RSSFeedNumber' style='width: 40px;' value='<?php if ($GLOBALS["PixGallery_RSSFeedNumber"] == ""){ $GLOBALS["PixGallery_RSSFeedNumber"] = "10";} echo $GLOBALS["PixGallery_RSSFeedNumber"]; ?>'>
					<br />
					What do name do you want to use for the "Creator" field (typically this is the Artist, Company or Owners name): <input type='text' name='PixGallery_RSSCreatorEntry' style='width: 250px;' value='<?php if ($GLOBALS["PixGallery_RSSCreatorEntry"] == ""){ $GLOBALS["PixGallery_RSSCreatorEntry"] = "My Name";} echo $GLOBALS["PixGallery_RSSCreatorEntry"]; ?>'>
					<br />
					What entry do you want for the "Rights" field (Your copyright statement goes here): <input type='text' name='PixGallery_RSSRightsEntry' style='width: 250px;' value='<?php if ($GLOBALS["PixGallery_RSSRightsEntry"] == ""){ $GLOBALS["PixGallery_RSSRightsEntry"] = "Copyright Statement";} echo $GLOBALS["PixGallery_RSSRightsEntry"]; ?>'>
					<br />
	<!--- *****end for displaying RSS and SEO Header links for your galleries mod ***** -->
	
	</blockquote>
	
	
	
	<h2>Plugin Options</h2>
	<h3>Administrative Options</h3>
	<blockquote>
	<!--- ****Mod added for Reported Plugin Conflicts With YARPP and other post/page scanning plugins***** -->
		Disable Admin Area Scanning: Set this to "Yes" if you are getting any "Path Not found" errors in your admin area (and only in admin, this has nothing to do with standard "path not found" errors in the front end due to incorrect gallery paths) when trying to add a new post/page with a gallery. This setting turns off pixgallery scanning on admin pages. If you don't have any errors leave this setting set to "No".
		<br />
			<select name='PixGallery_AdminFix'>

				<option value='0'	<?php if ($GLOBALS["PixGallery_AdminFix"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_AdminFix"] == '1') echo "selected"; ?>>Yes</option>
			</select>
			<br /><br />
	<!--- *****end Legacy Mode mod ***** -->
	
	<!--- ****Mod added for Legacy Mode***** -->
		Pixgallery Legacy Mode support: Set this to yes if you are upgrading from a previous version and still need to update your image captions to the new system. Not needed for new installs.
		<br />
			<select name='PixGallery_LegacyMode'>

				<option value='0'	<?php if ($GLOBALS["PixGallery_LegacyMode"] == '0') echo "selected"; ?>>No</option>
				<option value='1'	<?php if ($GLOBALS["PixGallery_LegacyMode"] == '1') echo "selected"; ?>>Yes</option>

			</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/legacy_mode_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Legacy Mode setting" alt="Get Help on the Legacy Mode Setting"></a>
			<br /><br />
	<!--- *****end Legacy Mode mod ***** -->
	<!--- ****Mod added for Debug/Admin Mode***** -->
			Pixgallery Debug/Admin Mode: Set this to yes if you want to see more advanced information and get Debug info when logged in as Admin. If you select "Advanced" you can edit things directly from the front end, but this might mess up your theme a bit (but only admins would see this, not regular users)
		<br />
				<select name='PixGallery_DebugMode'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_DebugMode"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_DebugMode"] == '1') echo "selected"; ?>>Yes</option>

				</select><select name='PixGallery_DebugModeType'>

					<option value='0'	<?php if ($GLOBALS["PixGallery_DebugModeType"] == '0') echo "selected"; ?>>Simple (info only)</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_DebugModeType"] == '1') echo "selected"; ?>>Advanced (Edit Mode)</option>

				</select>
				<a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/debug_mode_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Debug Mode setting" alt="Get Help on the Debug Mode Setting"></a>
				<br /><br />
	<!--- *****end Debug Mode mod ***** -->
	
	<!--- ***** Use Visual Editor Option mod ***** -->
			Would you like to use the advanced Visual Editor to edit your Image and Gallery Captions?
			<br />
					<select name='PixGallery_UseVisualEditor'>
				<option value='0'	<?php if ($GLOBALS["PixGallery_UseVisualEditor"] == '0') echo "selected"; ?>>No</option>
					<option value='1'	<?php if ($GLOBALS["PixGallery_UseVisualEditor"] == '1') echo "selected"; ?>>Yes</option>
					</select>
					<br /><br />
	<!--- ***** end Use Visual Editor mod ***** -->

	<!--- ***** Use Absolute Path for Cache Images mod ***** -->
				Would you like to turn on Absolute Linking Path support? (only needed if you are using Fancy Permalinks AND have Wordpress in a subfolder and your thumbnail images are not appearing properly)
				<br />
						<select name='PixGallery_UseAbsPath'>
					<option value='0'	<?php if ($GLOBALS["PixGallery_UseAbsPath"] == '0') echo "selected"; ?>>No</option>
						<option value='1'	<?php if ($GLOBALS["PixGallery_UseAbsPath"] == '1') echo "selected"; ?>>Yes</option>
						</select><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/absolute_linking_setting.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on the Absolute Linking setting" alt="Get Help on the Absolute Linking Setting"></a>
					<br /><br />
	<!--- ***** end Use Absolute Path for Cache Images mod ***** -->
	
		
	<!--- ***** AWSOM Footer Credit mod ***** -->
						Would you like Support AWSOM Pixgallery by adding a credit to AWSOM.org in your footer? (It's appreciated, but not required)
						<br />
								<select name='PixGallery_AddFooterCredit'>
							<option value='0'	<?php if ($GLOBALS["PixGallery_AddFooterCredit"] == '0') echo "selected"; ?>>No</option>
								<option value='1'	<?php if ($GLOBALS["PixGallery_AddFooterCredit"] == '1') echo "selected"; ?>>Yes</option>
							</select>
	<!--- ***** end AWSOM Footer Credit mod ***** -->
</blockquote>

			
			<br />

	
<h3>Cache Path</h3>
<blockquote>
Relative path to your thumbnail/watermarked images cache directory which has 777 write permissions. This directory is used to store the thumbnail and watermarked images that are automatically created by Pixgallery.<br /><br />
	<input type='text' name='PixGallery_RootCache' style='width: 350px;' value='<?php echo $GLOBALS["PixGallery_RootCache"]; ?>'><a href="<?php echo $PixGlobal_AdminPath; ?>help_docs/cache_folder_settings.txt" style="text-decoration:none" target="_blank"><img src="<?php echo $PixGlobal_AdminPath; ?>images/help.png" border="0" align="top" title="Click here to get Help on Cache Folder settings" alt="Get Help on the Cache Folder Setting"></a>


</blockquote>
<br />

</fieldset>
<?php $showCreateCacheLink = get_option("awsom_pixgallery_cache_created");
if ($showCreateCacheLink !== "1") { ?>
If you would like to switch to and create a default cache folder click the "Create Default Cache" button.<br />
		<div class="submit"><input type="submit" name="createdefaultcache" value="Create Default Cache"></div>
		<?php } else { ?>
			Default Cache Folder Already Created.
		<?php } ?>
		</blockquote>
<div class="submit"><input type="submit" name="info_update" value="Apply Options" /></div>

</form>
Your Support is Always Appreciated: <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="Harknell@optonline.net">
<input type="hidden" name="item_name" value="AWSOM.org Web Development Donation">
<input type="hidden" name="no_shipping" value="0">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div>

<?php
}

// ***** AWSOM Gallery admin area mod *****//

 function PixGallery_Manage_Galleries() {
 global $wpdb, $awsomcreatedefaultgallerydir, $PixGlobal_Path, $PixGlobal_AdminPath;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $PixGallery_UseVisualEditor;

if ($PixGallery_UseVisualEditor == 1){  ?>
<script type="text/javascript" src="<?php echo $PixGlobal_AdminPath; ?>support_files/tiny_mce/tiny_mce.js"></script>



	<script type="text/javascript">
	tinyMCE.init({
		// General options
		theme : "advanced",
		plugins : "safari,pagebreak,table,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras",
		mode : "textareas",
        elements : "newgallerycaption, changedgalcaption",
        relative_urls : false,
        remove_script_host : false,
        width : "565",
	height : "250",
	skin : "wp_theme",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true



	});
</script>
<!-- /TinyMCE -->
<?php }

if (isset($_POST['admin_edit_gallerylink_on_page'])){
	/* get new gallery input from edit link on thumbnail view page */
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     			 die(__('Cheating uh?'));
		$editgalleryfrompagepath = $_POST['editlinkgallerypath'];
	$editgalleryfrompagepath = trim($editgalleryfrompagepath);
	$editgalleryfrompagepath = $wpdb->escape($editgalleryfrompagepath);
	$takemebacktogallery = $_POST['takemeback'];
	$takemebacktogallery = trim($takemebacktogallery);
	$takemebacktogallery = $wpdb->escape($takemebacktogallery);

	}

if (isset($_POST['mass_edit_gallery_captions_onpage'])){
	/* get and process mass edits from gallery Front End */
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     			 die(__('Cheating uh?'));
		$editgalleryfrompagepath = $_POST['editlinkgallerypath'];
	$editgalleryfrompagepath = trim($editgalleryfrompagepath);
	$editgalleryfrompagepath = $wpdb->escape($editgalleryfrompagepath);
	$takemebacktogallery = $_POST['takemeback'];
	$takemebacktogallery = trim($takemebacktogallery);
	$takemebacktogallery = $wpdb->escape($takemebacktogallery);
	$FEnumtoprocess = $_POST['FEtotaledits'];
	$FEnumtoprocess = $wpdb->escape($FEnumtoprocess);
	$comingfrompaginated = $_POST['pxg_paginated'];
	$comingfrompaginated = trim($comingfrompaginated);
	$comingfrompaginated = $wpdb->escape($comingfrompaginated);
	
	if ($comingfrompaginated == 1){
	$FEnumtoprocess = $FEnumtoprocess + 1;
	}
	$pxg_loopnum = 0;
	echo "<div class='updated'><p>";
	while ($pxg_loopnum < $FEnumtoprocess) {
	 
	$itempathmatch = "FEdbmatch".$pxg_loopnum;
	$itemsortmatch = "FESortNumber".$pxg_loopnum;
	$itemnamematch = "FECustomName".$pxg_loopnum;
	$itemtypematch = "FEitemtype".$pxg_loopnum;
	$itemrealnamematch = "FErealitemname".$pxg_loopnum;
	$itemcaptionmatch = "FEimagecaption".$pxg_loopnum;
	
	$FEimagecaption = $_POST[$itemcaptionmatch];
	$FEimagecaption = trim($FEimagecaption);
	$FEimagecaption = $wpdb->escape($FEimagecaption);
	$FEimagerealname = $_POST[$itemrealnamematch];
	$FEimagerealname = trim($FEimagerealname);
	$FEimagerealname = $wpdb->escape($FEimagerealname);
	$FEimagepath = $_POST[$itempathmatch];
	$FEimagepath = trim($FEimagepath);
	$FEimagepath = $wpdb->escape($FEimagepath);
	$FEitemname = $_POST[$itemnamematch];
	$FEitemname = trim($FEitemname);
	$FEitemname = $wpdb->escape($FEitemname);
	$FEitemsort = $_POST[$itemsortmatch];
	$FEitemsort = trim($FEitemsort);
	$FEitemsort = $wpdb->escape($FEitemsort);
	$FEitemtype = $_POST[$itemtypematch];
	$FEitemtype = trim($FEitemtype);
	$FEitemtype = $wpdb->escape($FEitemtype);
	
	
	$whatgalleryamifrom = $wpdb->get_var("SELECT apgid FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$editgalleryfrompagepath'");
	if ($whatgalleryamifrom == "" | $whatgalleryamifrom ==NULL){
	$whatgalleryamifrom = get_option('awsom_pixgallery_default_gallery');
	}
	//debug output
	//echo "item name received=$FEitemname | item path received=$FEimagepath | item sort received=$FEitemsort | item type received=$FEitemtype real name = $FEimagerealname<br />";
		if ($FEitemtype == "Photo"){
		$amiindb = $wpdb->get_var("SELECT capid FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$FEimagepath'");
			if ($amiindb == "" | $amiindb == NULL){
			$wpdb->query("
		INSERT INTO $awsom_pixgallery_caption_table_name (capid, name, imagepath, captiontext, customname,  sortid, imgtags, parentgallery )
		VALUES (null, '$FEimagerealname', '$FEimagepath', '$FEimagecaption', '$FEitemname', '$FEitemsort', '$newimagetagsfordb', '$whatgalleryamifrom')");
			}
			else {
			$wpdb->query("UPDATE $awsom_pixgallery_caption_table_name SET customname='$FEitemname', sortid='$FEitemsort', captiontext='$FEimagecaption' WHERE capid='$amiindb'");
			}
		}
		if ($FEitemtype == "Gallery"){
		$amiindb = $wpdb->get_var("SELECT apgid FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$FEimagepath'");
			if ($amiindb == "" | $amiindb == NULL){
			$wpdb->query("
		INSERT INTO $awsom_pixgallery_gallery_table_name (apgid, galleryname, galpath, galcaptiontext, galcustomname,  sortid )
		VALUES (null, '$FEimagerealname', '$FEimagepath', '$newimagecaptionfordb', '$FEitemname', '$FEitemsort')");
			}
			else {
			
			$wpdb->query("UPDATE $awsom_pixgallery_gallery_table_name SET galcustomname='$FEitemname', sortid='$FEitemsort' WHERE apgid='$amiindb'");
			}
		}
	$pxg_loopnum = $pxg_loopnum + 1;
	}
	echo "<p>All Mass Edits Added to Database</p>";
	//echo "number of items processed = ".$pxg_loopnum;
	echo "<p><a href='$takemebacktogallery'>Click Here to Return to the Gallery Page Just Edited</a></p></div>";
	
	}





if (isset($_POST['gallery_update_add']))
		{
		/* add Gallery to Database */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));
      	check_admin_referer('apg_add_gallery_options');
      	$takemebacktogallerypage = $_POST['takemebacktogallerypage'];
      	$takemebacktogallerypage = $wpdb->escape($takemebacktogallerypage);
		$newgallerynamefordb = $_POST['newgalleryname'];
		$newgallerynamefordb = trim($newgallerynamefordb);
		$newgallerycustomnamefordb = $_POST['customnewgalleryname'];
		$newgallerycustomnamefordb = trim($newgallerycustomnamefordb);
		$newgallerysortidfordb = $_POST['newgallerysortid'];
		$newgallerysortidfordb = trim($newgallerysortidfordb);
		$newgallerytagsfordb = $_POST['newgallerytags'];
		$newgallerycaptionfordb = $_POST['newgallerycaption'];
		$newgallerypathfordb = $_POST['newgallerypath'];
		$newgallerypathfordb = trim($newgallerypathfordb);
		$newgallerysettingfordb = $_POST['newgallerysetting'];
		$newgalleryisnowdefault = $_POST['newgalleryisdefault'];
		$newgallerynamefordb = $wpdb->escape($newgallerynamefordb);
		$newgallerycustomnamefordb = $wpdb->escape($newgallerycustomnamefordb);
		$newgallerysortidfordb = $wpdb->escape($newgallerysortidfordb);
		$newgallerytagsfordb = $wpdb->escape($newgallerytagsfordb);
		$newgallerycaptionfordb = $wpdb->escape($newgallerycaptionfordb);
		$newgallerypathfordb = $wpdb->escape($newgallerypathfordb);
		$newgallerysettingfordb = $wpdb->escape($newgallerysettingfordb);
		$newgalleryisnowdefault = $wpdb->escape($newgalleryisnowdefault);
		if ($newgallerynamefordb != "" && $newgallerypathfordb !=""){
		$wpdb->query("
		INSERT INTO $awsom_pixgallery_gallery_table_name (apgid, galleryname, galcustomname,  sortid, galpath, galtags, galsettings, galcaptiontext )
		VALUES (null, '$newgallerynamefordb', '$newgallerycustomnamefordb', '$newgallerysortidfordb','$newgallerypathfordb', '$newgallerytagsfordb', '$newgallerysettingfordb', '$newgallerycaptionfordb')");
		if ($newgalleryisnowdefault == 1) {
		$getgalleriesapgidfromdb = $wpdb->get_var('SELECT last_insert_id()');
		update_option("awsom_pixgallery_default_gallery", $getgalleriesapgidfromdb);
		}
		echo "<div class='updated'><p><strong>Gallery added successfully!</strong></p></div>";
		if ($takemebacktogallerypage !=""){
				echo "<div class='updated'><p><a href='$takemebacktogallerypage'>Click Here to Return to the Gallery Page</a></p></div>";
		}
		}
		else{
		echo "<div class='updated'><p><strong>No Gallery Name or Path supplied!</strong></p></div>";
		}
		}

	if (isset($_POST['gallery_update_delete']))
		{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));

		$isthisgallerydefault = get_option('awsom_pixgallery_default_gallery');
		if ($_POST['gallerytochange'] != $isthisgallerydefault ){
		/* delete gallery from Database */
		$gallerytobedeleted = $_POST['gallerytochange'];
		$gallerytobedeleted = $wpdb->escape($gallerytobedeleted);
		if ($gallerytobedeleted !=""){
		$wpdb->query("
		DELETE FROM $awsom_pixgallery_gallery_table_name WHERE apgid='$gallerytobedeleted'");
		$MoveCaptionsToDefault = get_option('awsom_pixgallery_default_gallery');
		$wpdb->query("UPDATE $awsom_pixgallery_caption_table_name SET parentgallery = '$MoveCaptionsToDefault' WHERE parentgallery='$gallerytobedeleted'");
		echo "<div class='updated'><p><strong>Gallery deleted successfully!</strong></p></div>";
		}
		else{ echo "<div class='updated'><p><strong>You Did Not Select a Gallery To Delete.</strong></p></div>";}
		}
		else{
		echo "<div class='updated'><p><strong>You Can't Delete the Default Gallery.</strong></p></div>";
		}

		}
	if (isset($_POST['createdefaultgallery']))
		{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));

			if (wp_mkdir_p($awsomcreatedefaultgallerydir)){
		    update_option ("awsom_pixgallery_defaultgallery_created", 1);
		    //$initialgalleryname = "Default";
			//$initialgalsettings = 1;
			//$initialgalpath = "/" . get_option( "upload_path" ) . "/awsompixgallery/";
			//$wpdb->query("INSERT INTO $awsom_pixgallery_gallery_table_name (apgid, galleryname, galsettings, galpath) VALUE (NULL, '$initialgalleryname', '$initialgalsettings', '$initialgalpath')");
			//$lastmadeapgid = $wpdb->get_var('SELECT last_insert_id()');
	  		//update_option("awsom_pixgallery_default_gallery", $lastmadeapgid);
			  echo "<div class='updated'><p><strong>Successfully Created Uploads area Gallery folder!</strong></p></div>";
      		}
	else 	{
			echo "<div class='updated'><p><strong>Could Not Create Uploads Area Gallery Folder!</strong></p></div>";
			}


		}
	if (isset($_POST['gallery_generate_code']))
		{

		/* Generate gallery code */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     	 die(__('Cheating uh?'));

		$gallerytobegenerated = $_POST['gallerytochange'];
		$gallerytobegenerated = $wpdb->escape($gallerytobegenerated);
		if ($gallerytobegenerated !=""){
		$thegallerypath = $wpdb->get_var("SELECT galpath FROM $awsom_pixgallery_gallery_table_name WHERE apgid='$gallerytobegenerated'");
		$thegallerypath = stripslashes($thegallerypath);
		$showthegeneratedcode = "[pixgallery path=\"" . $thegallerypath . "\"][/pixgallery]";

		echo "<div class='updated'><p><strong>Add this code to your Post/Page:</strong> $showthegeneratedcode</p></div>";
		}
		else{ echo "<div class='updated'><p><strong>You Did Not Select a Gallery.</strong></p></div>";
		}

		}

		if (isset($_POST['gallery_update_edit']))
				{
				if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     			 die(__('Cheating uh?'));

				if ($_POST['gallerytochange'] != ""){
				/* edit gallery from Database */
				$reloadallcaptions = $_POST['display_all_db_entries_after_edit'];
				$reloadallcaptions = $wpdb->escape($reloadallcaptions);
				$takemebacktogallery = $_POST['takemeback'];
				$takemebacktogallery = trim($takemebacktogallery);
				$takemebacktogallery = $wpdb->escape($takemebacktogallery);
				$gallerytobeedited = $_POST['gallerytochange'];
				$gallerytobeedited = $wpdb->escape($gallerytobeedited);
				$gallerytexttobeedited = $wpdb->get_results("SELECT galleryname, galcustomname, sortid, galpath, galtags, galsettings, galcaptiontext FROM $awsom_pixgallery_gallery_table_name WHERE apgid='$gallerytobeedited'");
				$gallerymightbecomedefault = get_option('awsom_pixgallery_default_gallery');
				if ($gallerymightbecomedefault == $gallerytobeedited){
				$optionlistfordefault = "<option value=\"1\">This is the Default Gallery</option>";
				}
				else { $optionlistfordefault = "<option value=\"0\" Selected>No</option><option value=\"1\" >Yes</option>";
				}
				foreach ($gallerytexttobeedited as $thisgallery){
				$thisgallery->galleryname = stripslashes($thisgallery->galleryname);
				$thisgallery->galcustomname = stripslashes($thisgallery->galcustomname);
				$thisgallery->sortid = stripslashes($thisgallery->sortid);
				$thisgallery->galpath = stripslashes($thisgallery->galpath);
				$thisgallery->galtags = stripslashes($thisgallery->galtags);
				$thisgallery->galsettings = stripslashes($thisgallery->galsettings);
				$thisgallery->galcaptiontext = stripslashes($thisgallery->galcaptiontext);
				$updatethisgalname = $thisgallery->galleryname;
				$updatethisgalsettings = $thisgallery->galsettings;
				$updatethisgalcustomname = $thisgallery->galcustomname;
				$updatethisgalsortid = $thisgallery->sortid;
				$updatethisgaltags = $thisgallery->galtags;
				$updatethisgalcaption = $thisgallery->galcaptiontext;
				$updatethisgallerypath = $thisgallery->galpath;

				}

				//$galleriesfromdatabasetoedit = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_gallery_table_name");
								//$markgalleryselected = "";
					   			//foreach ($galleriesfromdatabasetoedit as $thisgalleriessdata) {
					   			//stripslashes($thisgalleriessdata->galleryname);
					   			//if ($thisgalleriessdata->apgid == $updatethisparentgallery) {
					   			//$markgalleryselected = "Selected";
					   			//}
					   				//$showthesettingsresults .= "<option value=\"$thisgalleriessdata->apgid\" $markgalleryselected>$thisgalleriessdata->galleryname</option>";
									//$markgalleryselected = "";
			//}


			echo "<div class=\"updated\"><form method=\"post\">";
			if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_edit_gallery_options'); }

			echo"<fieldset class=\"galleries\">
			<legend>Click \"Update Gallery\" to update this Gallery.</legend>
			<blockquote>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td width=\"360\">(<b>Required</b>) Gallery Name:<br />
						<input type='text' value=\"$updatethisgalname\" name=\"changedgalleryname\" style=\"width: 150px;\"></td></tr>
					<tr>
						<td width=\"360\">Custom Gallery Name For Display:<br />
						<input type='text' value=\"$updatethisgalcustomname\" name=\"changedcustomgalname\" style=\"width: 150px;\"></td></tr>
					<tr>
					<tr>
						<td width=\"400\">(<b>Required</b>) Path To Gallery (from root folder of your Wordpress website--start with a / and end with a /):<br />
						<input type='text' value=\"$updatethisgallerypath\" name=\"changedgalpath\" style=\"width: 350px;\"></td></tr>
					<tr>
											<td width=\"360\">Position in Gallery (if subgallery):<br />
						<input type='text' value=\"$updatethisgalsortid\" name=\"changedgalsortid\" style=\"width: 150px;\"></td></tr>
					<tr>
					<td width=\"360\">Use Display Options Settings: (coming soon)<br />
					<select name=\"changedsettingsmember\">
					<option value=\"1\">Global</option>

					</select>

					</td>
					</tr>
					<tr>
						<td width=\"600\">Gallery Tags (Separate by Spaces):<br />
						<input type='text' value=\"$updatethisgaltags\" name=\"changedgaltags\" style=\"width: 500px;\"></td></tr>
						<tr><td><br />
						Gallery Caption: [<b>please use HTML &lt;br&gt; to add multi-line caption text</b>]
						<textarea  name=\"changedgalcaption\" rows=\"4\" cols=\"80\">$updatethisgalcaption</textarea></td>
						<input type='hidden' name=\"updateapgid\" value=\"$gallerytobeedited\">
						<input type='hidden' name=\"takemebacktogallerypage\" value=\"$takemebacktogallery\">";
						if ($reloadallcaptions != ""){
						echo "<input type='hidden' name=\"display_all_gallery_db_entries\" value=\"$reloadallcaptions\">";
						}
						echo"</tr>
					<tr>
												<td width=\"360\">Make This Gallery Default?:<br />
												<select name=\"changegallerytodefault\">

											$optionlistfordefault


												</select>

												</td>
		</tr>
				</table>
				<br />
			</blockquote>
			</fieldset>
			<div class=\"submit\" style=\"text-align: left;\"><input type=\"submit\" name=\"gallery_update_now\" value=\"Update Gallery\" /></div>

</form></div>";

		}
		else{
		echo "<div class='updated'><p><strong>No Gallery selected to edit! Please select a Gallery from the dropdown list.</strong></p></div>";
		}
		}
	if (isset($_POST['gallery_update_now']))
		{
		/* update Gallery in Database */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      	die(__('Cheating uh?'));
		check_admin_referer('apg_edit_gallery_options');
		$updatedgalleryname = $_POST['changedgalleryname'];
		$updatedgalleryname = trim($updatedgalleryname);
		$updatedcustomgalname = $_POST['changedcustomgalname'];
		$updatedcustomgalname = trim($updatedcustomgalname);
		$updatedgallerysortid = $_POST['changedgalsortid'];
		$updatedgallerysortid = trim($updatedgallerysortid);
		$updatedgaltags = $_POST['changedgaltags'];
		$updatedgalcaptiontext = $_POST['changedgalcaption'];
		$updatethisapgid = $_POST['updateapgid'];
		$updategalsettings = $_POST['changedsettingsmember'];
		$updategalpath = $_POST['changedgalpath'];
		$takemebacktothegallerypage = $_POST['takemebacktogallerypage'];
		$updategalpath = trim($updategalpath);
		$updatedefaultgalleryindb = $_POST['changegallerytodefault'];
		$updatedgalleryname = $wpdb->escape($updatedgalleryname);
		$updatedcustomgalname = $wpdb->escape($updatedcustomgalname);
		$updatedgallerysortid = $wpdb->escape($updatedgallerysortid);
		$updatedgaltags = $wpdb->escape($updatedgaltags);
		$updatedgalcaptiontext = $wpdb->escape($updatedgalcaptiontext);
		$updategalsettings = $wpdb->escape($updategalsettings);
		$updategalpath = $wpdb->escape($updategalpath);
		$updatedefaultgalleryindb = $wpdb->escape($updatedefaultgalleryindb);
		$takemebacktothegallerypage = $wpdb->escape($takemebacktothegallerypage);
		if ($updatedgalleryname != "" && $updategalpath !=""){
		$wpdb->query("UPDATE $awsom_pixgallery_gallery_table_name SET galleryname='$updatedgalleryname', galcustomname='$updatedcustomgalname', sortid='$updatedgallerysortid', galtags='$updatedgaltags', galcaptiontext='$updatedgalcaptiontext', galsettings='$updategalsettings', galpath='$updategalpath' WHERE apgid='$updatethisapgid'");
		if ($updatedefaultgalleryindb == 1) {
		update_option("awsom_pixgallery_default_gallery", $updatethisapgid);
		}
		echo "<div class='updated'><p><strong>Gallery updated successfully! </strong></p></div>";
		if ($takemebacktothegallerypage != ""){
				echo "<div class='updated'><p><a href='$takemebacktothegallerypage'>Click Here to Return to the Gallery Page</a></p></div>";
		}
		}
		else{
		echo "<div class='updated'><p><strong>No Gallery name or Path supplied! Gallery data not altered.</strong></p></div>";
		}
		}
?>

<?php
if (isset($_POST['display_all_gallery_db_entries'])){
if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheating uh?'));
PixGallery_ShowAllGalleryEntriesInDB();
} ?>





<div class="wrap">
  <form method="post"><?php if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_add_gallery_options'); } ?>

    <h2>Pixgallery Gallery Add/Edit/Delete Page</h2>

<fieldset class="galleries">
<legend>Click "Add Gallery" to input a new Gallery Profile into the database. You can use HTML in the Gallery Caption.</legend>
<?php $showCreateDefaultFolderLink = get_option("awsom_pixgallery_defaultgallery_created");
if ($showCreateDefaultFolderLink != 1) { ?>
If you would like to switch to, create, and use the AWSOM Pixgallery wp-content/uploads area Gallery folder click the "Create Uploads Gallery" button.<br />
		<input type="submit" name="createdefaultgallery" value="Create Uploads Gallery">
		<?php } else { ?>
			You may use the Awsom Pixgallery Gallery folder created in your uploads folder.
		<?php } ?><br />
<?php PixGallery_ShowDefaultGalleryinadmin(); ?> <br />

<blockquote>
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">(<b>Required</b>) Gallery Name:<br />
			<input type='text' name="newgalleryname" style="width: 150px;"></td></tr>
		<tr>
			<td width="360">Custom Gallery Name For Display:<br />
			<input type='text' name="customnewgalleryname" style="width: 150px;"></td></tr>
		<tr>
		<tr>
					<td width="400">(<b>Required</b>) Path To Gallery (from root folder of your Wordpress website--start with a / and end with a /):<br />
					<input type='text' name="newgallerypath" value='<?php echo $editgalleryfrompagepath; ?>' style="width: 350px;"></td></tr>
		<tr>
					<td width="360">Position in Gallery (if subgallery):<br />
			<input type='text' name="newgallerysortid" style="width: 150px;"></td></tr>
		<tr>
					<td width="360">Use Display Options Settings: (coming soon)<br />
					<select name="newgallerysetting">

				<option value ="1">Global</option>


					</select>

					</td>
		</tr>
		<tr>
			<td width="600">Gallery Tags (Separate by Spaces):<br />
			<input type='text' name="newgallerytags" style="width: 500px;"></td></tr>
			<tr><td><br />
			Gallery Caption: [<b>please use HTML &lt;br&gt; to add multi-line caption text</b>]
			<textarea  name="newgallerycaption" rows="4" cols="80"></textarea></td>
			<input type='hidden' name="takemebacktogallerypage" value='<?php echo $takemebacktogallery; ?>'>
		</tr>
		<tr>
							<td width="360">Set This Gallery as Default?:<br />
							<select name="newgalleryisdefault">

						<option value ="0" Selected>No</option>
						<option value ="1">Yes</option>


							</select>

							</td>
		</tr>
	</table>
	<br />
</blockquote>
</fieldset>
<div class="submit" style="text-align: left;"><input type="submit" name="gallery_update_add" value="Add Gallery" /></div>

</form>
<br />
	<hr width="100%" />
	<br />
<form method="post">
<fieldset class="galleries">
Click "Generate Gallery Code" to have the code automatically generated for you to place in your page/post for the selected Gallery.<br /> Click "Edit Gallery" to edit the selected Gallery. <br />Click "Delete Gallery" to delete the selected Gallery from the database.(You can't delete the default Gallery)
<blockquote>
<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Select Gallery to Edit/Delete/Generate Code:<br />
			<select name="gallerytochange">
			<option value="">Select a Gallery</option>
		<?php PixGallery_ShowAllGalleriesInDBEditor();

?>
			</select>

			</td>
		</tr>
	</table>
</blockquote>
</fieldset>

<div class="submit" style="text-align: left;"><input type="submit" name="gallery_generate_code" value="Generate Gallery Code" /><input type="submit" name="gallery_update_edit" value="Edit Gallery" /> <input type="submit" name="gallery_update_delete" value="Delete Gallery" /><input type="submit" name="display_all_gallery_db_entries" value="Show All Existing Galleries" /></div>

</form>
</div>

<?php
}

function PixGallery_ShowAllGalleryEntriesInDB() {
global $wpdb;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name;
			$galleriesfromdatabase = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_gallery_table_name");
			echo "<div class='updated'>";
			if ($galleriesfromdatabase){
			foreach ($galleriesfromdatabase as $thisgalleriesdata) {
			$thisgalleriesdata->apgid = stripslashes($thisgalleriesdata->apgid);
			$isthisgallerycurrentdefault = get_option('awsom_pixgallery_default_gallery');
			if ( $isthisgallerycurrentdefault == $thisgalleriesdata->apgid){
			$thisgalleryiscurrentdefault = "Yes";
			}
			else{
			$thisgalleryiscurrentdefault = "No";
			}
			$thisgalleriesdata->apgid = stripslashes($thisgalleriesdata->apgid);
			$thisgalleriesdata->galleryname = stripslashes($thisgalleriesdata->galleryname);
			$thisgalleriesdata->galcustomname = stripslashes($thisgalleriesdata->galcustomname);
			$thisgalleriesdata->galpath = stripslashes($thisgalleriesdata->galpath);
			$thisgalleriesdata->sortid = stripslashes($thisgalleriesdata->sortid);
			$thisgalleriesdata->galtags = stripslashes($thisgalleriesdata->galtags);
			$thisgalleriesdata->galcaptiontext = stripslashes($thisgalleriesdata->galcaptiontext);
			$thisgalleriesdata->galsettings = stripslashes($thisgalleriesdata->galsettings);
			// *****Change next line when individual gallery settings are introduced ****** //
			$thisgalleriessettingsname = "global";
			echo "<b>Gallery ID:</b> $thisgalleriesdata->apgid <br /> ";
			echo "<b>Gallery Name:</b> $thisgalleriesdata->galleryname <br /> ";
			echo "<b>Custom Gallery Name:</b> $thisgalleriesdata->galcustomname <br /> ";
			echo "<b>Gallery Path:</b> $thisgalleriesdata->galpath <br /> ";
			echo "<b>Position in Gallery:</b> $thisgalleriesdata->sortid <br /> ";
			echo "<b>Gallery Display Settings:</b> $thisgalleriessettingsname <br /> ";
			echo "<b>Gallery Tags:</b> $thisgalleriesdata->galtags <br /> ";
			echo "<b>Gallery Caption:</b> $thisgalleriesdata->galcaptiontext <br /> ";
			echo "<b>Is This Gallery The Default Gallery?:</b> $thisgalleryiscurrentdefault";
			echo "<form method=\"post\">";
			echo "<input type=\"hidden\" name=\"gallerytochange\" value=\"$thisgalleriesdata->apgid\" />";
			echo "<input type=\"hidden\" name=\"display_all_db_entries_after_edit\" value=\"true\" />";
			echo "<input type=\"submit\" value=\"Edit Gallery Caption\" name=\"gallery_update_edit\"></form>";
			echo "<form method=\"post\">";
			echo "<input type=\"hidden\" name=\"gallerytochange\" value=\"$thisgalleriesdata->apgid\" />";
			echo "<input type=\"hidden\" name=\"display_all_gallery_db_entries\" value=\"true\" />";
			echo "<input type=\"submit\" value=\"Delete Gallery Caption\" name=\"gallery_update_delete\"></form>";
			echo "<hr>";
}
}
			else{
			echo "<p>Error:No Galleries are currently entered in the Database.</p>";
			}
echo "</div>";
}



// ***** end AWSOM Gallery admin area mod *******//

// ***** AWSOM Image Caption admin area mod *****//
 function PixGallery_ShowAllCaptionsInDB() {
global $wpdb;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $PixGallery_UseVisualEditor;
			$imagesfromdatabase = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_caption_table_name");

			foreach ($imagesfromdatabase as $thisimagesdata) {
			$thisimagesdata->name = stripslashes($thisimagesdata->name);
				echo "<option value=\"$thisimagesdata->capid\">$thisimagesdata->name</option>";
}
}
   function PixGallery_ShowAllGalleriesInDB() {
   global $wpdb;
    global $awsom_pixgallery_gallery_table_name;
   			$galleriesfromdatabase = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_gallery_table_name");
			$defaultgalleryselected = get_option('awsom_pixgallery_default_gallery');
   			foreach ($galleriesfromdatabase as $thisgalleriessdata) {
   			$thisgalleriessdata->galleryname = stripslashes($thisgalleriessdata->galleryname);
   			if ($thisgalleriessdata->apgid == $defaultgalleryselected){
   			echo "<option value=\"$thisgalleriessdata->apgid\" Selected>$thisgalleriessdata->galleryname</option>";
   			}
   			else {
   				echo "<option value=\"$thisgalleriessdata->apgid\">$thisgalleriessdata->galleryname</option>";
   			}
   }
}
	function PixGallery_ShowDefaultGalleryinadmin() {
   global $wpdb;
   $awsom_pixgallery_gallery_table_name = $wpdb->prefix . "awsompxggalleries";
   $defaultgalleryToDisplay = get_option('awsom_pixgallery_default_gallery');
   			$gallerytodisplaytoadmin = $wpdb->get_var("SELECT galleryname FROM $awsom_pixgallery_gallery_table_name WHERE apgid = '$defaultgalleryToDisplay'");
			$gallerypathtodisplaytoadmin = $wpdb->get_var("SELECT galpath FROM $awsom_pixgallery_gallery_table_name WHERE apgid = '$defaultgalleryToDisplay'");
			$gallerytodisplaytoadmin = stripslashes($gallerytodisplaytoadmin);
			$gallerypathtodisplaytoadmin = stripslashes($gallerypathtodisplaytoadmin);

   			echo "The Current Default Gallery is: <strong>$gallerytodisplaytoadmin</strong><br />The Default Gallery path is: <strong>$gallerypathtodisplaytoadmin</strong>";
}
	function PixGallery_ShowAllGalleriesInDBEditor() {
	global $wpdb;
   $awsom_pixgallery_gallery_table_name = $wpdb->prefix . "awsompxggalleries";
   			$galleriesfromdatabase = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_gallery_table_name");
			$defaultgalleryselected = get_option('awsom_pixgallery_default_gallery');
   			foreach ($galleriesfromdatabase as $thisgalleriessdata) {
   			$thisgalleriessdata->galleryname = stripslashes($thisgalleriessdata->galleryname);
   			echo "<option value=\"$thisgalleriessdata->apgid\">$thisgalleriessdata->galleryname</option>";

   }
}


function PixGallery_ShowAllImagesCaptionsInDB() {
global $wpdb;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name;
			echo "<div class='updated'>";
			$imagescaptionsfromdatabase = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_caption_table_name");
			if ($imagescaptionsfromdatabase){
			foreach ($imagescaptionsfromdatabase as $thisimagesdata) {
			$thisimagesdata->capid = stripslashes($thisimagesdata->capid);
			$thisimagesdata->viewnum = stripslashes($thisimagesdata->viewnum);
			$thisimagesdata->name = stripslashes($thisimagesdata->name);
			$thisimagesdata->imagepath = stripslashes($thisimagesdata->imagepath);
			$thisimagesdata->customname = stripslashes($thisimagesdata->customname);
			$thisimagesdata->sortid = stripslashes($thisimagesdata->sortid);
			$thisimagesdata->imgtags = stripslashes($thisimagesdata->imgtags);
			$thisimagesdata->captiontext = stripslashes($thisimagesdata->captiontext);
			$thisimagesdata->parentgallery = stripslashes($thisimagesdata->parentgallery);
			$thisimagesparentgallery = $wpdb->get_var("SELECT galleryname FROM $awsom_pixgallery_gallery_table_name WHERE apgid = $thisimagesdata->parentgallery");
			$thisimagesparentgallery = stripslashes($thisimagesparentgallery);
			echo "<b>Image ID:</b> $thisimagesdata->capid <br /> ";
			echo "<b>Image Name:</b> $thisimagesdata->name <br /> ";
			echo "<b>Image Path:</b> $thisimagesdata->imagepath <br /> ";
			echo "<b>Number of Views:</b> $thisimagesdata->viewnum <br />";
			echo "<b>Custom Image Name:</b> $thisimagesdata->customname <br /> ";
			echo "<b>Position Number:</b> $thisimagesdata->sortid <br /> ";
			echo "<b>In Gallery Called:</b> $thisimagesparentgallery <br /> ";
			echo "<b>Image Tags:</b> $thisimagesdata->imgtags <br /> ";
			echo "<b>Image Caption:</b> $thisimagesdata->captiontext ";
			echo "<form method=\"post\">";
			echo "<input type=\"hidden\" name=\"imagetochange\" value=\"$thisimagesdata->capid\" />";
			echo "<input type=\"hidden\" name=\"display_all_db_entries_after_edit\" value=\"true\" />";
			echo "<input type=\"submit\" value=\"Edit Image Caption\" name=\"caption_update_edit\"></form>";
			echo "<form method=\"post\">";
			echo "<input type=\"hidden\" name=\"imagetochange\" value=\"$thisimagesdata->capid\" />";
			echo "<input type=\"hidden\" name=\"display_all_db_entries\" value=\"true\" />";
			echo "<input type=\"submit\" value=\"Delete Image Caption\" name=\"caption_update_delete\"></form>";
			echo "<hr>";
}
}
			else{
			echo "<p>No Captions are currently entered in the Database.</p>";
			}
echo "</div>";
}




function PixGallery_Manage_Captions()
{
global $wpdb, $PixGallery_RootCache,$PixGlobal_Path, $PixGlobal_AdminPath;
global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $PixGallery_UseVisualEditor;


	if ($PixGallery_UseVisualEditor == 1){  ?>
<script type="text/javascript" src="<?php echo $PixGlobal_AdminPath; ?>support_files/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		// General options
		theme : "advanced",
		plugins : "safari,pagebreak,table,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras",
		mode : "textareas",
        elements : "newimagecaption, changedimagecaption",
        relative_urls : false,
        remove_script_host : false,
        width : "565",
	height : "250",
	skin : "wp_theme",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});
</script>
<!-- /TinyMCE -->
<?php }


	if (isset($_POST['admin_edit_link_on_page'])){
	/* get new image name input from edit link on single image view page */
	$editcaptionfrompage = $_POST['editlinkimagename'];
	$editcaptionfrompage = trim($editcaptionfrompage);
	$editcaptionfrompage = $wpdb->escape($editcaptionfrompage);
	$editcaptionfrompagepath = $_POST['editlinkimagepath'];
	$editcaptionfrompagepath = trim($editcaptionfrompagepath);
	$editcaptionfrompagepath = $wpdb->escape($editcaptionfrompagepath);
	$takemebacktoimage = $_POST['takemeback'];
	$takemebacktoimage = trim($takemebacktoimage);
	$takemebacktoimage = $wpdb->escape($takemebacktoimage);

	}

	if (isset($_POST['delete_cache'])){
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     	 die(__('Cheating uh?'));
		check_admin_referer('apg_clear_cache_folder');
		$dir = ABSPATH.$PixGallery_RootCache;
		$deletecacheworked = Pixgallery_CleanupCacheFolder($dir, false);
		//echo "<div class='updated'><p><strong> var is $dir</strong></p></div>";
		echo "<div class='updated'><p><strong>Cache Folder Reset successfully!</strong></p></div>";
		//}
		//else{
		//echo "<div class='updated'><p><strong>Warning, Could Not Reset Cache Folder! Check Cache Folder path and Permissions.</strong></p></div>";
		//}
		}

if (isset($_POST['add_all_image_text_value'])){
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     	 die(__('Cheating uh?'));
		check_admin_referer('apg_add_to_all_images_text');
		$textforallimagestoadd = $_POST['text_for_all_images'];
		$textforallimagestoadd = trim($textforallimagestoadd);
		
		
		update_option( 'awsom_pixgallery_all_images_text', $textforallimagestoadd );
		echo "<div class='updated'><p><strong>'Universal Image Text' Added To The Database</strong></p></div>";
		//}
		//else{
		//echo "<div class='updated'><p><strong>Warning, Could Not Reset Cache Folder! Check Cache Folder path and Permissions.</strong></p></div>";
		//}
		}




	if (isset($_POST['caption_update_add']))
		{
		/* add caption for new image to Database */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
     	 die(__('Cheating uh?'));
     	 check_admin_referer('apg_add_image_caption_options');
     	$takemebacktoimagepage = $_POST['takemebacktoimagepage'];
		$newimagenamefordb = $_POST['newimagename'];
		$newimagenamefordb = trim($newimagenamefordb);
		$newimagepathfordb = $_POST['newimagepath'];
		$newimagepathfordb = trim($newimagepathfordb);
		$newimagecustomnamefordb = $_POST['customnewimagename'];
		$newimagecustomnamefordb = trim($newimagecustomnamefordb);
		$newimagesortfordb = $_POST['newimagesortid'];
		$newimagesortfordb = trim($newimagesortfordb);
		$newimagetagsfordb = $_POST['newimagetags'];
		$newimagecaptionfordb = $_POST['newimagecaption'];
		$newgalleryparentfordb = $_POST['newgallerymember'];
		$newimagenamefordb = $wpdb->escape($newimagenamefordb);
		$newimagepathfordb = $wpdb->escape($newimagepathfordb);
		$newimagecustomnamefordb = $wpdb->escape($newimagecustomnamefordb);
		$newimagesortfordb = $wpdb->escape($newimagesortfordb);
		$newimagetagsfordb = $wpdb->escape($newimagetagsfordb);
		$newimagecaptionfordb = $wpdb->escape($newimagecaptionfordb);
		$newgalleryparentfordb = $wpdb->escape($newgalleryparentfordb);
		$takemebacktoimagepage = $wpdb->escape($takemebacktoimagepage);
		if ($newimagenamefordb !== ""){
		$wpdb->query("
		INSERT INTO $awsom_pixgallery_caption_table_name (capid, name, imagepath, captiontext, customname,  sortid, imgtags, parentgallery )
		VALUES (null, '$newimagenamefordb', '$newimagepathfordb', '$newimagecaptionfordb', '$newimagecustomnamefordb', '$newimagesortfordb', '$newimagetagsfordb', '$newgalleryparentfordb')");

		echo "<div class='updated'><p><strong>Caption added successfully!</strong></p></div>";
		if ($takemebacktoimagepage !=""){
		echo "<div class='updated'><p><a href='$takemebacktoimagepage'>Click Here to Return to the Image Page</a></p></div>";
		}
		}
		else{
		echo "<div class='updated'><p><strong>No Image name supplied!</strong></p></div>";
		}
		}

	if (isset($_POST['caption_update_delete']))
		{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheating uh?'));
		if ($_POST['imagetochange'] !==""){
		/* delete caption and image from Database */


		$imagetobedeleted = $_POST['imagetochange'];
		$imagetobedeleted = $wpdb->escape($imagetobedeleted);
		$wpdb->query("
		DELETE FROM $awsom_pixgallery_caption_table_name WHERE capid='$imagetobedeleted'");

		echo "<div class='updated'><p><strong>Image deleted successfully!</strong></p></div>";
		}
		else{
		echo "<div class='updated'><p><strong>No Image selected to delete! Please select an image from the dropdown list.</strong></p></div>";
		}
		}

		if (isset($_POST['caption_update_edit']))
				{
				if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheating uh?'));

				if ($_POST['imagetochange'] != ""){
				/* edit caption and image from Database */
				$reloadallcaptions = $_POST['display_all_db_entries_after_edit'];
				$reloadallcaptions = $wpdb->escape($reloadallcaptions);
				$takemebacktoimage = $_POST['takemeback'];
				$takemebacktoimage = trim($takemebacktoimage);
				$takemebacktoimage = $wpdb->escape($takemebacktoimage);
				$editpathfrompage = $_POST['editlinkimagepathupdate'];
				$editpathfrompage = trim($editpathfrompage);
				$editpathfrompage = $wpdb->escape($editpathfrompage);
				$imagetobeedited = $_POST['imagetochange'];
				$imagetobeedited = $wpdb->escape($imagetobeedited);
				$captiontexttobeedited = $wpdb->get_results("SELECT name, captiontext, imagepath, customname, sortid, imgtags, parentgallery, viewnum FROM $awsom_pixgallery_caption_table_name WHERE capid='$imagetobeedited'");
				foreach ($captiontexttobeedited as $thiscaption){
				$thiscaption->name = stripslashes($thiscaption->name);
				$thiscaption->viewnum = stripslashes($thiscaption->viewnum);
				$thiscaption->imagepath = stripslashes($thiscaption->imagepath);
				$thiscaption->customname = stripslashes($thiscaption->customname);
				$thiscaption->sortid = stripslashes($thiscaption->sortid);
				$thiscaption->parentgallery = stripslashes($thiscaption->parentgallery);
				$thiscaption->imgtags = stripslashes($thiscaption->imgtags);
				$thiscaption->captiontext = stripslashes($thiscaption->captiontext);
				$updatethisname = $thiscaption->name;
				$updatethisviewnum = $thiscaption->viewnum;
				$updatethisimagepath = $thiscaption->imagepath;
				if ($updatethisimagepath ==""){
				$updatethisimagepath = $editpathfrompage;
				}
				$updatethiscustomname = $thiscaption->customname;
				$updatethisimagesortid = $thiscaption->sortid;
				$updatethisimagetags = $thiscaption->imgtags;
				$updatethiscaption = $thiscaption->captiontext;
				$updatethisparentgallery = $thiscaption->parentgallery;

				}
				$awsom_pixgallery_gallery_table_name = $wpdb->prefix . "awsompxggalleries";
				$galleriesfromdatabasetoedit = $wpdb->get_results("SELECT * FROM $awsom_pixgallery_gallery_table_name");
								$markgalleryselected = "";
					   			foreach ($galleriesfromdatabasetoedit as $thisgalleriessdata) {
					   			$thisgalleriessdata->galleryname = stripslashes($thisgalleriessdata->galleryname);
					   			if ($thisgalleriessdata->apgid == $updatethisparentgallery) {
					   			$markgalleryselected = "Selected";
					   			}
					   				$showthegalleryresults .= "<option value=\"$thisgalleriessdata->apgid\" $markgalleryselected>$thisgalleriessdata->galleryname</option>";
									$markgalleryselected = "";
			}




			echo "<div class=\"updated\"><form method=\"post\">";
			 if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_edit_image_caption_options'); }

			echo "<fieldset class=\"captions\">
			<legend>Click \"Update Caption\" to update this image Name and Caption.</legend>
			<blockquote>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td width=\"360\">(<b>Required</b>) Image Name:<br />
						<input type='text' value=\"$updatethisname\" name=\"changedimagename\" style=\"width: 150px;\"></td></tr>
					<tr>
											<td width=\"300\">(<b>Required</b>) Image Path:<br />
						<input type='text' value=\"$updatethisimagepath\" name=\"changedimagepath\" style=\"width: 600px;\"></td></tr>
					<tr>
						<td width=\"360\">Custom Image Name For Display:<br />
						<input type='text' value=\"$updatethiscustomname\" name=\"changedcustomimagename\" style=\"width: 150px;\"></td></tr>
					<tr>
						<td width=\"360\">Position in Gallery:<br />
						<input type='text' value=\"$updatethisimagesortid\" name=\"changedcustomimagesortid\" style=\"width: 150px;\"></td></tr>
					<tr>
					<tr>
						<td width=\"360\">Image View Count:<br />
						<input type='text' value=\"$updatethisviewnum\" name=\"changedviewnum\" style=\"width: 150px;\"></td></tr>
					<tr>
					<td width=\"360\">Part of Gallery:<br />
					<select name=\"changedgallerymember\">

					$showthegalleryresults
					</select>

					</td>
					</tr>
					<tr>
						<td width=\"600\">Image Tags (Separate by Spaces):<br />
						<input type='text' value=\"$updatethisimagetags\" name=\"changedimagetags\" style=\"width: 500px;\"></td></tr>
						<tr><td><br />
						Image Caption: [<b>please use HTML &lt;br&gt; to add multi-line caption text</b>]
						<textarea  name=\"changedimagecaption\" rows=\"4\" cols=\"80\">$updatethiscaption</textarea></td>
						<input type='hidden' name=\"updatecapid\" value=\"$imagetobeedited\">
						<input type='hidden' name=\"takemebacktoimagepage\" value=\"$takemebacktoimage\">";
						if ($reloadallcaptions != ""){
							echo "<input type='hidden' name=\"display_all_db_entries\" value=\"$reloadallcaptions\">";
						}
						echo "</tr>
				</table>
				<br />
			</blockquote>
			</fieldset>
			<div class=\"submit\" style=\"text-align: left;\"><input type=\"submit\" name=\"caption_update_now\" value=\"Update Caption\" /></div>

</form></div>";

		}
		else{
		echo "<div class='updated'><p><strong>No Image selected to edit! Please select an image from the dropdown list.</strong></p></div>";
		}
		}
	if (isset($_POST['caption_update_now']))
		{
		/* update image name and caption in Database */
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheating uh?'));
      	check_admin_referer('apg_edit_image_caption_options');
		$updatedimagename = $_POST['changedimagename'];
		$updatedimagename = trim($updatedimagename);
		$updatedimagepath = $_POST['changedimagepath'];
		$updatedimagepath = trim($updatedimagepath);
		$updatedcustomimagename = $_POST['changedcustomimagename'];
		$updatedcustomimagename = trim($updatedcustomimagename);
		$updatedimagesortid = $_POST['changedcustomimagesortid'];
		$updatedimagesortid = trim($updatedimagesortid);
		$updatedimagesviewnum = $_POST['changedviewnum'];
		$updatedimagesviewnum = trim($updatedimagesviewnum);
		$updatedimagetags = $_POST['changedimagetags'];
		$updatedcaptiontext = $_POST['changedimagecaption'];
		$updatethiscapid = $_POST['updatecapid'];
		$takemebacktotheimagepage = $_POST['takemebacktoimagepage'];
		$updateparentgallery = $_POST['changedgallerymember'];
		$updatedimagename = $wpdb->escape($updatedimagename);
		$updatedimagepath = $wpdb->escape($updatedimagepath);
		$updatedcustomimagename = $wpdb->escape($updatedcustomimagename);
		$updatedimagesortid = $wpdb->escape($updatedimagesortid);
		$updatedimagesviewnum = $wpdb->escape($updatedimagesviewnum);
		$updatedimagetags = $wpdb->escape($updatedimagetags);
		$updatedcaptiontext = $wpdb->escape($updatedcaptiontext);
		$updateparentgallery = $wpdb->escape($updateparentgallery);
		$takemebacktotheimagepage = $wpdb->escape($takemebacktotheimagepage);
		if ($updatedimagename != ""){
		$wpdb->query("UPDATE $awsom_pixgallery_caption_table_name SET name='$updatedimagename', imagepath = '$updatedimagepath', customname='$updatedcustomimagename', sortid='$updatedimagesortid', viewnum ='$updatedimagesviewnum', imgtags='$updatedimagetags', captiontext='$updatedcaptiontext', parentgallery='$updateparentgallery' WHERE capid='$updatethiscapid'");

		echo "<div class='updated'><p><strong>Image Caption updated successfully!</strong></p></div>";
		if ($takemebacktotheimagepage != ""){
		echo "<div class='updated'><p><a href='$takemebacktotheimagepage'>Click Here to Return to the Image Page</a></p></div>";
		}
		}
		else{
		echo "<div class='updated'><p><strong>No Image name supplied! Caption data not altered.</strong></p></div>";
		}
		}
?>

<?php
if (isset($_POST['display_all_db_entries'])){
if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheating uh?'));
PixGallery_ShowAllImagesCaptionsInDB();
} ?>

<div class="wrap">






  <form method="post"><?php if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_add_image_caption_options'); } ?>

    <h2>Pixgallery Caption Add/Edit/Delete Page</h2>

<fieldset class="captions">
<legend>Click "Add Caption" to input a new Caption into the database.You can use HTML in the caption.</legend>
<strong>MAJOR CHANGE</strong>: When inputting the Image Name make sure you use it's file extension (.gif, .png, .jpg, etc.).<br /> Also make sure that you type in the same upper/lowecase
as the original file (so Example.gif is different from example.gif).<br />Optional: Use Custon Image Name to input a name you want to display to visitors.
<blockquote>
	<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">(<b>Required</b>) Image Name (*with* file extension):<br />
			<input type='text' value='<?php echo $editcaptionfrompage; ?>' name="newimagename" style="width: 150px;"></td></tr>
		<tr>
					<td width="360">(<b>Required</b>) Image Path (including file name):<br />
			<input type='text' value='<?php echo $editcaptionfrompagepath; ?>' name="newimagepath" style="width: 550px;"></td></tr>
		<tr>
			<td width="360">Custom Image Name For Display:<br />
			<input type='text'  name="customnewimagename" style="width: 150px;"></td></tr>
		<tr>
			<td width="360">Position in Gallery:<br />
			<input type='text'  name="newimagesortid" style="width: 150px;"></td></tr>
		<tr>
					<td width="360">Part of Gallery:<br />
					<select name="newgallerymember">

				<?php PixGallery_ShowAllGalleriesInDB();

		?>
					</select>

					</td>
		</tr>
		<tr>
			<td width="600">Image Tags (Separate by Spaces):<br />
			<input type='text' name="newimagetags" style="width: 500px;"></td></tr>
			<tr><td><br />
			Image Caption: [<b>please use HTML &lt;br&gt; to add multi-line caption text</b>]
			<textarea  name="newimagecaption" rows="4" cols="80"></textarea></td>
			<input type='hidden' name="takemebacktoimagepage" value='<?php echo $takemebacktoimage; ?>'>
		</tr>
	</table>
	<br />
</blockquote>
</fieldset>
<div class="submit" style="text-align: left;"><input type="submit" name="caption_update_add" value="Add Caption" /></div>

</form>
<br />
	<hr width="100%" />
	<br />
<form method="post">
<fieldset class="captions">
<legend>Click "Edit Caption" to edit the selected image's caption. Click "Delete Caption" to delete the selected image from the database.</legend>
<blockquote>
<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td width="360">Select Image to Edit/Delete:<br />
			<select name="imagetochange">
			<option value="">Select an Image</option>
		<?php PixGallery_ShowAllCaptionsInDB();

?>
			</select>

			</td>
		</tr>
	</table>
</blockquote>
</fieldset>

<div class="submit" style="text-align: left;"><input type="submit" name="caption_update_edit" value="Edit Caption" /> <input type="submit" name="caption_update_delete" value="Delete Caption" /><input type="submit" name="display_all_db_entries" value="Show All Existing Captions" /></div>

</form>

<form method="post"><?php if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_add_to_all_images_text'); } ?>
<h2>Universal Image Text</h2>If you want to add something to all of your images (copyright statement, etc.) you can fill that in the following text box and it will automatically appear under all of your images when viewing them individually.<br />
<textarea name="text_for_all_images" rows="4" cols="80"><?php $presentuniversaltext = get_option( 'awsom_pixgallery_all_images_text'); $presentuniversaltext = stripslashes($presentuniversaltext); echo $presentuniversaltext; ?></textarea>
<div class="submit" style="text-align: left;"><input type="submit" name="add_all_image_text_value" value="Submit Image Text" /></div></form>
<form method="post"><?php if (function_exists('wp_nonce_field')) { wp_nonce_field('apg_clear_cache_folder'); } ?>
<fieldset class="captions">

<br /><br />To Reset the Cache Folder and Delete all of your Thumbnails and Watermarked images (and have Pixgallery recreate them) click "Delete All Cache Files":<br /><div class="submit" style="text-align: left;"><input type="submit" name="delete_cache" value="Delete All Cache Files" /></div>
</form>
</div>

<?php
}
//***** End Caption admin area mod *****//




function PixGallery_Options_Hook()
{
    if (function_exists('add_options_page'))
		add_options_page('PixGallery Settings', 'PixGallery', 8, basename(__FILE__), 'PixGallery_Options_Panel');

	if (function_exists('add_submenu_page'))
			add_submenu_page('themes.php','PixGallery Manage Galleries', 'PixGallery Galleries', 8, basename(__FILE__), 'PixGallery_Manage_Galleries');
if (function_exists('add_submenu_page'))
			add_submenu_page('post.php','PixGallery Manage Image Captions', 'PixGallery Image Captions', 8, basename(__FILE__), 'PixGallery_Manage_Captions');
}


/* ------------------------------------------------------------------------------ */
/* Plugin stuff..
/* ------------------------------------------------------------------------------ */
//***** AWSOM Image Caption function to create db table *****//
function PixGallery_CaptionTableInstall() {
   global $wpdb, $awsomcreatecachedir, $awsomcreatedefaultgallerydir, $awsomcreatecustomizationdir, $awsom_pixgallery_db_version;
   global $awsom_pixgallery_caption_table_name, $awsom_pixgallery_gallery_table_name, $awsom_pixgallery_comment_tracking_table_name;

	if (!file_exists($awsomcreatecustomizationdir)) {
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	wp_mkdir_p($awsomcreatecustomizationdir);
	}

   if($wpdb->get_var("show tables like '$awsom_pixgallery_caption_table_name'") != $awsom_pixgallery_caption_table_name) {

      $sql = "CREATE TABLE ".$awsom_pixgallery_caption_table_name." (
	      capid mediumint(8) NOT NULL AUTO_INCREMENT,
	      name varchar(255)  NOT NULL,
	      customname varchar(255)  NOT NULL,
	      imagepath varchar(255) NOT NULL,
	      imgtags  longtext  NOT NULL,
	      parentgallery mediumint(8) NOT NULL,
	      sortid mediumint(8) NOT NULL,
	      viewnum mediumint(9) NOT NULL,
	      allowcomments tinyint(2) NOT NULL,
              captiontext longtext NOT NULL,
              PRIMARY KEY  (capid)
	     );";

      require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      dbDelta($sql);
      update_option("awsom_pixgallery_db_version", 2);

      if (wp_mkdir_p($awsomcreatecachedir)){
      update_option ("awsom_pixgallery_cache_created", 1);

      	}
      	if (wp_mkdir_p($awsomcreatedefaultgallerydir)){
			        update_option ("awsom_pixgallery_defaultgallery_created", 1);
      	}

      }



	if($wpdb->get_var("show tables like '$awsom_pixgallery_gallery_table_name'") != $awsom_pixgallery_gallery_table_name) {
     $sql = "CREATE TABLE ".$awsom_pixgallery_gallery_table_name." (
	 	      apgid mediumint(8) NOT NULL AUTO_INCREMENT,
	 	      galleryname varchar(255)  NOT NULL,
	 	      galcustomname varchar(255) NOT NULL,
	 	      galpath varchar(255) NOT NULL,
	 	      galtags  longtext  NOT NULL,
	 	      sortid mediumint(8) NOT NULL,
	 	      allowcomments tinyint(2) NOT NULL,
	 	      galsettings mediumint(8) NOT NULL,
	               galcaptiontext longtext NOT NULL,
	               PRIMARY KEY  (apgid)
	     );";
	  require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      dbDelta($sql);
      $initialgalleryname = "Default";
      $initialgalsettings = 1;
      $initialgalpath = "/wp-content/uploads/awsompixgallery/";
      $wpdb->query("INSERT INTO $awsom_pixgallery_gallery_table_name (apgid, galleryname, galsettings, galpath) VALUE (NULL, '$initialgalleryname', '$initialgalsettings', '$initialgalpath')");

	  update_option("awsom_pixgallery_default_gallery", 1);
	  }



	  if($wpdb->get_var("show tables like '$awsom_pixgallery_comment_tracking_table_name'") != $awsom_pixgallery_comment_tracking_table_name) {
		$sql = "CREATE TABLE ".$awsom_pixgallery_comment_tracking_table_name." (
			 	      apgctid mediumint(9) NOT NULL AUTO_INCREMENT,
			 	      commentid bigint(20)  NOT NULL,
			 	      itempath varchar(255) NOT NULL,
			 	      postid bigint(20) NOT NULL,
			 	      PRIMARY KEY  (apgctid)
	     );";
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
     	dbDelta($sql);
		}
		$pixgallerydb_installed_ver = get_option( "awsom_pixgallery_db_version" );

		   if( $pixgallerydb_installed_ver == "1" ) {
		   $sql = "CREATE TABLE ".$awsom_pixgallery_caption_table_name." (
		   	      capid mediumint(8) NOT NULL AUTO_INCREMENT,
		   	      name varchar(255)  NOT NULL,
		   	      customname varchar(255)  NOT NULL,
				  	      imagepath varchar(255) NOT NULL,
				  	      imgtags  longtext  NOT NULL,
			      parentgallery mediumint(8) NOT NULL,
			      sortid mediumint(8) NOT NULL,
			      viewnum mediumint(9) NOT NULL,
			      allowcomments tinyint(2) NOT NULL,
		                 captiontext longtext NOT NULL,
		                 PRIMARY KEY  (capid)
		   	     );";

		         require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		      dbDelta($sql);
			$sql = "CREATE TABLE ".$awsom_pixgallery_gallery_table_name." (
			 	      apgid mediumint(8) NOT NULL AUTO_INCREMENT,
			 	      galleryname varchar(255)  NOT NULL,
			 	      galcustomname varchar(255) NOT NULL,
			 	      galpath varchar(255) NOT NULL,
			 	      galtags  longtext  NOT NULL,
			 	      allowcomments tinyint(2) NOT NULL,
			 	      galsettings mediumint(8) NOT NULL,
			 	      sortid mediumint(8) NOT NULL,
			               galcaptiontext longtext NOT NULL,
			               PRIMARY KEY  (apgid)
			     );";
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		      dbDelta($sql);
		update_option("awsom_pixgallery_db_version", 2);

     	 }
     	 if( $pixgallerydb_installed_ver == "" ) {
		 		   $sql = "CREATE TABLE ".$awsom_pixgallery_caption_table_name." (
		 		   	      capid mediumint(8) NOT NULL AUTO_INCREMENT,
		 		   	      name varchar(255)  NOT NULL,
		 		   	      customname varchar(255)  NOT NULL,
		 				  	      imagepath varchar(255) NOT NULL,
		 				  	      imgtags  longtext  NOT NULL,
		 			      parentgallery mediumint(8) NOT NULL,
		 			      sortid mediumint(8) NOT NULL,
		 			      viewnum mediumint(9) NOT NULL,
		 			      allowcomments tinyint(2) NOT NULL,
		 		                 captiontext longtext NOT NULL,
		 		                 PRIMARY KEY  (capid)
		 		   	     );";

		 		         require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		 		      dbDelta($sql);
		 		    $awsom_image_parentgallery_default = "1";

      $wpdb->query("UPDATE $awsom_pixgallery_caption_table_name  SET parentgallery = $awsom_image_parentgallery_default");
	  $wpdb->query("UPDATE $awsom_pixgallery_caption_table_name  SET $awsom_pixgallery_caption_table_name.customname = $awsom_pixgallery_caption_table_name.name");
	  update_option("awsom_pixgallery_cache_created", 0);

		 			$sql = "CREATE TABLE ".$awsom_pixgallery_gallery_table_name." (
		 			 	      apgid mediumint(8) NOT NULL AUTO_INCREMENT,
		 			 	      galleryname varchar(255)  NOT NULL,
		 			 	      galcustomname varchar(255) NOT NULL,
		 			 	      galpath varchar(255) NOT NULL,
		 			 	      galtags  longtext  NOT NULL,
		 			 	      allowcomments tinyint(2) NOT NULL,
		 			 	      galsettings mediumint(8) NOT NULL,
		 			 	      sortid mediumint(8) NOT NULL,
		 			               galcaptiontext longtext NOT NULL,
		 			               PRIMARY KEY  (apgid)
		 			     );";
		 				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		 		      dbDelta($sql);
	  $initialgalleryname = "Default";
      $initialgalsettings = 1;
      $initialgalpath = "/wp-content/uploads/awsompixgallery/";
      $wpdb->query("INSERT INTO $awsom_pixgallery_gallery_table_name (apgid, galleryname, galsettings, galpath) VALUE (NULL, '$initialgalleryname', '$initialgalsettings', '$initialgalpath')");
		 		update_option("awsom_pixgallery_db_version", 2);
		 }
	}
// ***** End create table *****//
function PixGallery_Footer_Credit()
{
?>
<div id="awsomfootercredit"><!--- AWSOM Pixgallery Footer Credit Block -->
 <a href="http://www.awsom.org" title="AWSOM Plugin Powered">AWSOM Powered</a>
</div><!--- end AWSOM Pixgallery Footer Credit Block -->
<?php
}

function PixGallery_Header()
{
	global $PixGlobal_Path, $PixGallery_CustomPopup;

?>
<?php $Pixgallery_Custom_CSS_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery.css';
	if (file_exists($Pixgallery_Custom_CSS_Location)){ ?>

<link rel="stylesheet" href="<?php echo WP_CONTENT_URL; ?>/uploads/awsom-customizations/pixgallery.css" type="text/css" />
	<?php } 
	else { ?>
<link rel="stylesheet" href="<?php echo $PixGlobal_Path; ?>pixgallery.css" type="text/css" />
	<?php } ?>
<?php if ($PixGallery_CustomPopup == "0") { ?>
<script language='javascript' type='text/javascript' src='<?php echo $PixGlobal_Path; ?>pixgallery.js.php'></script>
<?php }

}

function PixGallery_ShareThis_Integration()
{
global $wp_query;
if(is_single() || is_page()) {
	$thisentriescontent = $wp_query->post->post_content;
	$thisentriescontent = strtolower($thisentriescontent);
	$isthereagallery = strpos($thisentriescontent, '[/pixgallery]');
	$isthereagallerylegacy = strpos($thisentriescontent, '</pixgallery>');
		if ($isthereagallerylegacy !== false | $isthereagallery !== false) {
			if (function_exists('st_request_handler')){
			remove_action('the_content', 'st_add_link');
			remove_action('the_content', 'st_add_widget');
			}
		}
	}
}

function PixGallery_EditTitle($pxgtitleaddition) {
global $wp_query,$wp_pxg_url,$PixGallery_RootCache,$PixGallery_SEOTitlePlace,$PixGallery_SEOTitleContent,$awsom_pixgallery_caption_table_name,$awsom_pixgallery_gallery_table_name,$wpdb;

	if(is_single() || is_page()) {
	$thisentriescontent = $wp_query->post->post_content;
	$thisentriescontent = strtolower($thisentriescontent);
	$isthereagallery = strpos($thisentriescontent, '[/pixgallery]');
	$isthereagallerylegacy = strpos($thisentriescontent, '</pixgallery>');
		if ($isthereagallerylegacy !== false) {
	
	$Pixgallery_Custom_Language_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if (file_exists($Pixgallery_Custom_Language_Location)){
	require WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if ($PixGallery_Lang_File_Version < 2) {
	PixGallery_PrintError("Error: Pixgallery has new language elements, please copy and update the newest pixgallery_lang.php file to your awsom_customizations folder to display all text properly in your galleries.");
		}
	}
	else {
	require WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/pixgallery_lang.php';}
	
		$StartIndex		= 0;
		$TagName		= "pixgallery";
	
		$TagBegin		= strpos($thisentriescontent, "<$TagName");
		$TagBeginEnd	= strpos($thisentriescontent, ">", $TagBegin);
		$TagEnd			= strpos($thisentriescontent, "</$TagName>");

		if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
			{
			$TagBeginEnd	+= 1;
			$TagContents	= substr($thisentriescontent, $TagBegin, $TagBeginEnd - $TagBegin);
			$TopGalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));
			}
			$whatIsGet = $_SERVER['QUERY_STRING'];
			$whatIsGet = htmlspecialchars($whatIsGet);
			$whatIsGet = str_replace("%2F", "/", $whatIsGet);
			$whatIsGet = str_replace("&amp;", "&", $whatIsGet);
			$pxgdumppagination = strpos($whatIsGet, "pxs=");
			
			
			If ($pxgdumppagination !== false){
			$shredpagination = $pxgdumppagination - 1;
			$whatIsGet = substr($whatIsGet,0,$shredpagination);
				}
			
			
			
			$pos = strpos($whatIsGet, "px=/");
			If ($pos === false){
			$pxgpathtoobject = $TopGalleryPath;
			$pxgistoplevel = 1;
			}
			else {
			$pxgpathtoobject = $TopGalleryPath.substr($whatIsGet,($pos + 3));
			}
			$pos = $pos + 3;
			//$pos = strpos($whatIsGet, "/");
			$pos2 = strrpos($whatIsGet, "/") + 1;
			$length = $pos2 - $pos;
			
			$pxgpathtoobject = str_replace("+", " ", $pxgpathtoobject);
			$pxgpathtoobject = str_replace("//", "/", $pxgpathtoobject);
			//$pxgtagsjustname = substr($whatIsGet,$pos2);
			//$pxgtagsfullpath = str_replace("/", " > ", $pxgtags);
			
			
			
			$pxgwheretolookfortitle = substr($pxgpathtoobject, -1);
				if ($pxgwheretolookfortitle == "/"){
				$usegallerytype = 1;
				$pxgtitlecontent = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$pxgpathtoobject'");
					if ($pxgtitlecontent) {
				$pxgtitlecontent = stripslashes($pxgtitlecontent);
					}
					else {
						if ($pxgistoplevel == 1) {
						$pxgtitlecontent = str_replace("/", " ",$pxgpathtoobject);
						}
						else {
						$pxglastgallerystart = strrpos($pxgpathtoobject, "/", -2);
						$pxgtitlecontent = substr($pxgpathtoobject,$pxglastgallerystart);
						}
					}
				}
				else{
				$usegallerytype = 0;
				$pxgtitlecontent = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$pxgpathtoobject'");
					if ($pxgtitlecontent) {
				$pxgtitlecontent = stripslashes($pxgtitlecontent);
						}
					else {
					$pxggetimagename = strrpos($pxgpathtoobject, "/") + 1;
					$pxgtitlecontent = substr($pxgpathtoobject,$pxggetimagename);
					}
				}
						
		}
		
	if ($isthereagallery !== false) {
	
	$Pixgallery_Custom_Language_Location = WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if (file_exists($Pixgallery_Custom_Language_Location)){
	require WP_CONTENT_DIR.'/uploads/awsom-customizations/pixgallery_lang.php';
	if ($PixGallery_Lang_File_Version < 2) {
	PixGallery_PrintError("Error: Pixgallery has new language elements, please copy and update the newest pixgallery_lang.php file to your awsom_customizations folder to display all text properly in your galleries.");
		}
	}
	else {
	require WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/pixgallery_lang.php';}
	
		$StartIndex		= 0;
		$TagName		= "pixgallery";
	
		$TagBegin		= strpos($thisentriescontent, "[$TagName");
		$TagBeginEnd	= strpos($thisentriescontent, "]", $TagBegin);
		$TagEnd			= strpos($thisentriescontent, "[/$TagName]");

		if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
			{
			$TagBeginEnd	+= 1;
			$TagContents	= substr($thisentriescontent, $TagBegin, $TagBeginEnd - $TagBegin);
			$TopGalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));
			}
			$whatIsGet = $_SERVER['QUERY_STRING'];
			$whatIsGet = htmlspecialchars($whatIsGet);
			$whatIsGet = str_replace("%2F", "/", $whatIsGet);
			$whatIsGet = str_replace("&amp;", "&", $whatIsGet);
			$pxgdumppagination = strpos($whatIsGet, "pxs=");
			
			
			If ($pxgdumppagination !== false){
			$shredpagination = $pxgdumppagination - 1;
			$whatIsGet = substr($whatIsGet,0,$shredpagination);
				}
			
			
			
			$pos = strpos($whatIsGet, "px=/");
			If ($pos === false){
			$pxgpathtoobject = $TopGalleryPath;
			$pxgistoplevel = 1;
			}
			else {
			$pxgpathtoobject = $TopGalleryPath.substr($whatIsGet,($pos + 3));
			}
			$pos = $pos + 3;
			//$pos = strpos($whatIsGet, "/");
			$pos2 = strrpos($whatIsGet, "/") + 1;
			$length = $pos2 - $pos;
			
			$pxgpathtoobject = str_replace("+", " ", $pxgpathtoobject);
			$pxgpathtoobject = str_replace("//", "/", $pxgpathtoobject);
			//$pxgtagsjustname = substr($whatIsGet,$pos2);
			//$pxgtagsfullpath = str_replace("/", " > ", $pxgtags);
			
			
			
			$pxgwheretolookfortitle = substr($pxgpathtoobject, -1);
				if ($pxgwheretolookfortitle == "/"){
				$usegallerytype = 1;
				$pxgtitlecontent = $wpdb->get_var("SELECT galcustomname FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$pxgpathtoobject'");
					if ($pxgtitlecontent) {
				$pxgtitlecontent = stripslashes($pxgtitlecontent);
					}
					else {
						if ($pxgistoplevel == 1) {
						$pxgtitlecontent = str_replace("/", " ",$pxgpathtoobject);
						}
						else {
						$pxglastgallerystart = strrpos($pxgpathtoobject, "/", -2);
						$pxgtitlecontent = substr($pxgpathtoobject,$pxglastgallerystart);
						}
					}
				}
				else{
				$usegallerytype = 0;
				$pxgtitlecontent = $wpdb->get_var("SELECT customname FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$pxgpathtoobject'");
					if ($pxgtitlecontent) {
				$pxgtitlecontent = stripslashes($pxgtitlecontent);
						}
					else {
					$pxggetimagename = strrpos($pxgpathtoobject, "/") + 1;
					$pxgtitlecontent = substr($pxgpathtoobject,$pxggetimagename);
					}
				}
						
		}
	//Debug Section	
	//echo $TopGalleryPath;
	//echo "<br>";
	//echo "( pxgtitlecontent = ".$pxgtitlecontent." )";
	//echo "( pxgpathtoobject = ".$pxgpathtoobject." )";
	//echo "( whatIsGet = ".$whatIsGet." )";
	//echo "( pxgdumppagination = ".$pxgdumppagination." )";
	//echo "pos is ".$pos;
	//echo "<br>";
	//echo "rest is".$rest;
	//echo "<br>";
	//echo "what is get".$whatIsGet;
	//echo "<br>";
	//echo "pxgtags is".$pxgtags;
	//echo "feed link =".$pxgheaddisplayfeedlink;
	//echo "<br>";
	//$Whatisthepage = $_SERVER['REQUEST_URI'];
	//echo $Whatisthepage;
	
	
	}
	
	if ($PixGallery_SEOTitleContent == 0 || $PixGallery_SEOTitleContent == NULL){
		$pxgheaderoutput = $pxgtitlecontent;
		$pxgheaderoutput = str_replace("/", " ",$pxgheaderoutput);
			if ($usegallerytype == 1){
				$pxgheaderoutput = $PixGallery_Lang_Output_Frontend['seo_title_gallery_prepend'].$pxgheaderoutput.$PixGallery_Lang_Output_Frontend['seo_title_gallery_append'];
				}
			else {
				$pxgheaderoutput = $PixGallery_Lang_Output_Frontend['seo_title_image_prepend'].$pxgheaderoutput.$PixGallery_Lang_Output_Frontend['seo_title_image_append'];
				}
		}
	//Have To Add customnames in entire path, not done yet, for now shows actual names
	else {
		if ($pxgistoplevel == 1) {
		//$pxgheaderoutput = str_replace("/", " ",$pxgpathtoobject);
		//$pxgheaderoutput = $PixGallery_Lang_Output_Frontend['seo_breadcrumb_delimiter'].$pxgheaderoutput;
		$pxgheaderoutput = $PixGallery_Lang_Output_Frontend['seo_breadcrumb_prepend'].$PixGallery_Lang_Output_Frontend['seo_breadcrumb_delimiter'].$pxgtitlecontent.$PixGallery_Lang_Output_Frontend['seo_breadcrumb_append'];
		}
		else {
		$pxgheaderoutput = str_replace("/", $PixGallery_Lang_Output_Frontend['seo_breadcrumb_delimiter'],$pxgpathtoobject);
		$pxgheaderoutput = $PixGallery_Lang_Output_Frontend['seo_breadcrumb_prepend'].$pxgheaderoutput.$PixGallery_Lang_Output_Frontend['seo_breadcrumb_append'];
		}
		}	
	
	if ($PixGallery_SEOTitlePlace == 0 || $PixGallery_SEOTitlePlace == NULL) {
		$pxgtitleaddition .= $pxgheaderoutput;
		}
	elseif ($PixGallery_SEOTitlePlace == 1) {
		$pxgtitleaddition = $pxgheaderoutput.$pxgtitleaddition;
		}
	elseif ($PixGallery_SEOTitlePlace == 2) {
		$pxgtitleaddition = $pxgheaderoutput;
		}
	else {
		}


return $pxgtitleaddition;
}

function PixGallery_AddToHeader()
{
global $wp_query,$wp_pxg_url,$PixGallery_RootCache,$PixGallery_DisplayRSS,$PixGallery_MetaKeywords,$awsom_pixgallery_caption_table_name,$awsom_pixgallery_gallery_table_name,$wpdb;

	if(is_single() || is_page()) {
	$thisentriescontent = $wp_query->post->post_content;
	$thisentriescontent = strtolower($thisentriescontent);
	$isthereagallery = strpos($thisentriescontent, '[/pixgallery]');
	$isthereagallerylegacy = strpos($thisentriescontent, '</pixgallery>');
		if ($isthereagallerylegacy !== false) {
	
	
		$StartIndex		= 0;
		$TagName		= "pixgallery";
	
		$TagBegin		= strpos($thisentriescontent, "<$TagName");
		$TagBeginEnd	= strpos($thisentriescontent, ">", $TagBegin);
		$TagEnd			= strpos($thisentriescontent, "</$TagName>");

		if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
			{
			$TagBeginEnd	+= 1;
			$TagContents	= substr($thisentriescontent, $TagBegin, $TagBeginEnd - $TagBegin);
			$TopGalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));
			}
			$whatIsGet = $_SERVER['QUERY_STRING'];
			$whatIsGet = htmlspecialchars($whatIsGet);
			$whatIsGet = str_replace("%2F", "/", $whatIsGet);
			$pos = strpos($whatIsGet, "px=/");
			If ($pos === false){
			$pxgtags = $TopGalleryPath;
			}
			else {
			$pxgtags = $TopGalleryPath.substr($whatIsGet,($pos + 3));
			}
			$pos = $pos + 3;
			//$pos = strpos($whatIsGet, "/");
			$pos2 = strrpos($whatIsGet, "/") + 1;
			$length = $pos2 - $pos;
			
			$pxgtags = str_replace("+", " ", $pxgtags);
			$pxgtags = str_replace("//", "/", $pxgtags);
			If ($length <= 1){
				$rest = $TopGalleryPath;
				$rest = str_replace("/", "-", $rest);
				$pxgheaddisplayfeedlink = $wp_pxg_url.'/'.$PixGallery_RootCache.'/Pixgallery'.$rest.'imagefeed.xml';
				}
			else {
			$rest = substr($whatIsGet,$pos,$length);
			$rest = str_replace("+", " ", $rest);
			$rest = $TopGalleryPath.$rest;
			$rest = str_replace("//", "/", $rest);
			$rest = str_replace("/", "-", $rest);
			$pxgheaddisplayfeedlink = $wp_pxg_url.'/'.$PixGallery_RootCache.'/Pixgallery'.$rest.'imagefeed.xml';
			}
			if ($PixGallery_DisplayRSS == 1){
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Pixgallery ".$rest." Gallery Feed\" href=\"".$pxgheaddisplayfeedlink."\" />";
			}
			if ($PixGallery_MetaKeywords == 1){
			$pxgwheretolookfortags = substr($pxgtags, -1);
				if ($pxgwheretolookfortags == "/"){
				$pxgtagcontent = $wpdb->get_var("SELECT galtags FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$pxgtags'");
				$pxgtagcontent = stripslashes($pxgtagcontent);
				$pxgtagcontent = str_replace(" ", ",", $pxgtagcontent);
				}
				else{
				$pxgtagcontent = $wpdb->get_var("SELECT imgtags FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$pxgtags'");
				$pxgtagcontent = stripslashes($pxgtagcontent);
				$pxgtagcontent = str_replace(" ", ",", $pxgtagcontent);
				}
			
			echo "<meta name=\"keywords\" content=\"".$pxgtagcontent."\" >";
			}
		}
		
	if ($isthereagallery !== false) {
		
	
	
		$StartIndex		= 0;
		$TagName		= "pixgallery";
	
		$TagBegin		= strpos($thisentriescontent, "[$TagName");
		$TagBeginEnd	= strpos($thisentriescontent, "]", $TagBegin);
		$TagEnd			= strpos($thisentriescontent, "[/$TagName]");

		if (($TagBegin !== FALSE) && ($TagEnd !== FALSE))
			{
			$TagBeginEnd	+= 1;
			$TagContents	= substr($thisentriescontent, $TagBegin, $TagBeginEnd - $TagBegin);
			$TopGalleryPath	= urldecode(PixGallery_HtmlAttribute($TagContents, "path"));
			}
			$whatIsGet = $_SERVER['QUERY_STRING'];
			$whatIsGet = htmlspecialchars($whatIsGet);
			$whatIsGet = str_replace("%2F", "/", $whatIsGet);
			$pos = strpos($whatIsGet, "px=/");
			If ($pos === false){
			$pxgtags = $TopGalleryPath;
			}
			else {
			$pxgtags = $TopGalleryPath.substr($whatIsGet,($pos + 3));
			}
			$pos = $pos + 3;
			//$pos = strpos($whatIsGet, "/");
			$pos2 = strrpos($whatIsGet, "/") + 1;
			$length = $pos2 - $pos;
			
			$pxgtags = str_replace("+", " ", $pxgtags);
			$pxgtags = str_replace("//", "/", $pxgtags);
			If ($length <= 1){
				$rest = $TopGalleryPath;
				$rest = str_replace("/", "-", $rest);
				$pxgheaddisplayfeedlink = $wp_pxg_url.'/'.$PixGallery_RootCache.'/Pixgallery'.$rest.'imagefeed.xml';
				}
			else {
			$rest = substr($whatIsGet,$pos,$length);
			$rest = str_replace("+", " ", $rest);
			$rest = $TopGalleryPath.$rest;
			$rest = str_replace("//", "/", $rest);
			$rest = str_replace("/", "-", $rest);
			$pxgheaddisplayfeedlink = $wp_pxg_url.'/'.$PixGallery_RootCache.'/Pixgallery'.$rest.'imagefeed.xml';
			}
			if ($PixGallery_DisplayRSS == 1){
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Pixgallery ".$rest." Gallery Feed\" href=\"".$pxgheaddisplayfeedlink."\" />";
			}
			if ($PixGallery_MetaKeywords == 1){
			$pxgwheretolookfortags = substr($pxgtags, -1);
				if ($pxgwheretolookfortags == "/"){
				$pxgtagcontent = $wpdb->get_var("SELECT galtags FROM $awsom_pixgallery_gallery_table_name WHERE galpath = '$pxgtags'");
				$pxgtagcontent = stripslashes($pxgtagcontent);
				$pxgtagcontent = str_replace(" ", ",", $pxgtagcontent);
				}
				else{
				$pxgtagcontent = $wpdb->get_var("SELECT imgtags FROM $awsom_pixgallery_caption_table_name WHERE imagepath = '$pxgtags'");
				$pxgtagcontent = stripslashes($pxgtagcontent);
				$pxgtagcontent = str_replace(" ", ",", $pxgtagcontent);
				}
			
			echo "<meta name=\"keywords\" content=\"".$pxgtagcontent."\" >";
			}
			
		}
	//Debug Section	
	//echo $TopGalleryPath;
	//echo "<br>";
	
	//echo "pos is ".$pos;
	//echo "<br>";
	//echo "rest is".$rest;
	//echo "<br>";
	//echo "what is get".$whatIsGet;
	//echo "<br>";
	//echo "pxgtags is".$pxgtags;
	//echo "feed link =".$pxgheaddisplayfeedlink;
	//echo "<br>";
	//$Whatisthepage = $_SERVER['REQUEST_URI'];
	//echo $Whatisthepage;
	
	}
}

// ***** AWSOM create Caption table  on plugin activation *****//
add_action('activate_awsom-pixgallery/pixgallery.php','PixGallery_CaptionTableInstall');
// ***** end Caption table add *****//
if ($PixGallery_AddFooterCredit == "1") {
	if (function_exists('AWSOM_Archive_Footer_Credit')){
		 remove_action('wp_footer', 'AWSOM_Archive_Footer_Credit',11);
		 }
		if (function_exists('AWSOM_Footer_Credit')){
		 	 remove_action('wp_footer', 'AWSOM_Footer_Credit',11);
	 }
	add_action('wp_footer', 'PixGallery_Footer_Credit');
}
if ($PixGallery_IndividualComments == "1"){
add_filter('comment_post_redirect', 'Pixgallery_Comment_Proper_Return', 30);
add_filter('comments_array', 'Pixgallery_Comment_Show', 60);
add_action('comment_form', 'Pixgallery_Comment_Passvars', 10);
add_action('comment_post', 'Pixgallery_Comment_Tracking', 60);
}
if ($PixGallery_SEOTitle == 1){
add_filter('wp_title', 'PixGallery_EditTitle', 10);
}
if ($PixGallery_ShareThisConnector == 1){
add_action('wp_head', 'PixGallery_ShareThis_Integration', 10);
}
add_action('wp_head', 'PixGallery_AddToHeader', 10);
add_action('delete_comment', 'Pixgallery_CleanupTracking', 60);
add_action('wp_head',		'PixGallery_Header', 5);
add_action('admin_menu',	'PixGallery_Options_Hook');
add_filter('the_content',	'PixGallery_PhotoGallery', 10);
add_filter('the_content',	'PixGallery_PhotoGalleryNewTag', 10);
add_filter('the_content',	'PixGallery_Image', 10);
?>