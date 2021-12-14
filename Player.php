<?php
namespace classes\tv;

require_once __DIR__ . '/../../config.php';

use classes\linguage\Lingua;

//ini_set("error_reporting", E_ALL);
//include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php"; 
//include_once $_SERVER['DOCUMENT_ROOT'] . "/classes/ClassLingua.php";
//define('YOUTUBE_DL', '/usr/local/bin/youtube-dl'); // find your youtube-dl path and replace with it
 
/**
 * The objective of this classe is to provide a way to see a lista of videos 
 * @author António Lira Fernandes
 * @version 4.5
 * @updated 05-out-2018 21:50:00
 */

//Problemas conhecidos

class Player{
	//REQUIREMENTS
	//YouTube-dl
  //    YouTube-dl is a command line utility for downloading videos from over 30 different sources. It started out as a simple download but has evolved into something much larger than that.
  //    to instal: 
  //          sudo wget https://yt-dl.org/downloads/latest/youtube-dl -O /usr/local/bin/youtube-dl
  //          sudo chmod a+rx /usr/local/bin/youtube-dl
  //    verify the permissions for owner of /home/userActual/.cache/youtube-dl/
	
  //MISSION
  //The objective of this classe is to provide a way to see a lista of videos 
  
  
	//METHODS
	// __construct() - class constructor
	// 
	
	

//########################################## varibles ###############################################################################	
	
	/**
	 * Este array vai receber todos os textos de output da classe
	 */
  private $texts=array("errBrowser"=>"Your browser does not support the video tag.");
  private $listVideos;
	private $width="320";
  private $height="240";
  private $style="float:left;
                  width: :width;
                  height: :height;
                  margin:10px;    
                  border:1px solid silver;";
	private $first;
  private $autoplay="autoplay";
  private $loop=1;
  private $id="videoPlayer";
  private $start=0;
  

  //###################################################################################################################################

	/**
	 * class constructor
	 */
  public function __construct(){  

    $linguage= new Lingua();
    $this->texts=$linguage->textsTranslate($this->texts);
    
  }
  
  private function managerType($link){
    $pattern = '/www.youtube.com/';
    if (preg_match($pattern, $link)){
        $link = $this->getDirectUrl($link);
    }
    return $link;
  }
  
  
    /**
   * Fetches direct URL of given youtube video
   */
  private function getDirectUrl($youtube_video) {
    // lets build command to get direct url via youtube-dl
    $video_json_command = YOUTUBE_DL.' -f best -g '.$youtube_video;
    // get url
    $direct_url = shell_exec($video_json_command);
  // remove any possible white spaces
    $direct_url = str_replace(array(' ',"\n"), '', $direct_url);
  return $direct_url;
   }
  
  
  private function getReady(){
    $videos=explode(",", $this->listVideos);
    
    $this->first=$videos[0];
    $this->listVideos=""; 
    $this->first=$this->managerType($this->first);
    $this->first = str_replace("'", "", $this->first);
    $this->addVideo($this->first);
    for ($i=1;$i<count($videos);$i++){
      $this->addVideo($this->managerType($videos[$i]));
    }
   $this->listVideos = str_replace("''", "'", $this->listVideos);
  }
 
  //###################################################################################################################################
  
  public function addVideo($newVideo){
    $sep="";
    if ($this->listVideos<>""){
      $sep=",";
    }
    //echo "<br><br>lv: " .  $this->listVideos;
    $this->listVideos=$this->listVideos  . $sep . "'" . $newVideo . "'";
    //echo "<br>lv: " .  $this->listVideos;
  }
  
  //###################################################################################################################################
  
  public function getVideos(){
    return $this-listVideos;
  }
 
   //###################################################################################################################################
 
   public function setStart($time){
    $this->start=$time;
  }
  
   public function setId($id){
    $this->id=$id;
  }
  
   public function setAutoplay($autoplay){
    $this->autoplay=$autoplay;
  }
  
   public function setLoop($loop){
    $this->loop=$loop;
  }
  
