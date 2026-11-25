<?php
/** Sitemap Index — .sitemap-grid from footer/sitemap.astro, fed by WordPress content. */
defined('ABSPATH') || exit;
$pages = array('/' => 'Home', '/about/' => 'About', '/services/' => 'Services', '/our-work/' => 'Work', '/blog/' => 'Blog', '/faq/' => 'FAQ', '/contact-us/' => 'Contact', '/impact/' => 'Impact', '/subscription/' => 'Subscription');
$services = get_pages(array('parent' => (int) (get_page_by_path('services')->ID ?? 0), 'sort_column' => 'menu_order,post_title'));
$cs = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC'));
$posts = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC', 'ignore_sticky_posts' => true));
$legal = array('/privacy-policy/' => 'Privacy Policy', '/cookie-policy/' => 'Cookie Policy', '/terms-of-use/' => 'Terms of Use', '/refund-policy/' => 'Refund Policy');
$col = function ($n, $title, $items, $more_url = '', $more_n = 0) { ?>
  <div class="sitemap-col">
    <div class="sitemap-col-head"><span class="sitemap-n"><?php echo esc_html($n); ?></span><h2><?php echo esc_html($title); ?></h2></div>
    <ul>
      <?php foreach ($items as $url => $label) : ?><li><a href="<?php echo esc_url(home_url($url)); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?>
      <?php if ($more_n > 0) : ?><li><a href="<?php echo esc_url(home_url($more_url)); ?>" class="sitemap-more">… and <?php echo (int) $more_n; ?> more</a></li><?php endif; ?>
    </ul>
  </div>
<?php };
$svc = array(); foreach ($services as $s) { $svc[wp_make_link_relative(get_permalink($s))] = get_the_title($s); }
$csl = array(); foreach (array_slice($cs, 0, 24) as $id) { $csl[wp_make_link_relative(get_permalink($id))] = get_the_title($id); }
$pl = array(); foreach (array_slice($posts, 0, 24) as $id) { $pl[wp_make_link_relative(get_permalink($id))] = get_the_title($id); }
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap static-page sitemap', 'id' => 'static-page')); ?>>
  <div class="sitemap-grid" data-reveal>
    <?php
    $col('01', 'Pages', $pages);
    $col('02', 'Services (' . count($svc) . ')', $svc);
    $col('03', 'Case Studies (' . count($cs) . ')', $csl, '/our-work/', max(0, count($cs) - 24));
    $col('04', 'Blog Posts (' . count($posts) . ')', $pl, '/blog/', max(0, count($posts) - 24));
    $col('05', 'Legal', $legal);
    ?>
  </div>
</section>
