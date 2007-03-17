<?php
/*
Plugin Name: WP-RelativeDate
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Displays Relative Date To Your Post
Version: 1.00
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


/*  Copyright 2005  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Function: Display Post Relative Date (Today/Yesterday/Days Ago/Weeks Ago)
add_filter('the_date', 'relative_post_date');
function relative_post_date($current_dateformat, $display_ago_only = 0) {
	global $previous_day;
	$day_diff = (date('z', current_time('timestamp', 1)) - get_post_time('z'));
	if($day_diff < 0) { $day_diff = 32; }
	if ($current_dateformat != $previous_day) {
		if($day_diff == 0) {
			return __('Today');
		} elseif($day_diff == 1) {
			return __('Yesterday');
		} elseif ($day_diff < 7) {
			if($display_ago_only) {
				return __("$day_diff days ago");
			} else {
				return $current_dateformat.' ('.__("$day_diff days ago").')';
			}
		} elseif ($day_diff < 31) {
			if($display_ago_only) {
				return __(ceil($day_diff/7).' weeks ago');
			} else {
				return $current_dateformat.' ('.__(ceil($day_diff/7).' weeks ago').')';
			}
		} else {
			return $current_dateformat;
		}
		$previous_day = $current_dateformat;
	}
}


### Function: Display Post Relative Time (Seconds Ago/Minutes Ago/Hours Ago)
add_filter('the_time', 'relative_post_time');
function relative_post_time($current_timeformat, $display_ago_only = 0) {
	$date_today = date('j-n-Y', current_time('timestamp', 1));
	$post_date = get_post_time('j-n-Y');
	$time_diff = (current_time('timestamp', 1) - get_post_time());
	$format_ago = '';
	if($post_date == $date_today) {
		if($time_diff < 60) {
			$format_ago = __($time_diff.' seconds ago');
		} elseif ($time_diff < 120) {
			$format_ago = __('1 minute ago');
		} elseif ($time_diff < 3600) {
			$format_ago = __(intval($time_diff/60).' minutes ago');
		} elseif ($time_diff < 7200) {
			$format_ago = __('1 hour ago');
		} elseif ($time_diff < 86400) {
			$format_ago = __(intval($time_diff/3600).' hours ago');
		}
		if($display_ago_only) {
			return $format_ago;
		} else {
			return $current_timeformat.' ('.$format_ago.')';
		}
	} else {
		return $current_timeformat;
	}
}


### Function: Display Comment Relative Date (Today/Yesterday/Days Ago/Weeks Ago)
add_filter('get_comment_date', 'relative_comment_date');
function relative_comment_date($current_dateformat, $display_ago_only = 0) {
	global $comment;
	$comment_date = $comment->comment_date;
	$day_diff = (date('z', current_time('timestamp', 1)) - mysql2date('z', $comment_date));
	if($day_diff < 0) { $day_diff = 32; }
	if($day_diff == 0) {
		return __('Today');
	} elseif($day_diff == 1) {
		return __('Yesterday');
	} elseif ($day_diff < 7) {
		if($display_ago_only) {
			return __("$day_diff days ago");
		} else {
			return $current_dateformat.' ('.__("$day_diff days ago").')';
		}
	} elseif ($day_diff < 31) {
		if($display_ago_only) {
			return __(ceil($day_diff/7).' weeks ago');
		} else {
			return $current_dateformat.' ('.__(ceil($day_diff/7).' weeks ago').')';
		}
	} else {
		return $current_dateformat;
	}
}


### Function: Display Comment  Relative Time (Seconds Ago/Minutes Ago/Hours Ago)
add_filter('get_comment_time', 'relative_comment_time');
function relative_comment_time($current_timeformat, $display_ago_only = 0) {
	global $comment;	
	$comment_date = $comment->comment_date;
	$date_today = date('j-n-Y', current_time('timestamp', 1));
	$post_date = mysql2date('j-n-Y', $comment_date);
	$time_diff = (current_time('timestamp', 1) - mysql2date('U', $comment_date));
	$format_ago = '';
	if($post_date == $date_today) {
		if($time_diff < 60) {
			$format_ago = __($time_diff.' seconds ago');
		} elseif ($time_diff < 120) {
			$format_ago = __('1 minute ago');
		} elseif ($time_diff < 3600) {
			$format_ago = __(intval($time_diff/60).' minutes ago');
		} elseif ($time_diff < 7200) {
			$format_ago = __('1 hour ago');
		} elseif ($time_diff < 86400) {
			$format_ago = __(intval($time_diff/3600).' hours ago');
		}
		if($display_ago_only) {
			return $format_ago;
		} else {
			return $current_timeformat.' ('.$format_ago.')';
		}
	} else {
		return $current_timeformat;
	}
}
?>