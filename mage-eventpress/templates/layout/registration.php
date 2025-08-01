<?php
	/*
	* @Author 		engr.sumonazma@gmail.com
	* Copyright: 	mage-people.com
	*/
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	$event_id = $event_id ?? 0;
	//echo '<pre>';			print_r($event_id);			echo '</pre>';
	$all_dates=$all_dates??[];
	$all_times=$all_times??[];
	$all_dates = is_array($all_dates) && sizeof($all_dates)>0 ?$all_dates: MPWEM_Functions::get_dates( $event_id );
	$all_times = is_array($all_times) && sizeof($all_times)>0 ?$all_times: MPWEM_Functions::get_times( $event_id, $all_dates );
	$date      = $date ?? MPWEM_Functions::get_upcoming_date_time( $event_id, $all_dates, $all_times );
	$url_date='';
	if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'mpwem_date_'.$event_id )) {
		$url_date=isset($_GET['date'])?sanitize_text_field(wp_unslash($_GET['date'])):null;
	}
	$date=$url_date?date( 'Y-m-d H:i', $url_date  ):$date;
	ob_start();
	if ( $event_id > 0 ) {
        ?><div class="mpStyle"><?php
		$reg_status = MP_Global_Function::get_post_info( $event_id, 'mep_reg_status', 'on' );
		//echo '<pre>';			print_r($all_dates);			echo '</pre>';
		if ( $reg_status == 'on' ) {
			if ( sizeof( $all_dates ) > 0 ) {
				$event_member_type = MP_Global_Function::get_post_info( $event_id, 'mep_member_only_event', 'for_all' );
				$saved_user_role   = MP_Global_Function::get_post_info( $event_id, 'mep_member_only_user_role', [] );
				if ( $event_member_type == 'for_all' || ( is_user_logged_in() && ( array_intersect( wp_get_current_user()->roles, $saved_user_role ) || in_array( 'all', $saved_user_role ) ) ) ) {
					//$full_location = MPWEM_Functions::get_location( $event_id );
					?>
                    <div class="mpwem_registration_area">
                        <!-- <div class="section-title"><?php //esc_html_e( 'Tickets and prices', 'mage-eventpress' ); ?></div> -->
						<?php do_action( 'mpwem_date_select', $event_id, $all_dates, $all_times, $date ); ?>
                        <form action="" method='post' id="mpwem_registration" enctype="multipart/form-data">
							<?php do_action( 'mpwem_registration_content', $event_id, $all_dates, $all_times, $date ); ?>
                        </form>
						<?php do_action( 'mpwem_hidden_content', $event_id ); ?>
                    </div>
					<?php
				}
			} else {
				MPWEM_Layout::msg( esc_html__( 'Sorry, this event is expired and no longer available', 'mage-eventpress' ) );
			}
		} else {
			// MPWEM_Layout::msg( esc_html__( 'Sorry, this event is  no longer available', 'mage-eventpress' ) );
		}
		?></div><?php
	}
	echo ob_get_clean();