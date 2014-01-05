<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with widgets. Arranged in three per row.
	 */
	get_sidebar( 'footer' );
?>

	<div id="footer">
		<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'chunk' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'chunk' ), 'WordPress' ); ?></a>
		<span class="sep"> | </span>
		<?php printf( __( 'Theme: %1$s by %2$s.', 'chunk' ), 'Chunk', '<a href="http://automattic.com/" rel="designer">Automattic</a>' ); ?>
	</div>

</div>

<?php wp_footer(); ?>

</body>
</html>