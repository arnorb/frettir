<?php

define('SIMPLEPIE_RELATIVE_DATE', 'YmdHis'); // We'll define this here so we won't have to remember it later.
 
function doRelativeDate($posted_date) {
    /*
        This function returns either a relative date or a formatted date depending
        on the difference between the current datetime and the datetime passed.
            $posted_date should be in the following format: YYYYMMDDHHMMSS
 
        Relative dates look something like this:
            3 weeks, 4 days ago
        Formatted dates look like this:
            on 02/18/2004
 
        The function includes 'ago' or 'on' and assumes you'll properly add a word
        like 'Posted ' before the function output.
 
        By Garrett Murray, http://graveyard.maniacalrage.net/etc/relative/
    */
    $in_seconds = strtotime(substr($posted_date,0,8).' '.
                  substr($posted_date,8,2).':'.
                  substr($posted_date,10,2).':'.
                  substr($posted_date,12,2));
    $diff = time()-$in_seconds;
    $months = floor($diff/2592000);
    $diff -= $months*2419200;
    $weeks = floor($diff/604800);
    $diff -= $weeks*604800;
    $days = floor($diff/86400);
    $diff -= $days*86400;
    $hours = floor($diff/3600);
    $diff -= $hours*3600;
    $minutes = floor($diff/60);
    $diff -= $minutes*60;
    $seconds = $diff;
 
    if ($months>0) {
        // over a month old, just show date (mm/dd/yyyy format)
        return substr($posted_date,6,2).'/'.substr($posted_date,4,2).'/'.substr($posted_date,0,4);
    } else {
        if ($weeks>0) {
            // weeks and days
            $relative_date .= ($relative_date?' og ':'').$weeks.' viku'.($weeks%10!=1||$weeks==11?'m':'');
            $relative_date .= $days>0?($relative_date?' og ':'').$days.($days%10!=1?' dögum':' degi'):'';
        } elseif ($days>0) {
            // days and hours
            $relative_date .= ($relative_date?' og ':'').$days.($days%10!=1||$days==11?' dögum':' degi');
            $relative_date .= $hours>0?($relative_date?' og ':'').$hours.' tím'.($hours%10!=1||$hours==11?'um':'a'):'';
        } elseif ($hours>0) {
            // hours and minutes
            $relative_date .= ($relative_date?' og ':'').$hours.' tím'.($hours%10!=1||$hours==11?'um':'a');
            $relative_date .= $minutes>0?($relative_date?' og ':'').$minutes.' mínútu'.($minutes%10!=1||$minutes==11?'m':''):'';
        } elseif ($minutes>0) {
            // minutes only
            $relative_date .= ($relative_date?' og ':'').$minutes.' mínútu'.($minutes%10!=1||$minutes==11?'m':'');
        } else {
            // seconds only
            $relative_date .= ($relative_date?' og ':'').$seconds.' sekúndu'.($seconds%10!=1||$seconds==11?'m':'');
        }
    }
    // show relative date and add proper verbiage
    return $relative_date/*.' síðan'*/;
}


?>