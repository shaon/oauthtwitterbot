<?php
require_once 'twitteroauth/twitteroauth.php';
require_once 'config.php';
require 'simplepie.inc';

/* Connect to twitter via Abraham's oAuth Library */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$feed = new SimplePie();
$feed->set_feed_url(FEED_URL);
$feed->init();
$feed->handle_content_type();

/* find the total number of available feed */
$max = $feed->get_item_quantity();

/* Get last tweeted data */
$last_saved_tweet = file_get_contents('last_tweet');

/* Get the first item on the feed */
$item = $feed->get_item(0);

/* permalink of the first item */
$itemlink = $item->get_permalink();

/* save the permalink into file */
file_put_contents('last_tweet', $itemlink);

/* title of the first item */
$itemtitle = $item->get_title();

/* length of the item title */
$titlelength = strlen($itemtitle);

if ($titlelength > 110) {
    $itemtitle = substr($itemtitle, 0, 107) . "...";
}

if ($itemlink != $last_saved_tweet) {

    /* shorten the url with tinyurl */
    $shortlink = file_get_contents("http://tinyurl.com/api-create.php?url=" . $itemlink);

    /* Update status with Abraham's oAuth Library */
    $connection->post('statuses/update', array ('status' => $itemtitle . " " . $shortlink));
}

?>