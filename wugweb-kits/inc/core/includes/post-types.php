<?php
defined('ABSPATH') || exit;
add_action('init', function(){
 register_post_type('case_study', array('labels'=>array('name'=>'Case Studies','singular_name'=>'Case Study'),'public'=>true,'has_archive'=>true,'rewrite'=>array('slug'=>'work','with_front'=>false),'show_in_rest'=>true,'supports'=>array('title','editor','excerpt','thumbnail','author')));
 register_taxonomy('discipline','case_study',array('label'=>'Discipline','public'=>true,'show_in_rest'=>true,'rewrite'=>array('slug'=>'discipline')));
 register_taxonomy('industry','case_study',array('label'=>'Industry','public'=>true,'show_in_rest'=>true,'rewrite'=>array('slug'=>'industry')));
});
