<?php declare(strict_types=1);

namespace hikari_no_yume\TwitterBot;

class Tweeter {
    private $tweetDeviser;
    private $twitterKeys;
    private $tweetInterval = 30;
    private $deviseRetryInterval = 60;
    private $tweetRetryInterval = 60;

    public function __construct(TweetDeviser $tweetDeviser) {
        $this->tweetDeviser = $tweetDeviser;
    }

    public function setTweetInterval(int $minutes) /* : void */ {
        $this->tweetInterval = $minutes;
    }

    public function setDeviseRetryInterval(int $seconds) /* : void */ {
        $this->deviseRetryInterval = $seconds;
    }

    public function setTweetRetryInterval(int $seconds) /* : void */ {
        $this->tweetRetryInterval = $seconds;
    }

    public function setTwitterKeys(array $twitterKeys) /* : void */ {
        if (!isset(
            $twitterKeys["oauth_access_token"],
            $twitterKeys["oauth_access_token_secret"],
            $twitterKeys["consumer_key"],
            $twitterKeys["consumer_secret"]
            )) {
            throw new \RuntimeException("One or all of the keys oauth_access_token, oauth_access_token_secret, consumer_key and consumer_key missing");
        }
        $this->twitterKeys = $twitterKeys;
    }

    public function loadTwitterKeysFromJSONFile(string $filename) /* : void */ {
        $json = @\file_get_contents($filename);
        if ($json === FALSE) {
            throw new \RuntimeException("Could not load Twitter keys from JSON file \"$filename\"");
        }
        $twitterKeys = \json_decode($json, true);
        if (!\is_array($twitterKeys)) {
            throw new \RuntimeException("Twitter keys file contains NULL or invalid JSON");
        }
        $this->setTwitterKeys($twitterKeys);
    }

    private function tweet(string $tweet) /* : void */ {
        $twitter = new \TwitterAPIExchange($this->twitterKeys);
        $twitter->buildOauth('https://api.twitter.com/1.1/statuses/update.json', 'POST')
            ->setPostfields([
                'status' => $tweet
            ])
            ->performRequest();
    }

    public function run() /* : void */ {
        while (true) {
            echo "Hello! I'm going to make a tweet.", PHP_EOL;

retry_devise:
            try {
                $phrase = $this->tweetDeviser->devise();
                echo "Devised '$phrase'!", PHP_EOL;
            } catch (\Throwable $e) {
                echo "Oops! Error when devising phrase: $e", PHP_EOL;
                echo "Waiting $this->deviseRetryInterval seconds and retrying.", PHP_EOL;
                sleep($this->deviseRetryInterval);
                goto retry_devise;
            }

retry_tweet:
            try {
                $this->tweet($phrase);
                echo "Tweeted '$phrase'!", PHP_EOL;
            } catch (\Throwable $e) {
                echo "Oops! Error when tweeting: $e", PHP_EOL;
                echo "Waiting $this->tweetRetryInterval seconds and retrying.", PHP_EOL;
                sleep($this->tweetRetryInterval);
                goto retry_tweet;
            }

            echo "Sleeping for $this->tweetInterval minutes...", PHP_EOL;
            sleep($this->tweetInterval * 60);
        }
    }
}
