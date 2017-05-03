<?php declare(strict_types=1);

namespace hikari_no_yume\TwitterBot;

interface TweetDeviser {
    public function devise(): string;
}
