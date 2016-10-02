
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon ref link generator GP</title>
  </head>
  <body>
          <form method="POST">
              <label for="searchquery">Suchbegriff oder ASIN eingeben
                    <input type="text" id="searchquery" name="searchquery">
              <input type="submit" value="Suche Artikel"></br>
              </label>
          </form>
           
<?php
      /**
      *
      * Skript um Suchabfragen an Amazon.de zu senden und aus der Atrikel liste
      * Referenzurls zu berechnen
      *
      * @author     Daniel Rohr <daniel@gameplane.de>
      * @license    GPLV3
      * @link       https://github.com/KiteDragon
      * @since      24.06.2016
      * 
      */     
  
     if(isset($_POST["searchquery"]))
     {  
       
       // Amazon REF-Tag
       $amazon_tag  = "gameplanede-21";
       
       // Ausgabelimit
       $limit       = 5;
       
        // blanks durch '+' ersetzen und zum such link zussamensetzen
        $keywordstr = str_replace(" ", "+", $_POST["searchquery"]);
        $searchurl  = "https://www.amazon.de/s/&url=search-alias%3Daps&field-keywords=". $keywordstr;

        //Referenz ID 
        $ref        = "&tag=".$amazon_tag;

        //Download des HTML codes der Ergebnisseite 
        $ch = curl_init($searchurl);
        $timeout = 5;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $searchurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        // Filtern nach <a> Tags
        $dom = new DOMDocument;
        $dom->loadHTML($html);

        $arr = $dom->getElementsByTagName("a");
        
        $i = 0;

        // Durchlaufen aller <a> Tags und deren ChildNodes 
        // Aufbau: <a href=[LINK ZUM ARTIKEL]><h2>[BESCHREIBUNG ARTIKEL]</h2></a>
        // Ausgabe als <a> Tag
        foreach ($arr as $product)
        {
           foreach ($product->childNodes as $headline)
           {
              if($headline->tagName == "h2")
              {
                echo "<a href='".$product->getAttribute("href").$ref."'>".$headline->nodeValue."</a></br>";
                if(++$i == $limit) break; // Bricht ab $limit automatisch ab.
              }
           }
        }
       
        
     }
?>
</body>
</html>
