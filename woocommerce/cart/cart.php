<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">Remove Item</th>
			<th class="product-thumbnail">Image</th>
			<th class="product-mfg">Manufacturer</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
						?>
					</td>
					<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() )
								echo $thumbnail;
							else
								printf( '<a href="%s">%s</a>', $_product->get_permalink( $cart_item ), $thumbnail );
						?>
					</td>
					<td class="mfg">
						<?php
							the_field('mfg', $product_id);
						?>
					</td>
					<td class="product-name">
						<?php
							if ( ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							else
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', $_product->get_permalink( $cart_item ), $_product->get_title() ), $cart_item, $cart_item_key );

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

               				// Backorder notification
               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
						?>
					</td>
					<td class="product-price">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>
					<td class="product-quantity">
						<?php
							$defaults = array(
								'input_name'  	=> 'quantity',
								'input_value' => $cart_item['quantity'],
								'max_value' => apply_filters( 'woocommerce_quantity_input_max', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product ),
								'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $_product ),
								'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $_product ),
							);

							if ( ! empty( $defaults['min_value'] ) )
								$min = $defaults['min_value'];
							else $min = 1;

							if ( ! empty( $defaults['max_value'] ) )
								$max = $defaults['max_value'];
							else $max = 10;

							if ( ! empty( $defaults['step'] ) )
								$step = $defaults['step'];
							else $step = 1;

							?>
							<div class="quantity_select">
								<select name="<?php echo esc_attr( $input_name ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="qty">
								<?php
								for ( $count = $min; $count <= $max; $count = $count+$step ) {
									if ( $count == $defaults['input_value'] )
										$selected = ' selected';
									else $selected = '';
									echo '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';

								}
								?>
								</select>
							</div>
					</td>
					<td class="product-subtotal">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>


			</tbody>
		</table>
		<div class="cart-actions">
			<input type="submit" class="button" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" />
		</div>
				<?php if ( WC()->cart->coupons_enabled() ) { ?>
					<div class="coupon cart-actions">
						<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action('woocommerce_cart_coupon'); ?>

					</div>
				<?php } ?>


				<!-- <input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" /> -->

				<?php //do_action( 'woocommerce_proceed_to_checkout' ); ?>

				<?php //wp_nonce_field( 'woocommerce-cart' ); ?>


		<?php do_action( 'woocommerce_after_cart_contents' ); ?>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>
<div class="cart-collaterals">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

	<?php woocommerce_cart_totals(); ?>

	<?php woocommerce_shipping_calculator(); ?>

</div>
<div class="cart-actions">
	<?php do_action( 'woocommerce_after_cart' ); ?>
</div>
<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
<div class="cart-actions">
		<input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" />
	</div>
	<?php //do_action( 'woocommerce_proceed_to_checkout' ); ?>
	<?php wp_nonce_field( 'woocommerce-cart' ); ?>
</form>