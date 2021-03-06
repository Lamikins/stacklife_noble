<?php
/****************************
 * This script hits the Awesome API to retrieve the n recently awesomed items.
 * We grab the hollis ID of each awesome item and use it to get details of the item from the
 * LibraryCloud API. We package that up and serialize it ot a JSON object that is used display
 * a StackView stack on the homepage.


 To DO:
 Get shelfrank?
 ****************************/


    /**$sl_home = dirname(dirname(dirname(__FILE__)));**/
    require_once(__DIR__ . '/../../etc/sl_ini.php');
    
    if($live){
      require_once('/var/local/noble/circ/circ_counts.php');  
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 2,
        /**CURLOPT_URL => "http://librarylab.law.harvard.edu/awesome/api/item/recently-awesome?limit=200"
        We may want to randomize a search query here, since Noblenet does not support "recently awesomed" data**/
        CURLOPT_URL => "http://catalog.noblenet.org/opac/extras/opensearch/1.1/NOBLE/mods/keyword/?searchTerms=artificial+intelligence+neural+networks&count=40"
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $noble_response = json_decode($json);

    $static_docs = array();

    foreach ($noble_response->mods as $item) {


        // Do we need to set default like this, or does StackView do that for us?
        $static_doc = array('title' => 'Unknown Title', 'creator' => array(), 'measurement_page_numeric' => 0, 'measurement_height_numeric' => 0, 'pub_date' => 0);

        // boy i love inconsistent data
        // The labels in noblenet and the Awesome API don't always match. Let's align those here.
        if (
          !is_array($item->titleInfo)
          && property_exists($item->titleInfo, 'title')
          && !empty($item->titleInfo->title)
        ) {
            $static_doc['title'] =  preg_replace("/[^A-Za-z0-9_\s-]/", "",$item->titleInfo->title);
        }
        else {
            if (property_exists($item->titleInfo[0], 'title') && !empty($item->titleInfo[0]->title)) {

                $title = preg_replace("/[^A-Za-z0-9_\s-]/", "",$item->titleInfo[0]->title);

                if(property_exists($item->titleInfo[0], 'nonSort') && !empty($item->titleInfo[0]->nonSort)){
                    $title = ($item->titleInfo[0]->nonSort) . $title;
                }

                $static_doc['title'] =  $title;
            }
        }

        if (is_array($item->name)) {
          foreach($item->name as $name) {
            // XXX - make sure that namePart exists
            if(is_array($name->namePart)){
                array_push($static_doc['creator'], $name->namePart[0]);
            }else{
                array_push($static_doc['creator'], $name->namePart);
            }
            
          }
        }
        else if (property_exists($item->name, 'namePart') && !empty($item->name->namePart)) {
          if(is_array($item->name->namePart)){
            $static_doc['creator'] = array($item->name->namePart[0]);
          }else{
            $static_doc['creator'] = array($item->name->namePart);
          }
        }
        
        //------------------------------------
        //Get physical description of the item
        //------------------------------------
        
        if (!empty($item->physicalDescription->extent)) {
            if( preg_match('/([1-9]*\s*)(?=cm)/',$item->physicalDescription->extent,$height) ){
                $height_cm= $height[0];
            }
            if( preg_match('/([1-9]*\s*)(?=p)/',$item->physicalDescription->extent,$p)) {
               $pages = $p[0];
            }
        }

        if(!$height_cm || $height_cm > 33 || $height_cm < 20) $height_cm = 27;
        if(!$pages) $pages = 200;
        $static_doc['measurement_page_numeric'] = $pages;
        $static_doc['measurement_height_numeric'] = $height_cm;
        
        //--------------
        //Shelfrank
        //--------------
        
        $libs = array('BEVERLY','BUNKERHILL','DANVERS','ENDICOTT','EVERETT','GLOUCESTER','GORDON','LYNNFIELD','LYNN','MARBLEHEAD','MELROSE','MERRIMACK','MIDDLESEX','MONTSERRAT','NORTHSHORE','NORTHERNESSEX','PEABODY','READING','REVERE','SALEM','SALEMSTATE','SAUGUS','STONEHAM','SWAMPSCOTT','WAKEFIELD','WINTHROP','PANO','PANA','PANB','PANC', 'PANG', 'PANI', 'PANK','PANP');
      
      $shelfrank = 1;
      if($live){
          foreach($libs as $library){
              $shelfrank = $shelfrank + getNOBLECirculationCount(array($item->recordInfo->recordIdentifier),$library)[$item->recordInfo->recordIdentifier];
          } 
          if($shelfrank >= 400){
            $shelfrank = 100;
          }else{
            $shelfrank = floor($shelfrank/4);
          }
      }
        
        //set shelfrank to random number for now
        $static_doc['shelfrank'] = $shelfrank;

        
        if(is_array($item->originInfo->dateIssued)){
            $static_doc['pub_date'] = intval($item->originInfo->dateIssued[1]);
        }else{
            $static_doc['pub_date'] = intval($item->originInfo->dateIssued);
        }

        /** if (property_exists($item, 'typeOfResource') && !empty($item->typeOfResource)) {
            $type = $item->typeOfResource;
            if($type == "text"){
                $type="Book";
            }
             = $type;
        } **/
        
        $static_doc['format'] = "Book";
        
        if(is_array($item->titleInfo)){
            $title_nf = $item->titleInfo[0]->title;
        }else{
            $title_nf = $item->titleInfo->title;
        }

        $title_link_friendly = strtolower($title_nf);
        //Make alphanumeric (removes all other characters)
        $title_link_friendly = preg_replace("/[^a-z0-9_\s-]/", "",$title_link_friendly);
        //Clean up multiple dashes or whitespaces
        $title_link_friendly = preg_replace("/[\s-]+/", " ", $title_link_friendly);
        //Remove spaces from end of line.
        $title_link_friendly = preg_replace("/\s+$/", "", $title_link_friendly);
        //Convert whitespaces and underscore to dash
        $title_link_friendly = preg_replace("/[\s_]/", "-", $title_link_friendly);

        if(!empty($item->identifier[0]->{'@attributes'}->invalid) && ($item->identifier[0]->{'@attributes'}->invalid == 'yes')){
            //we simply don't add items that have invalid ISBNs

            //$static_doc['link'] = "/item/" . $title_link_friendly . '/' . $item->recordInfo->recordIdentifier;
        }else{
            //may run into errors with this regex.
            if(is_string($item->identifier[0])){
                $isbn = preg_replace("/\s.*/","",$item->identifier[0]);
                if(strlen($isbn) != 13 || strlen($isbn) != 10){
                    //test out using record identifier
                    $static_doc['link'] = $www_root . "/item/" . $title_link_friendly . '/' . $item->recordInfo->recordIdentifier;
                    $static_docs[] = $static_doc;
                }
            }
        }
    }

    $complete_object = array();
    $complete_object['start'] = -1;
    $complete_object['limit'] = 0;
    $complete_object['num_found'] = count($static_docs);
    $complete_object['docs'] = $static_docs;
    $serialized_object = json_encode($complete_object);

    // Let's make sure we have at least 10 items and then we'll write them out to a static JSON file
    if ($complete_object['num_found'] > 10) {
        $file_path = __DIR__ . '/../web/js/awesome.json';
        // Write the contents back to the file
        file_put_contents($file_path, $serialized_object);
    }
?>
