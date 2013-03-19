<?php


class ImageResize{
  var $permit_types = array( "gif", "jpeg", "png");
  var $file_types = array( "", "gif", "jpeg", "png", "swf", "psd", "bmp", "tiff", "tiff", "jpc", "jp2", "jpx", "jb2", "swc", "iff", "wbmp", "xbm");

  function __construct( $filename=null, $remote=false){
    if( !$filename){
      throw new Exception( "Not Selected File.");
    }

    $this->filename_orig = $filename;
    $this->filename = $filename;
    $this->is_remote = $remote;
    $this->_initial_file();
    $this->width();
    $this->height();
    $this->ratio();
    $this->quality();
    $this->type();
  }

  function __descruct(){
  }

  function _func_getstatus(){

    $url = $this->filename_orig;
    $header = null;
    $options = array(
		     CURLOPT_RETURNTRANSFER => true,
		     CURLOPT_HEADER         => true,
		     CURLOPT_FOLLOWLOCATION => true,
		     CURLOPT_ENCODING       => "",
		     CURLOPT_USERAGENT      => "status checker",
		     CURLOPT_SSL_VERIFYPEER => false,
		     CURLOPT_SSL_VERIFYHOST => false,
		     CURLOPT_AUTOREFERER    => true,
		     CURLOPT_CONNECTTIMEOUT => 120,
		     CURLOPT_TIMEOUT        => 120,
		     CURLOPT_MAXREDIRS      => 10,
		     );
    $curl = curl_init( $url);
    curl_setopt_array( $curl, $options);
    $content = curl_exec( $curl);

    if( !curl_errno( $curl)) {
      $header = curl_getinfo( $curl);
    }
    curl_close( $curl);

    return $header['http_code'];
  }

  function _func_is_remotefile(){
    $statuscode = $this->_func_getstatus();
    if( $statuscode != 200){
      throw new Exception( "URL not found.");
    }
  }

  function _func_is_localfile(){
    if( !file_exists( $this->filename_orig)){
      throw new Exception( 'File not found.');
    }
  }

  function _initial_file(){
    if( $this->is_remote){
      $this->_func_is_remotefile();
    } else{
      $this->_func_is_localfile();
    }

    $file_info = getimagesize( $this->filename_orig);
    if( $file_info == false){
      throw new Exception( 'File not image.');
    }
    if( !in_array( $this->file_types[$file_info[2]], $this->permit_types)){
      throw new Exception( 'Invalid image type.');
    }

    $this->image_type_orig = $this->file_types[$file_info[2]];
    $this->image_width_orig = $file_info[0];
    $this->image_height_orig = $file_info[1];
    $this->crop = false;
  }

  function _func_is_int( $values, $is_int=true){
    if( !is_array( $values)){
      $values = array( $values);
    }

    foreach( $values as $value){
      if( !is_numeric( $value)){
	throw new Exception( "Not numeric.");
      }
      if( $is_int and !is_int( $value)){
	throw new Exception( "Not int.");
      }
      if( $value < 0){
	throw new Exception( "Not natural number.");
      }
    }

    return true;
  }

  function filename( $filename=null){
    $this->filename = $filename ? $filename : $this->filename_orig;
  }

  function width( $width=0){
    $this->_func_is_int( $width);

    $this->image_width = $width ? $width : null;
  }

  function height( $height=0){
    $this->_func_is_int( $height);

    $this->image_height = $height ? $height : null;
  }

  function ratio( $ratio=0){
    $this->_func_is_int( $ratio, false);

    $this->image_ratio = $ratio ? $ratio : 1;
  }

  function area( $top=0, $left=0, $width=0, $height=0){
    $this->_func_is_int( array( $top, $left, $width, $height));

    if( $top == 0 and $left == 0 and $width == 0 and $height == 0){
      $this->crop = false;
      $this->crop_position_top = 0;
      $this->crop_position_left = 0;
      $this->crop_width = $this->image_width_orig;
      $this->crop_height = $this->image_height_orig;
    } else{
      $this->crop = true;
      $this->crop_position_top = $top;
      $this->crop_position_left = $left;
      $this->crop_width = $width;
      $this->crop_height = $height;
    }
  }

