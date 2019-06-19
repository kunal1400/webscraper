<?php

require('simplehtmldom/simple_html_dom.php');

if( empty($_GET['url']) ) {
	echo "url is required";
	die;
}
if( empty($_GET['wrapperTag']) ) {
	echo "wrapperTag is required";
	die;
}
if( empty($_GET['titleTag']) ) {
	echo "titleTag is required";
	die;
}
if( empty($_GET['descriptionTag']) ) {
	echo "descriptionTag is required";
	die;
}

/**
* Generating the global parameter
**/
$url 			= $_GET['url'];
$wrapperTag 	= strtolower( $_GET['wrapperTag'] );
$titleTag 		= strtolower( $_GET['titleTag'] );
$descriptionTag = strtolower( $_GET['descriptionTag'] );

echo "<br/>-------------- SELECTORS FOUND ------------------<br/>";
echo "url =". $url . "<br/>";
echo "wrapperTag =". $wrapperTag . "<br/>";
echo "titleTag =". $titleTag . "<br/>";
echo "descriptionTag =". $descriptionTag . "<br/>";
echo "<br/>-------------- /SELECTORS FOUND ------------------<br/>";

// Create DOM from URL or file
$html = file_get_html($url);
$elements = $html->find($wrapperTag);

// Find top ten videos
$i = 0;
$items = [];
foreach ($elements as $element) {
    // if ($i > 10) {
    //     break;
    // } 

    // Find title in element
    $titles = $element->find($titleTag);    
    if($titles) {
    	foreach ($titles as $titleKey => $title) {
    		$items[$i]['title'] = trim($title->plaintext);
	    }
    }

    // Find title in element
    $thumbnails = $element->find('img');    
    if($thumbnails) {
    	foreach ($thumbnails as $titleKey => $thumbnail) {
    		$imageAttributes = $thumbnail->attr;
    		$items[$i]['thumbnail'] = $imageAttributes['src'];
	    }
    }

    // Find title in element
    $descriptions = $element->find($descriptionTag);    
    if($descriptions) {
    	foreach ($descriptions as $titleKey => $description) {
    		$items[$i]['description'] = trim($description->plaintext);
	    }
    }

    // // get title attribute
    // $videoTitle = $videoDetails->title;

    // // get href attribute
    // $videoUrl = 'https://youtube.com' . $videoDetails->href;

    // // push to a list of videos
    // $videos[] = [
    //     'title' => $videoTitle,
    //     'url' => $videoUrl
    // ];

    $i++;
}

echo "<pre>";
print_r($items);
echo "</pre>";