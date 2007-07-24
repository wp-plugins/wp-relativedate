<?php
/*
Plugin Name: WP-RelativeDate
Plugin URI: http://lesterchan.net/portfolio/programming.php
Description: Displays relative date alongside with your post/comments actual date. Like 'Today', 'Yesterday', '2 Days Ago', '2 Weeks Ago', '2 'Seconds Ago', '2 Minutes Ago', '2 Hours Ago'.
Version: 1.11
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  
	Copyright 2007  Lester Chan  (email : gamerz84@hotmail.com)

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


### Create Text Domain For Translation
load_plugin_textdomain('wp-relativedate', 'wp-content/plugins/relativedate');


### Function: Display Post Relative Date (Today/Yesterday/Days Ago/Weeks Ago)
add_filter('the_date', 'relative_post_date', '', 4);
function relative_post_date($the_date, $d, $before, $after, $display_ago_only = false) {
	global $id, $post, $previous_day;
	$the_date = strip_tags($the_date);
	if(gmdate('Y', current_time('timestamp')) != get_post_time('Y')) {
		return $before.$the_date.$after;
	}
	$day_diff = (gmdate('z', current_time('timestamp')) - get_post_time('z'));
	if($day_diff < 0) { $day_diff = 32; }
	if ($the_date != $previous_day) {
		if($day_diff == 0) {
			return $before.__('Today', 'wp-relativedate').$after;
		} elseif($day_diff == 1) {
			return $before. __('Yesterday', 'wp-relativedate').$after;
		} elseif ($day_diff < 7) {
			if($display_ago_only) {
				return $before.sprintf(__('%s days ago', 'wp-relativedate'), $day_diff).$after;
			} else {
				return $before.$the_date.' ('.sprintf(__('%s days ago', 'wp-relativedate'), $day_diff).')'.$after;
			}
		} elseif ($day_diff < 31) {
			if($display_ago_only) {
				return $before.sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7)).$after;
			} else {
				return $before.$the_date.' ('.sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7)).')'.$after;
			}
		} else {
			return $before.$the_date.$after;
		}
		$previous_day = $the_date;
	}
}


### Alternative To WordPress the_date().
function relative_post_the_date($d = '', $before = '', $after = '', $display_ago_only = false, $display = true) {
	global $id, $post;
	if (empty($d)) {
		$the_date .= mysql2date(get_option('date_format'), $post->post_date);
	} else {
		$the_date .= mysql2date($d, $post->post_date);
	}
	$output = '';
	if(gmdate('Y', current_time('timestamp')) != get_post_time('Y')) {
		$output = $before.$the_date.$after;
	} else {
		$day_diff = (gmdate('z', current_time('timestamp')) - get_post_time('z'));
		if($day_diff < 0) { $day_diff = 32; }
		if($day_diff == 0) {
			$output = $before.__('Today', 'wp-relativedate').$after;
		} elseif($day_diff == 1) {
			$output = $before. __('Yesterday', 'wp-relativedate').$after;
		} elseif ($day_diff < 7) {
			if($display_ago_only) {
				$output = $before.sprintf(__('%s days ago', 'wp-relativedate'), $day_diff).$after;
			} else {
				$output = $before.$the_date.' ('.sprintf(__('%s days ago', 'wp-relativedate'), $day_diff).')'.$after;
			}
		} elseif ($day_diff < 31) {
			if($display_ago_only) {
				$output = $before.sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7)).$after;
			} else {
				$output = $before.$the_date.' ('.sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7)).')'.$after;
			}
		} else {
			$output = $before.$the_date.$after;
		}
	}
	if($display) {
		echo $output;
	} else {
		return $output;
	}
}


### Function: Display Post Relative Time (Seconds Ago/Minutes Ago/Hours Ago)
add_filter('the_time', 'relative_post_time');
function relative_post_time($current_timeformat, $display_ago_only = 0) {
	$current_time = current_time('timestamp');
	$date_today_time = gmdate('j-n-Y H:i:s', $current_time);
	$post_date_time = get_post_time('j-n-Y H:i:s');
	$date_today = gmdate('j-n-Y', $current_time);
	$post_date = get_post_time('j-n-Y');
	$time_diff = (strtotime($date_today_time) - strtotime($post_date_time));
	$format_ago = '';
	if($post_date == $date_today) {
		if($time_diff < 60) {
			$format_ago = sprintf(__('%s seconds ago', 'wp-relativedate'), $time_diff);
		} elseif ($time_diff < 120) {
			$format_ago = __('1 minute ago', 'wp-relativedate');
		} elseif ($time_diff < 3600) {
			$format_ago = sprintf(__('%s minutes ago', 'wp-relativedate'), intval($time_diff/60));
		} elseif ($time_diff < 7200) {
			$format_ago = __('1 hour ago', 'wp-relativedate');
		} elseif ($time_diff < 86400) {
			$format_ago = sprintf(__('%s hours ago', 'wp-relativedate'), intval($time_diff/3600));
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
	if(gmdate('Y', current_time('timestamp')) != mysql2date('Y', $comment_date)) {
		return $current_dateformat;
	}
	$day_diff = (gmdate('z', current_time('timestamp')) - mysql2date('z', $comment_date));
	if($day_diff < 0) { $day_diff = 32; }
	if($day_diff == 0) {
		return __('Today', 'wp-relativedate');
	} elseif($day_diff == 1) {
		return __('Yesterday', 'wp-relativedate');
	} elseif ($day_diff < 7) {
		if($display_ago_only) {
			return sprintf(__('%s days ago', 'wp-relativedate'), $day_diff);
		} else {
			return $current_dateformat.' ('.sprintf(__('%s days ago', 'wp-relativedate'), $day_diff).')';
		}
	} elseif ($day_diff < 31) {
		if($display_ago_only) {
			return sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7));
		} else {
			return $current_dateformat.' ('.sprintf(__('%s weeks ago', 'wp-relativedate'), ceil($day_diff/7)).')';
		}
	} else {
		return $current_dateformat;
	}
}


### Function: Display Comment  Relative Time (Seconds Ago/Minutes Ago/Hours Ago)
add_filter('get_comment_time', 'relative_comment_time');
function relative_comment_time($current_timeformat, $display_ago_only = 0) {
	global $comment;	
	$current_time = current_time('timestamp');
	$date_today_time = gmdate('j-n-Y H:i:s', $current_time);
	$comment_date_time = mysql2date('j-n-Y H:i:s', $comment->comment_date);
	$date_today = gmdate('j-n-Y', $current_time);
	$comment_date = mysql2date('j-n-Y', $comment->comment_date);
	$time_diff = (strtotime($date_today_time) - strtotime($comment_date_time));
	$format_ago = '';
	if($comment_date == $date_today) {
		if($time_diff < 60) {
			$format_ago = sprintf(__('%s seconds ago', 'wp-relativedate'), $time_diff);
		} elseif ($time_diff < 120) {
			$format_ago = __('1 minute ago', 'wp-relativedate');
		} elseif ($time_diff < 3600) {
			$format_ago = sprintf(__('%s minutes ago', 'wp-relativedate'), intval($time_diff/60));
		} elseif ($time_diff < 7200) {
			$format_ago =  __('1 hour ago', 'wp-relativedate');
		} elseif ($time_diff < 86400) {
			$format_ago = sprintf(__('%s hours ago', 'wp-relativedate'), intval($time_diff/3600));
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