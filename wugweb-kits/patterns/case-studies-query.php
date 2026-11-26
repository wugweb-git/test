<?php
/**
 * Title: Featured Case Studies (query)
 * Slug: wugweb-kits/case-studies-query
 * Categories: wugweb-kits
 * Description: Query loop listing the latest case studies as cards.
 * Keywords: case studies,query,work
 */
?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained","wideSize":"1200px"}} --><div class="wp-block-group alignwide"><!-- wp:heading {"level":2,"fontSize":"h2"} --><h2 class="wp-block-heading has-h2-font-size">Work that leads with clarity and delivers with impact</h2><!-- /wp:heading --><!-- wp:query {"queryId":11,"query":{"perPage":6,"pages":0,"offset":0,"postType":"case_study","order":"desc","orderBy":"date","inherit":false}} --><div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":2}} --><!-- wp:group {"backgroundColor":"ash","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"}}} --><div class="wp-block-group has-ash-background-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-featured-image {"isLink":true} /--><!-- wp:post-terms {"term":"discipline","textColor":"yellow","fontSize":"label"} /--><!-- wp:post-title {"level":3,"isLink":true,"fontSize":"h3"} /--><!-- wp:post-excerpt {"textColor":"neutral-3","fontSize":"body-sm"} /--></div><!-- /wp:group --><!-- /wp:post-template --></div><!-- /wp:query --></div><!-- /wp:group -->
