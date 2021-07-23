<?php
class gecpsimageModuleFrontController extends ModuleFrontController
{
  public $params = array();

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	   $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/image.tpl');


  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }
    if (isset($_POST['action'])) {
      if (method_exists($this,$_POST['action'])) {
        $this->{$_POST['action']}();
      }
    }

  }

  public function initParams(){
    $this->params = array(

    );
  }

  public function getAroundPoints($x,$y,$w,$h){
    $x_max = $w-1;
    $y_max = $y-1;
    $x1 = $x==0?0:$x-1;
    $x2 = $x==$x_max?$x_max:$x+1;
    $y1 = $y==0?0:$y-1;
    $y2 = $y==$y_max?$y_max:$y+1;
    $x_array = [$x1,$x,$x2];
    $y_array = [$y1,$y,$y2];
    $p=[];
    foreach($x_array as $x_a){
      foreach ($y_array as $y_a) {
        if ($x_a == $x && $y_a == $y) {
          continue;
        }
        $p[]=[$x_a,$y_a];

      }
    }
    // if ($x==228&$y==206) {
    //   var_dump($p);
    // }
    return $p;

  }

  public function parseJpgToPngDiv(){
    $id_image = $_GET['id_image'];
    if (isset($id_image)) {
      $image = new Image($_GET['id_image']);
      $file_path = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($id_image) . $id_image."-medium_default.jpg";
      $img_size = getimagesize($file_path);
      $im = imagecreatefromjpeg($file_path);
      $red = imagecolorallocate($im, 255, 0, 0);
      $w = $_GET['w'];
      $d = $_GET['d'];
      for ($y=0; $y <$img_size[1] ; $y++) {
        if ($img_size[1]*0.8>$y || $y>$img_size[1]*0.85) {
          continue;
        }
        for ($x=0; $x < $img_size[0]; $x++) {
          if ($img_size[0]*0.55>$x || $x>$img_size[0]*0.65) {
            continue;
          }
          $rgb = imagecolorat($im,$x,$y);
          $r = ($rgb >> 16) & 0xFF;
          $g = ($rgb >> 8) & 0xFF;
          $b = $rgb & 0xFF;
          if ($r >= $w && $g >= $w && $b >= $w) {
            echo "<span x='$x' y='$y' style='background:red'></span>";
          }
          else {
            $points = $this->getAroundPoints($x,$y,$img_size[0],$img_size[1]);
            $has_w = 0;
            $has_d = 0;
            foreach ($points as $key => $point) {
              $rgb2 = imagecolorat($im,$point[0],$point[1]);
              $r2 = ($rgb2 >> 16) & 0xFF;
              $g2 = ($rgb2 >> 8) & 0xFF;
              $b2 = $rgb2 & 0xFF;
              if ($r2 >= $w && $g2 >= $w && $b2 >= $w) {
                $has_w = 1;
              }
              elseif ($r2-$r>=$d || $g2-$g>=$d  || $b2-$b>=$d ) {
                $has_d = 1;
              }
            }

            if ($has_w && $has_d) {
              echo "<span x='$x' y='$y' style='background:red'></span>";
            }
            else {
              echo "<span x='$x' y='$y' style='background:rgb($r,$g,$b)'></span>";
            }


          }
        }
        echo "<br>";
      }

      imagedestroy($im);
    }
  }
  public function parseJpgToPngWD(){
    $id_image = $_GET['id_image'];
    if (isset($id_image)) {
      $image = new Image($_GET['id_image']);
      $file_path = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($id_image) . $id_image.".jpg";
      $img_size = getimagesize($file_path);
      $im = imagecreatefromjpeg($file_path);
      $red = imagecolorallocate($im, 255, 0, 0);
      $w = $_GET['w'];
      $d = $_GET['d'];
      for ($y=0; $y <$img_size[1] ; $y++) {
        // if ($img_size[1]*0.1>$y || $y>$img_size[1]*0.5) {
        //   continue;
        // }
        for ($x=0; $x < $img_size[0]; $x++) {
          // if ($img_size[0]*0.18>$x || $x>$img_size[0]*0.2) {
          //   continue;
          // }
          $rgb = imagecolorat($im,$x,$y);
          $r = ($rgb >> 16) & 0xFF;
          $g = ($rgb >> 8) & 0xFF;
          $b = $rgb & 0xFF;
          if ($r >= $w && $g >= $w && $b >= $w) {
            imagesetpixel($im,$x,$y,$red);
          }
          else {
            $points = $this->getAroundPoints($x,$y,$img_size[0],$img_size[1]);
            $has_w = 0;
            $has_d = 0;
            foreach ($points as $key => $point) {
              $rgb2 = imagecolorat($im,$point[0],$point[1]);
              $r2 = ($rgb2 >> 16) & 0xFF;
              $g2 = ($rgb2 >> 8) & 0xFF;
              $b2 = $rgb2 & 0xFF;
              if ($r2 >= $w && $g2 >= $w && $b2 >= $w) {
                $has_w = 1;
              }
              elseif ($r2-$r>=$d || $g2-$g>=$d  || $b2-$b>=$d ) {
                $has_d = 1;
              }
            }

            if ($has_w && $has_d) {
              imagesetpixel($im,$x,$y,$red);
            }
            else {
              //echo "<span x='$x' y='$y' style='background:rgb($r,$g,$b)'></span>";
            }


          }
        }
        //echo "<br>";
      }
      header('Content-Type: image/png');
      imagepng($im);
      imagedestroy($im);
    }
  }

  public function parseJpgToPng(){
    $id_image = $_GET['id_image'];
    if (isset($id_image)) {
      $image = new Image($_GET['id_image']);
      $file_path = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($id_image) . $id_image.".jpg";
      $img_size = getimagesize($file_path);
      $im = imagecreatefromjpeg($file_path);
      $red = imagecolorallocate($im, 255, 0, 0);
      $w = $_GET['w'];
      for ($y=0; $y <$img_size[1] ; $y++) {
        for ($x=0; $x < $img_size[0]; $x++) {
          $rgb = imagecolorat($im,$x,$y);
          $r = ($rgb >> 16) & 0xFF;
          $g = ($rgb >> 8) & 0xFF;
          $b = $rgb & 0xFF;
          if ($r >= $w && $g >= $w && $b >= $w) {
            imagesetpixel($im,$x,$y,$red);
          }
        }
      }
      header('Content-Type: image/png');
      imagepng($im);
      imagedestroy($im);




    //   $white = imagecolorallocate($img_obj, 255, 255, 255);
    //   imagecolortransparent($img_obj, $white);
    // header('Content-Type: image/png');
    //   imagepng($img_obj);
    //   imagedestroy($img_obj);


    }
  }
}
