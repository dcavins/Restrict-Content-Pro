<?php
global $user_ID, $rcp_options, $rcp_load_css;

$rcp_load_css = true;

do_action( 'rcp_subscription_details_top' );

$rcp_extension = new CC_RCPBP();

$current_user_plan = rcp_get_subscription_id( $user_id );
$url_plan_args = '';
if ( ! empty( $current_user_plan ) ) {
	$url_plan_args = '?level=' . $current_user_plan;
}

$upgrade_page_url = trailingslashit( bp_loggedin_user_domain() . bp_get_settings_slug() ) . 'subscription-management/upgrade/' . $url_plan_args;

// print_r( rcp_get_subscription_levels( 'active' ) );
?>
<h3>Subscription History</h3>
<?php
if( isset( $_GET['profile'] ) && 'cancelled' == $_GET['profile'] ) : ?>
<p class="rcp_success"><span><?php _e( 'Your profile has been successfully cancelled.', 'rcp' ); ?></span></p>
<?php endif; ?>
<table class="rcp-table" id="rcp-account-overview">
	<thead>
		<tr>
			<th><?php _e( 'Status', 'rcp' ); ?></th>
			<th><?php _e( 'Subscription', 'rcp' ); ?></th>
			<?php if( rcp_is_recurring() && ! rcp_is_expired() ) : ?>
			<th><?php _e( 'Renewal Date', 'rcp' ); ?></th>
			<?php else : ?>
			<th><?php _e( 'Expiration', 'rcp' ); ?></th>
			<?php endif; ?>
			<th><?php _e( 'Actions', 'rcp' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php rcp_print_status(); ?></td>
			<td><?php echo rcp_get_subscription(); ?></td>
			<td><?php echo rcp_get_expiration_date(); ?></td>
			<td>
				<?php
				$action_links = array();
				// Users without an active subscription
				// Active users are not yet expired and have status active or canceled
				if( ( ( ! rcp_is_active( $user_ID ) || rcp_is_expired( $user_ID ) ) || rcp_get_status( $user_ID ) == 'cancelled' ) && rcp_subscription_upgrade_possible( $user_ID ) ) {
					$action_links[] = '<a href="' . $upgrade_page_url . '" title="' . __( 'Renew your subscription', 'rcp' ) . '" class="rcp_sub_details_renew">' . __( 'Subscribe', 'rcp' ) . '</a>';
				}
				// Users who have a current subscription but who could upgrade or become recurring payers.
				// if( ! rcp_is_active( $user_ID ) && rcp_subscription_upgrade_possible( $user_ID ) ) {
				if( rcp_is_active( $user_ID ) && ( ! rcp_is_recurring( $user_ID ) || rcp_subscription_upgrade_possible( $user_ID ) ) ) {
					$action_links[] = '<a href="' . $upgrade_page_url . '" title="' . __( 'Upgrade your subscription', 'rcp' ) . '" class="rcp_sub_details_renew">' . __( 'Upgrade', 'rcp' ) . '</a>';
				}

				if( rcp_is_active( $user_ID ) && rcp_can_member_cancel( $user_ID ) ) {
					$action_links[] = '<a href="' . rcp_get_member_cancel_url( $user_ID ) . '" title="' . __( 'Cancel your subscription', 'rcp' ) . '">' . __( 'Cancel', 'rcp' ) . '</a>';
				}

				echo implode( ' | ', apply_filters( 'rcp_subscription_details_actions', $action_links ) );

				do_action( 'rcp_subscription_details_action_links' );
				?>
			</td>
		</tr>
	</tbody>
</table>
<table class="rcp-table" id="rcp-payment-history">
	<thead>
		<tr>
			<th><?php _e( 'Invoice #', 'rcp' ); ?></th>
			<th><?php _e( 'Subscription', 'rcp' ); ?></th>
			<th><?php _e( 'Payment Method', 'rcp' ); ?></th>
			<th><?php _e( 'Amount', 'rcp' ); ?></th>
			<th><?php _e( 'Date', 'rcp' ); ?></th>
			<th><?php _e( 'Actions', 'rcp' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php if( rcp_get_user_payments() ) : ?>
		<?php foreach( rcp_get_user_payments() as $payment ) : ?>
			<tr>
				<td><?php echo $payment->id; ?></td>
				<td><?php echo $payment->subscription; ?></td>
				<td><?php
					// Payment type
					if ( 'subscr_payment' == $payment->payment_type ) {
						$payment_type = 'Recurring payment';
					} else {
						$payment_type = 'One-time payment';
					}
					echo $payment_type; ?></td>
				<td><?php echo rcp_currency_filter( $payment->amount ); ?></td>
				<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ); ?></td>
				<td><a href="<?php echo rcp_get_pdf_download_url( $payment->id ); ?>"><?php _e( 'PDF Receipt', 'rcp' ); ?></td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr><td colspan="6"><?php _e( 'You have not made any payments.', 'rcp' ); ?></td></tr>
	<?php endif; ?>
	</tbody>
</table>
<?php do_action( 'rcp_subscription_details_bottom' );