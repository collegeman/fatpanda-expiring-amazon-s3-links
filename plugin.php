<?php
/*
Plugin Name: Expiring Amazon S3 Links by Fat Panda
Plugin URI: http://github.com/collegeman/fatpanda-expiring-amazon-s3-links
Description: A shortcode for generating temporary links to things stored privately on Amazon S3.
Author: Fat Panda, LLC
Author URI: http://fatpandadev.com
Version: 0.1.1
License: GPL2
*/

/*
Copyright (C)2012 Fat Panda, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// http://css-tricks.com/snippets/php/generate-expiring-amazon-s3-link/

define('FPEAS3', __FILE__);
define('FPEAS3_DIR', dirname(__FILE__));

@define('FPEAS3_AWS_S3_ACCESS_ID', '');
@define('FPEAS3_AWS_S3_SECRET', '');

add_action('init', 'fpeas3_init');

function fpeas3_init() {
  add_shortcode('s3', 'fpeas3_shortcode');
}

function fpeas3_shortcode($atts, $content = null) {
  extract(shortcode_atts(array(
    'expires' => '5',
    'bucket' => '',
    'path' => ''
  ), $atts));

  if (!$content = trim($content)) {
    $content = 'Download';
  }

  $keys = array(
    'access_id' => get_post_meta(get_the_ID(), 'aws_s3_access_id', true),
    'secret' => get_post_meta(get_the_ID(), 'aws_s3_secret', true)
  );

  if (empty($keys['access_id']) || empty($keys['secret'])) {
    $keys = fpeas3_get_static_keys();
  }

  if (empty($keys['access_id']) || empty($keys['secret'])) {
    $error = "Expiring Amazon S3 Links not setup correctly: missing Access ID or Secret.";
    error_log($error);
    if (current_user_can('admin')) {
      return $error;
    } else {
      return '';
    }
  }

  return sprintf('<a rel="nofollow" href="%s" class="s3-temp-link">%s</a>', 
    fpeas3_get_temporary_link($keys['access_id'], $keys['secret'], $bucket, $path, $expires), $content);
}

function fpeas3_get_static_keys() {
  return array(
    'access_id' => FPEAS3_AWS_S3_ACCESS_ID,
    'secret' => FPEAS3_AWS_S3_SECRET
  );
}

/**
 * Calculate the HMAC SHA1 hash of a string.
 * @param string $key The key to hash against
 * @param string $data The data to hash
 * @param int $blocksize Optional blocksize
 * @return string HMAC SHA1
 */
function fpeas3_crypto_hmacSHA1($key, $data, $blocksize = 64) {
  if (strlen($key) > $blocksize) $key = pack('H*', sha1($key));
  $key = str_pad($key, $blocksize, chr(0x00));
  $ipad = str_repeat(chr(0x36), $blocksize);
  $opad = str_repeat(chr(0x5c), $blocksize);
  $hmac = pack( 'H*', sha1(
    ($key ^ $opad) . pack( 'H*', sha1(
      ($key ^ $ipad) . $data
    ))
  ));
  return base64_encode($hmac);
}

/**
 * Create temporary URLs to your protected Amazon S3 files.
 * @param string $accessKey Your Amazon S3 access key
 * @param string $secretKey Your Amazon S3 secret key
 * @param string $bucket The bucket (bucket.s3.amazonaws.com)
 * @param string $path The target file path
 * @param int $expires In minutes
 * @return string Temporary Amazon S3 URL
 * @see http://awsdocs.s3.amazonaws.com/S3/20060301/s3-dg-20060301.pdf
 */
function fpeas3_get_temporary_link($accessKey, $secretKey, $bucket, $path, $expires = 5) {
  // Calculate expiry time
  $expires = time() + intval(floatval($expires) * 60);
  // Fix the path; encode and sanitize
  $path = str_replace('%2F', '/', rawurlencode($path = ltrim($path, '/')));
  // Path for signature starts with the bucket
  $signpath = '/'. $bucket .'/'. $path;
  // S3 friendly string to sign
  $signsz = implode("\n", $pieces = array('GET', null, null, $expires, $signpath));
  // Calculate the hash
  $signature = fpeas3_crypto_hmacSHA1($secretKey, $signsz);
  // Glue the URL ...
  $url = sprintf('http://%s.s3.amazonaws.com/%s', $bucket, $path);
  // ... to the query string ...
  $qs = http_build_query($pieces = array(
    'AWSAccessKeyId' => $accessKey,
    'Expires' => $expires,
    'Signature' => $signature,
  ));
  // ... and return the URL!
  $tempUrl = $url.'?'.$qs;
  return $tempUrl;
}