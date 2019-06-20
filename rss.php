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
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<?php
// // Find top ten videos
// $i = 0;
// $items = [];
// foreach ($elements as $element) {
//     // if ($i > 10) {
//     //     break;
//     // } 

//     // Find title in element
//     $titles = $element->find($titleTag);    
//     if($titles) {
//     	foreach ($titles as $titleKey => $title) {
//     		$items[$i]['title'] = trim($title->plaintext);
// 	    }
//     }

//     // Find title in element
//     $thumbnails = $element->find('img');    
//     if($thumbnails) {
//     	foreach ($thumbnails as $titleKey => $thumbnail) {
//     		$imageAttributes = $thumbnail->attr;
//     		$items[$i]['thumbnail'] = $imageAttributes['src'];
// 	    }
//     }

//     // Find title in element
//     $descriptions = $element->find($descriptionTag);    
//     if($descriptions) {
//     	foreach ($descriptions as $titleKey => $description) {
//     		$items[$i]['description'] = trim($description->plaintext);
// 	    }
//     }

//     // // get title attribute
//     // $videoTitle = $videoDetails->title;

//     // // get href attribute
//     // $videoUrl = 'https://youtube.com' . $videoDetails->href;

//     // // push to a list of videos
//     // $videos[] = [
//     //     'title' => $videoTitle,
//     //     'url' => $videoUrl
//     // ];

//     $i++;
// }

// // echo "<pre>";
// // print_r($items);
// // echo "</pre>";

?>

<rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
        <?php do_action('rss2_ns'); ?>>
<channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>        
        <?php foreach ($elements as $element): ?>
            <?php if($i < 10): ?>                
                <item>
                    <?php foreach ($element->find($titleTag) as $i => $title): ?>
                        <title><?php echo trim($title->plaintext) ?></title>
                    <?php endforeach; ?>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <dc:creator><?php the_author(); ?></dc:creator>
                    <guid isPermaLink="false"><?php the_guid(); ?></guid>
                    <?php foreach ($element->find($descriptionTag) as $i => $description): ?>
                        <description><![CDATA[<?php echo trim($description->plaintext) ?>]]></description>
                        <content:encoded><![CDATA[<?php echo trim($description->plaintext) ?>]]></content:encoded>
                    <?php endforeach; ?>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
                <?php $i++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
</channel>
</rss>