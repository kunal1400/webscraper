<?php
include( plugin_dir_path( __FILE__ ) . 'test/simplehtmldom/simple_html_dom.php');

global $post;

/**
* Generating the global parameter
**/
$url 			= get_post_meta($post->ID, '_urlForFeeds', ARRAY_A);
$wrapperTag 	= get_post_meta($post->ID, '_itemWrapper', ARRAY_A);
$titleTag 		= get_post_meta($post->ID, '_titleWrapper', ARRAY_A);
$descriptionTag = get_post_meta($post->ID, '_descriptionWrapper', ARRAY_A);
$permalink      = get_the_permalink($post->ID);

// echo "<br/>-------------- SELECTORS FOUND ------------------<br/>";
// echo "url =". $url . "<br/>";
// echo "wrapperTag =". $wrapperTag . "<br/>";
// echo "titleTag =". $titleTag . "<br/>";
// echo "descriptionTag =". $descriptionTag . "<br/>";
// echo "<br/>-------------- /SELECTORS FOUND ------------------<br/>";

if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    wp_die($url.' is not a valid URL');
}

// Create DOM from URL or file
$html = file_get_html($url);

$elements = $html->find($wrapperTag);
$i = 0;
?>

<?php
// Find top ten videos
$i = 0;
$items = [];
foreach ($elements as $element) {
    if ($i > 10) {
        break;
    } 

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

// echo "<pre>";
// print_r($items);
// echo "</pre>";

echo "<div>";
echo "<div class='wrapper'>
        <span>
            <img src='".plugin_dir_url( __FILE__ ).'images/feed-icon-28x28.png'."' alt='RSS feed url' title='RSS feed url' />
        </span>
        <span>
            <a target='_blank' href='".$permalink."'>$permalink</a>
        </span>
    </div>";

echo "<h1>Preview:</h1>";
foreach ($items as $i => $item) {    
    echo '<div class="well">
        <h3><a>'.$item["title"].'</a></h3>
        <p>'.$item["description"].'</p>
    </div>';
}
echo "</div>";
?>
<style type="text/css">
    .well {
        background-color: #f5f5f5;
        padding: 15px;
        border-bottom: 1px dotted #ddd;
    }
    .wrapper {
    }
    .wrapper > span {
        display: inline-block;
        vertical-align: middle;
    }
</style>