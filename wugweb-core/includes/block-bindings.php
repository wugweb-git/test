<?php
defined('ABSPATH') || exit;
add_action('init', function(){
 if(function_exists('register_block_bindings_source')){
  register_block_bindings_source('wugweb/post-meta',array('label'=>'Wugweb post data','get_value_callback'=>function($source_args,$block_instance){
   $post=get_post(); if(!$post)return null; $key=sanitize_key($source_args['key']??''); return $key==='copyright_year'?gmdate('Y'):$post->post_meta[$key]??get_post_meta($post->ID,$key,true);
  }));
 }
 register_block_type(__DIR__.'/../blocks/read-time');
});
