<?php
/**
 * @package Chunk
 */
?>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<div class="entry-meta">
				<?php if ( ! is_page() ) : ?>
				<div class="date"><a href="<?php the_permalink(); ?>"><?php chunk_date(); ?></a></div>
				<?php endif; ?>
				<?php if ( comments_open() || ( '0' != get_comments_number() && ! comments_open() ) ) : ?>
				<div class="comments"><?php comments_popup_link( __( 'Leave a comment', 'chunk' ), __( '1 Comment', 'chunk' ), __( '% Comments', 'chunk' ) ); ?></div>
				<?php endif; ?>
				<span class="cat-links"><?php the_category( ', ' ); ?></span>
				<span class="entry-format"><a href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'chunk' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a></span>
				<?php edit_post_link( __( 'Edit', 'chunk' ), '<span class="edit-link">', '</span>' ); ?>
			</div>
			<div class="main">
				<?php if ( is_single() ) : ?>
					<?php the_title(); ?>
				<?php else : ?>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'chunk' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<?php endif; ?>
				<div class="entry-content">
					<?php $audio_file = chunk_audio_grabber( get_the_ID() ); ?>
					<?php if ( ! empty( $audio_file ) ) : ?>
						<div class="player">
							<audio controls autobuffer id="audio-player-<?php the_ID(); ?>" src="<?php echo esc_url( $audio_file ); ?>">
								<source src="<?php echo esc_url( $audio_file ); ?>" type="audio/mp3" />
							</audio>
							<script type="text/javascript">
								var audioTag = document.createElement( 'audio' );
								if ( ! ( !! ( audioTag.canPlayType ) && ( "no" != audioTag.canPlayType( "audio/mpeg" ) ) && ( '' != audioTag.canPlayType( 'audio/mpeg' ) ) ) ) {
									AudioPlayer.embed(
										"audio-player-<?php the_ID(); ?>", {
											soundFile: "<?php echo esc_url( $audio_file ); ?>",
											animation: 'no',
											width: '300'
										}
									);
								}
							</script>
						</div>
					<?php endif; ?>
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'chunk' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-link"><span>' . __( 'Pages:', 'chunk' ) . '</span>', 'after' => '</p>' ) ); ?>
				</div>
				<?php the_tags( '<span class="tag-links"><strong>' . __( 'Tagged', 'chunk' ) . '</strong> ', ', ', '</span>' ); ?>
			</div>
		</div>

		<?php comments_template( '', true ); ?>