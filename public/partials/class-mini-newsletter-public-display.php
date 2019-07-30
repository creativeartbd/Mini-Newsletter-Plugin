<?php 
//  Provide a public-facing view for the plugin

class Mini_Newsletter_Public_Display {

	public function mn_newsletter_form ( $title, $placeholder ) {
        ?>
        <div class="single-widget widget-newsletter">
            <h5 class="widget-title">
                <?php echo esc_html ( $title ); ?>
            </h5>
            <div id="mn_form_result"></div>
            <form action="#" id="submit_newsletter">
                <input type="text" name="mn_email" id="mn_email" placeholder="<?php echo esc_attr( $placeholder ); ?>">
                <button type="submit" id="mn_submit">
                    <i class="icofont icofont-paper-plane"></i>
                </button>
                <?php wp_nonce_field( 'mn_action', 'mn_nonce_field' ) ?>
            </form>
        </div> 
        <?php
    }
}