<?php
// File: includes/functions.php

function timeAgo($datetime)
{
    $currentTime = new DateTime();
    $timeDiff = $currentTime->diff(new DateTime($datetime));
    if ($timeDiff->y > 0) {
        return $timeDiff->y . ' years ago';
    } elseif ($timeDiff->m > 0) {
        return $timeDiff->m . ' months ago';
    } elseif ($timeDiff->d > 0) {
        return $timeDiff->d . ' days ago';
    } elseif ($timeDiff->h > 0) {
        return $timeDiff->h . ' hours ago';
    } elseif ($timeDiff->i > 0) {
        return $timeDiff->i . ' minutes ago';
    } elseif ($timeDiff->s > 0) {
        return $timeDiff->s . ' seconds ago';
    } else {
        return 'just now';
    }
}