  public function setWidth($width){
    $this->width=$width;
  }
  
   //###################################################################################################################################
 
  public function setStyle($style){
    $this->style=$style;
  }
  
   //###################################################################################################################################
 
  public function setHeight($height){
    $this->height=$height;
  }
  
    //###################################################################################################################################
 
  public function getStyle(){
    $this->style=str_replace(":height",$this->height . "px",$this->style);
    $this->style=str_replace(":width",$this->width . "px",$this->style);
    return $this->style;
  }
  
  //###################################################################################################################################
 
  public function setDimention($width, $height){
    $this->height=$height;
    $this->width=$width;
  }
  
  //###################################################################################################################################
 
  public function setVideos($listOfVideos){
    $this->listVideos=$listOfVideos;
  }

	//###################################################################################################################################
  /**
	* Faz o que é necessaro para manter a tabela numa página html. Lista os dados e permite inserir novos, editar e apagar registos. Usa um parametro 'do' para tomar as decisões
	*/
	public function doHTML(){
	  	
    
    //print_r($this->listVideos);
    $this->getReady();
    $txtauto="autoplay=1";
    //echo "autoplay= " . $this->autoplay;
    if ($this->autoplay!="autoplay" && $this->autoplay!=1){
      //echo "<br>entrei aqui";
      $txtauto="";
    }
     ?>
    <style>
      #videoPlayer {
        <?php
        echo $this->getStyle();
        ?>
      }
    </style>
    
      

      <video class="js-media" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" <?php echo $txtauto; ?>   controls id="<?php echo $this->id; ?>">
          <source src="<?php echo $this->first; ?>" type="video/mp4" />
          <?php echo $this->texts["errBrowser"]; ?>
      </video>
    
    
      <script>
        document.getElementById('<?php echo $this->id; ?>').currentTime =<?php echo $this->start; ?>;
      </script>

     <script type="text/javascript">
        var nextVideo = [<?php echo $this->listVideos; ?>];
        var curVideo = 0;
        var loop = <?php echo $this->loop ?>;
        var videoPlayer = document.getElementById('<?php echo $this->id; ?>');
              videoPlayer.onended = function(){
		              ++curVideo;
                  if ((curVideo == nextVideo.length) && (loop==1)){
                     curVideo = 0;
                  }
                  if(curVideo < nextVideo.length){    		
                      videoPlayer.src = nextVideo[curVideo]; 
                      
                  } 
              }
    </script>


    <?php
	}

//###################################################################################################################################
  /**
	* Faz o que é necessaro para manter a tabela numa página html. Lista os dados e permite inserir novos, editar e apagar registos. Usa um parametro 'do' para tomar as decisões
	*/
	public function doOnlyOneHTML(){
	  	
    $this->getReady();
     $txtauto="autoplay";
    if ($this->autoplay=="no" || $this->autoplay==0){
      $txtauto="";
    }
     ?>
   
    <video class="js-media video" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" <?php echo $txtauto; ?>   controls id="<?php echo $this->id; ?>">
        <source src="<?php echo $this->first; ?>" type="video/mp4">
        <?php echo $this->texts["errBrowser"]; ?>
    </video>  

    <script>
       document.getElementById('<?php echo $this->id; ?>').currentTime =<?php echo $this->start; ?>;
    </script>
    <?php
	}

  
   
}

//###################################################################################################################################
//###################################################################################################################################
//###################################################################################################################################

//exemplos de utilização
/*
$player=new Player();
//$player->setVideos('http://www.w3schools.com/html/movie.mp4,https://www.youtube.com/watch?v=t1GKQnBbjCU,http://www.w3schools.com/html/mov_bbb.mp4,https://www.youtube.com/watch?v=SCTXPhxvU9s&t=476s');
$player->setVideos('http://www.w3schools.com/html/movie.mp4,http://www.w3schools.com/html/mov_bbb.mp4,http://www.w3schools.com/html/movie.mp4');
$player->setLoop(0);
$player->doHTML();
*/
?>