  function quality( $quality=80){
    $this->_func_is_int( $quality);
    if( $quality > 100){
      throw new Exception( "Invalid range.");
    }

    $this->image_quality = $quality;
  }

  function type( $type=null){
    if( $type != null and !in_array( $type, $this->permit_types)){
      throw new Exception( "Invalid image type.");
    }

    $this->image_type = $type ? $type : $this->image_type_orig;
  }

  function _func_imagecreate(){
    switch( $this->image_type_orig){
    case "gif":
      return imagecreatefromgif( $this->filename_orig);
    case "jpeg":
      return imagecreatefromjpeg( $this->filename_orig);
    case "png":
      return imagecreatefrompng( $this->filename_orig);
    }
  }


  function _make(){
    if( $this->crop){
      $crop_position_left = $this->crop_position_left;
      $crop_position_top = $this->crop_position_top;
      $crop_width = $this->crop_width;
      $crop_height = $this->crop_height;

      if( empty( $this->image_width) and empty( $this->image_height)){
	$image_width = $this->crop_width;
	$image_height = $this->crop_height;
      } elseif( empty( $this->image_width)){
	$image_width = round( $this->crop_width * ( $this->image_height / $this->crop_height));
	$image_height = $this->image_height;
      } elseif( empty( $this->image_height)){
	$image_width = $this->image_width;
	$image_height = round( $this->crop_height * ( $this->image_width / $this->crop_width));
      } elseif( $this->image_width and $this->image_height){
	$image_width = $this->image_width;
	$image_height = $this->image_height;
      }
    }else {
      if( empty( $this->image_width) and empty( $this->image_height)){
	$image_width = $this->image_width_orig;
	$image_height = $this->image_height_orig;
      } elseif( empty( $this->image_width)){
	$image_width = round( $this->image_height / ( $this->image_height_orig / $this->image_width_orig));
	$image_height = $this->image_height;
      } elseif( empty( $this->image_height)){
	$image_width = $this->image_width;
	$image_height = round( $this->image_width / ( $this->image_width_orig / $this->image_height_orig));
      } elseif( $this->image_width and $this->image_height){
	$image_width = $this->image_width;
	$image_height = $this->image_height;
      }
    }

    if( $this->image_ratio != 1){
      $image_width = round( $image_width * $this->image_ratio);
      $image_height = round( $image_height * $this->image_ratio);
    }

    $image = $this->_func_imagecreate();
    $new_image = imagecreatetruecolor( $image_width, $image_height);
    if( $this->crop){
      imagecopyresampled( $new_image, $image, 0, 0, $crop_position_top, $crop_position_left, $image_width, $image_height, $crop_width, $crop_height);
    } else{
      imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $image_width, $image_height, $this->image_width_orig, $this->image_height_orig);
    }

    imagedestroy( $image);

    return $new_image;
  }

  function _func_quality(){
    switch( $this->image_type){
    case "gif":
      return 0;
      break;
    case "jpeg":
      return $this->quality;
      break;
    case "png":
      $quality = $this->image_quality ? $this->image_quality : 1;
      $quality = 10 - round( $quality / 10, 0, PHP_ROUND_HALF_DOWN);
      $quality = $quality == 10 ? 9 : $quality;
      return $quality;
      break;
    }

  }

  function save(){
    $new_image = $this->_make();

    switch( $this->image_type){
    case "gif":
      imagegif( $new_image, $this->filename);
      break;
    case "jpeg":
      imagejpeg( $new_image, $this->filename, $this->image_quality);
      break;
    case "png":
      imagepng( $new_image, $this->filename, $this->_func_quality());
      break;
    }

    imagedestroy( $new_image);
  }

  function show(){
    header( str_replace( "[IMAGE_TYPE]", $this->image_type, "Content-Type: image/[IMAGE_TYPE]\n\n"));

    $new_image = $this->_make();

    switch( $this->image_type){
    case "gif":
      imagegif( $new_image, null);
      break;
    case "jpeg":
      imagejpeg( $new_image, null, $this->image_quality);
      break;
    case "png":
      imagepng( $new_image, null, $this->_func_quality());
      break;
    }

    imagedestroy( $new_image);
  }
}
