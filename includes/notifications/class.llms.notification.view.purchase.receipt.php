<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Notification View: Purchase Receipt
 * @since    [version]
 * @version  [version]
 * @package  LifterLMS\Views
 * @author   LifterLMS
 */
class LLMS_Notification_View_Purchase_Receipt extends LLMS_Abstract_Notification_View {

	/**
	 * Notification Trigger ID
	 * @var  [type]
	 */
	public $trigger_id = 'purchase_receipt';

	/**
	 * Setup body content for output
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_body() {

		$mailer = LLMS()->mailer();

		$table_style = sprintf(
			'border-collapse:collapse;color:%1$s;font-family:%2$s;font-size:%3$s;Margin-bottom:15px;text-align:left;width:100%%;',
			$mailer->get_css( 'font-color', false ),
			$mailer->get_css( 'font-family', false ),
			$mailer->get_css( 'font-size', false )
		);
		$tr_style = 'color:inherit;font-family:inherit;font-size:inherit;';
		$td_style = sprintf( 'border-bottom:1px solid %s;color:inherit;font-family:inherit;font-size:inherit;padding:10px;', $mailer->get_css( 'divider-color', false ) );


		$rows = array(
			'TRANSACTION_DATE' => __( 'Date', 'lifterlms' ),
			'PRODUCT_TITLE_LINK' => '{{PRODUCT_TYPE}}',
			'PLAN_TITLE' => __( 'Plan', 'lifterlms' ),
			'TRANSACTION_AMOUNT' => __( 'Amount', 'lifterlms' ),
			'TRANSACTION_SOURCE' => __( 'Payment Method', 'lifterlms' ),
			'TRANSACTION_ID' => __( 'Transaction ID', 'lifterlms' ),
		);

		ob_start();
		?><table style="<?php echo $table_style; ?>">
		<?php foreach( $rows as $code => $name ) : ?>
			<tr style="<?php echo $tr_style; ?>">
				<th style="<?php echo $td_style; ?>width:33.3333%;"><?php echo $name; ?></th>
				<td style="<?php echo $td_style; ?>">{{<?php echo $code; ?>}}</td>
			</tr>
		<?php endforeach; ?>
		</table>
		<p><a href="{{ORDER_URL}}"><?php _e( 'View Order Details', 'lifterlms' ); ?></a></p>
		<?php
		return ob_get_clean();

	}

	/**
	 * Setup footer content for output
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_footer() {
		return '';
	}

	/**
	 * Setup notification icon for output
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_icon() {
		return '';
	}

	/**
	 * Setup merge codes that can be used with the notification
	 * @return   array
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_merge_codes() {
		return array(
			'{{ORDER_ID}}' => __( 'Order ID', 'lifterlms' ),
			'{{ORDER_URL}}' => __( 'Order URL', 'lifterlms' ),
			'{{PLAN_TITLE}}' => __( 'Plan Title', 'lifterlms' ),
			'{{PRODUCT_TITLE}}' => __( 'Product Title', 'lifterlms' ),
			'{{PRODUCT_TYPE}}' => __( 'Product Type', 'lifterlms' ),
			'{{PRODUCT_TITLE_LINK}}' => __( 'Product Title (Link)', 'lifterlms' ),
			'{{STUDENT_NAME}}' => __( 'Student Name', 'lifterlms' ),
			'{{TRANSACTION_AMOUNT}}' => __( 'Transaction Amount', 'lifterlms' ),
			'{{TRANSACTION_DATE}}' => __( 'Transaction Date', 'lifterlms' ),
			'{{TRANSACTION_ID}}' => __( 'Transaction ID', 'lifterlms' ),
			'{{TRANSACTION_SOURCE}}' => __( 'Transaction Source', 'lifterlms' ),
		);
	}

	/**
	 * Replace merge codes with actual values
	 * @param    string   $code  the merge code to ge merged data for
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_merge_data( $code ) {

		$transaction = $this->post;
		$order = $transaction->get_order();

		switch ( $code ) {

			case '{{ORDER_ID}}':
				$code = $order->get( 'id' );
			break;

			case '{{ORDER_URL}}':
				$code = esc_url( $order->get_view_link() );
			break;

			case '{{PLAN_TITLE}}':
				$code = $order->get( 'plan_title' );
			break;

			case '{{PRODUCT_TITLE}}':
				$code = $order->get( 'product_title' );
			break;

			case '{{PRODUCT_TITLE_LINK}}':
				$permalink = esc_url( get_permalink( $order->get( 'product_id' ) ) );
				if ( $permalink ) {
					$title = $this->set_merge_data( '{{PRODUCT_TITLE}}' );
					$code = '<a href="' . $permalink . '">' . $title . '</a>';
				}
			break;

			case '{{PRODUCT_TYPE}}':
				$obj = $order->get_product();
				if ( $obj ) {
					$code = $obj->get_post_type_label( 'singular_name' );
				} else {
					$code = _x( 'Item', 'generic product type description', 'lifterlms' );
				}
			break;

			case '{{STUDENT_NAME}}':
				$code = $this->is_for_self() ? 'you' : $this->user->get_name();
			break;

			case '{{TRANSACTION_AMOUNT}}':
				$code = $transaction->get_price( 'amount' );
			break;

			case '{{TRANSACTION_DATE}}':
				$code = $transaction->get_date( 'date', get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
			break;

			case '{{TRANSACTION_ID}}':
				$code = $transaction->get( 'id' );
			break;

			case '{{TRANSACTION_SOURCE}}':
				$code = $transaction->get( 'gateway_source_description' );
			break;

		}

		return $code;

	}

	/**
	 * Setup notification subject for output
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_subject() {
		return sprintf( __( 'Purchase Receipt for %s', 'lifterlms' ), '{{PRODUCT_TITLE}}' );
	}

	/**
	 * Setup notification title for output
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	protected function set_title() {
		return sprintf( __( 'Purchase Receipt for Order #%s', 'lifterlms' ), '{{ORDER_ID}}' );
	}

}
