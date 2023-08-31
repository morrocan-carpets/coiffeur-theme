<?php
	global $bookingpress_ajaxurl, $BookingPressPro, $bookingpress_common_date_format;
	$bpa_edit_daysoffs     = $BookingPressPro->bookingpress_check_capability( 'bookingpress_edit_daysoffs' );
	$bpa_edit_special_days = $BookingPressPro->bookingpress_check_capability( 'bookingpress_edit_special_days' );
?>

<el-main class="bpa-main-listing-card-container bpa-timesheet-card-container bpa--is-page-non-scrollable-mob" :class="(bookingpress_staff_customize_view == 1 ) ? 'bpa-main-list-card__is-staff-custom-view':''" id="all-page-main-container">
	<div class="bpa-default-card bpa-timesheet--workinghours">
		<el-row type="flex" class="bpa-mlc-head-wrap">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="bpa-mlc-left-heading">
				<h1 class="bpa-page-heading"><?php esc_html_e( 'Working Hours', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
		</el-row>
		<div class="bpa-back-loader-container" id="bpa-page-loading-loader">
			<div class="bpa-back-loader"></div>
		</div>
		<div class="bpa-tc--content bpa-twh--body-content" id="bpa-main-container">
			<div class="bpa-wh__items">
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Monday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="monday_timings.workhours_start_time != 'Off' && monday_timings.workhours_end_time != 'Off'">{{ monday_timings.workhours_start_time+' - '+monday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="monday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in monday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Tuesday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="tuesday_timings.workhours_start_time != 'Off' && tuesday_timings.workhours_end_time != 'Off'">{{ tuesday_timings.workhours_start_time+' - '+tuesday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="tuesday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in tuesday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Wednesday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="wednesday_timings.workhours_start_time != 'Off' && wednesday_timings.workhours_end_time != 'Off'">{{ wednesday_timings.workhours_start_time+' - '+wednesday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="wednesday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in wednesday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Thursday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="thursday_timings.workhours_start_time != 'Off' && thursday_timings.workhours_end_time != 'Off'">{{ thursday_timings.workhours_start_time+' - '+thursday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="thursday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in thursday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Friday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="friday_timings.workhours_start_time != 'Off' && friday_timings.workhours_end_time != 'Off'">{{ friday_timings.workhours_start_time+' - '+friday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="friday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in friday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Saturday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="saturday_timings.workhours_start_time != 'Off' && saturday_timings.workhours_end_time != 'Off'">{{ saturday_timings.workhours_start_time+' - '+saturday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="saturday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in saturday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
				<div class="bpa-wh__item">
					<h5><?php esc_html_e( 'Sunday', 'bookingpress-appointment-booking' ); ?></h5>
					<div class="bpa-wh__item-body">
						<p v-if="sunday_timings.workhours_start_time != 'Off' && sunday_timings.workhours_end_time != 'Off'">{{ sunday_timings.workhours_start_time+' - '+sunday_timings.workhours_end_time }}</p>
						<p v-else><?php esc_html_e('Off','bookingpress-appointment-booking'); ?></p>
					</div>
					<div class="bpa-wh__item-breaks" v-if="sunday_timings.break_times.length > 0">
						<h5><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h5>
						<p v-for="break_time_data in sunday_timings.break_times">{{ break_time_data.start_time }} to {{ break_time_data.end_time }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="bpa-default-card bpa-timesheet--daysoff">
		<el-row type="flex" class="bpa-mlc-head-wrap">
			<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12" class="bpa-mlc-left-heading">
				<h1 class="bpa-page-heading"><?php esc_html_e( 'Holiday', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
			<?php if ( $bpa_edit_daysoffs ) { ?>
			<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
				<div class="bpa-hw-right-btn-group">
					<el-button class="bpa-btn" @click="open_days_off_modal_func(event)">
						<span class="material-icons-round">add</span>
						<?php esc_html_e( 'Add New', 'bookingpress-appointment-booking' ); ?>
					</el-button>
				</div>
			</el-col>
			<?php } ?>
		</el-row>
		<div class="bpa-tc--content bpa-sm__days-off-card">
			<div class="bpa-grid-list-container bpa-sm__doc-body">                
				<el-row type="flex" v-if="bookingpress_staffmembers_daysoff_details.length == 0 && bookingpress_staffmember_default_daysoff_details.length == 0">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<div class="bpa-data-empty-view">
							<div class="bpa-ev-left-vector">
								<picture>
									<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
									<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
								</picture>
							</div>				
							<div class="bpa-ev-right-content">					
								<h4><?php esc_html_e( 'No Holiday Available', 'bookingpress-appointment-booking' ); ?></h4>
							</div>				
						</div>
					</el-col>
				</el-row>
				<div class="bpa-ts-days-off__company-holiday-wrap" v-if="bookingpress_staffmember_default_daysoff_details.length != 0">
					<el-row>
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
							<h2 class="bpa-sec--sub-heading"><?php esc_html_e( 'Company Holiday', 'bookingpress-appointment-booking' ); ?></h2>
						</el-col>
					</el-row>
					<el-row class="bpa-assigned-service-body">                    
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
							<div class="bpa-card bpa-card__heading-row">
								<el-row type="flex">
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Date', 'bookingpress-appointment-booking' ); ?></h4>
										</div>
									</el-col>
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Holiday Name', 'bookingpress-appointment-booking' ); ?></h4>
										</div>
									</el-col>
									<el-col :xs="24" :sm="4" :md="2" :lg="4" :xl="4">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"></h4>
										</div>
									</el-col>
								</el-row>
							</div>
						</el-col>
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="default_day_off in bookingpress_staffmember_default_daysoff_details">
							<div class="bpa-card bpa-card__body-row list-group-item">
								<el-row type="flex">
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item --bpa-sm-is-legends-item">
											<span v-if="default_day_off.bookingpress_repeat == 0"></span>
											<span class="--bpa-is-legend-yearly" v-else></span>
											<h4 class="bpa-card__item__heading is--body-heading">{{ default_day_off.bookingpress_dayoff_date}}</h4>	
										</div>									
									</el-col>
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading is--body-heading">{{ default_day_off.bookingpress_name }}</h4>
										</div>
									</el-col>      
									<el-col :xs="24" :sm="4" :md="4" :lg="4" :xl="4">                          
									</el-col>	
								</el-row>
							</div>						
						</el-col>
					</el-row>
					<el-row>
						<div class="bpa-dc__staff--legends-area">
							<div class="bpa-la__item">
								<p><span></span><?php esc_html_e( 'Once Off', 'bookingpress-appointment-booking' ); ?></p>
							</div>
							<div class="bpa-la__item">
								<p><span></span><?php esc_html_e( 'Yearly', 'bookingpress-appointment-booking' ); ?></p>
							</div>
						</div>  
					</el-row>  
				</div>
				<div v-if="bookingpress_staffmembers_daysoff_details.length != 0">
					<el-row  v-if="bookingpress_staffmember_default_daysoff_details.length != 0">
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
							<h2 class="bpa-sec--sub-heading"><?php esc_html_e( 'Holiday', 'bookingpress-appointment-booking' ); ?></h2>
						</el-col>
					</el-row>
					<el-row class="bpa-assigned-service-body">
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
							<div class="bpa-card bpa-card__heading-row">
								<el-row type="flex">
									<el-col :xs="10" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Date', 'bookingpress-appointment-booking' ); ?></h4>
										</div>
									</el-col>
									<el-col :xs="10" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Holiday Name', 'bookingpress-appointment-booking' ); ?></h4>
										</div>
									</el-col>									
									<el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
									<?php if ( $bpa_edit_daysoffs ) { ?>
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Action', 'bookingpress-appointment-booking' ); ?></h4>
										</div>
										<?php } ?>
									</el-col>
								</el-row>
							</div>
						</el-col>
						<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="staffmember_day_off in bookingpress_staffmembers_daysoff_details">
							<div class="bpa-card bpa-card__body-row list-group-item">
								<el-row type="flex">
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item --bpa-sm-is-legends-item">
											<span v-if="staffmember_day_off.bookingpress_staffmember_daysoff_repeat == 0"></span>
											<span class="--bpa-is-legend-yearly" v-else></span>
											<h4 class="bpa-card__item__heading is--body-heading">{{ staffmember_day_off.bookingpress_staffmember_daysoff_formated_date}}</h4>	
										</div>
									</el-col>
									<el-col :xs="24" :sm="10" :md="10" :lg="10" :xl="10">
										<div class="bpa-card__item">
											<h4 class="bpa-card__item__heading is--body-heading">{{ staffmember_day_off.bookingpress_staffmember_daysoff_name }}</h4>
										</div>
									</el-col>
									<el-col :xs="24" :sm="4" :md="4" :lg="4" :xl="4">
										<?php if ( $bpa_edit_daysoffs ) { ?>
										<div>
											<el-tooltip effect="dark" content="" placement="top" open-delay="300">
												<div slot="content">
													<span><?php esc_html_e( 'Edit', 'bookingpress-appointment-booking' ); ?></span>
												</div>
												<el-button class="bpa-btn bpa-btn--icon-without-box" @click="bookingpress_edit_daysoff(staffmember_day_off, event)">
													<span class="material-icons-round">mode_edit</span>
												</el-button>
											</el-tooltip>
											<el-tooltip effect="dark" content="" placement="top" open-delay="300">
												<div slot="content">
													<span><?php esc_html_e( 'Delete', 'bookingpress-appointment-booking' ); ?></span>
												</div>
												<el-popconfirm
													cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>' 
													confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-appointment-booking' ); ?>' 
													icon="false" 
													title="<?php esc_html_e( 'Are you sure you want to delete holiday?', 'bookingpress-appointment-booking' ); ?>" 
													@confirm="bookingpress_delete_daysoff(staffmember_day_off.bookingpress_staffmember_daysoff_id)" 
													confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
													cancel-button-type="bpa-btn bpa-btn__small">

													<el-button slot="reference" type="text" class="bpa-btn bpa-btn--icon-without-box __danger">
														<span class="material-icons-round">delete</span>
													</el-button>

												</el-popconfirm>    
											</el-tooltip>
										</div>
										<?php } ?>
									</el-col>
								</el-row>
							</div>					
						</el-col>
					</el-row>
					<el-row>
						<div class="bpa-dc__staff--legends-area">
							<div class="bpa-la__item">
								<p><span></span><?php esc_html_e( 'Once Off', 'bookingpress-appointment-booking' ); ?></p>
							</div>
							<div class="bpa-la__item">
								<p><span></span><?php esc_html_e( 'Yearly', 'bookingpress-appointment-booking' ); ?></p>
							</div>
						</div>
					</el-row>
				</div>	
			</div>
		</div>
	</div>  
	<div class="bpa-default-card bpa-timesheet--specialdays">
		<el-row type="flex" class="bpa-mlc-head-wrap">
			<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12" class="bpa-mlc-left-heading">
				<h1 class="bpa-page-heading"><?php esc_html_e( 'Special Days', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
			<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
				<?php if ( $bpa_edit_special_days ) { ?>
				<div class="bpa-hw-right-btn-group">
					<el-button class="bpa-btn" @click="open_special_days_func(event)">
						<span class="material-icons-round">add</span>
						<?php esc_html_e( 'Add New', 'bookingpress-appointment-booking' ); ?>
					</el-button>
				</div>
				<?php } ?>
			</el-col>
		</el-row>
		<div class="bpa-tc--content bpa-sm__special-days-card">
			<div class="bpa-grid-list-container bpa-sm__doc-body">
				<el-row type="flex" v-if="bookingpress_staffmembers_specialdays_details.length == 0">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<div class="bpa-data-empty-view">
							<div class="bpa-ev-left-vector">
								<picture>
									<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
									<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
								</picture>
							</div>				
							<div class="bpa-ev-right-content">					
								<h4><?php esc_html_e( 'No Special Days Available', 'bookingpress-appointment-booking' ); ?></h4>
							</div>				
						</div>
					</el-col>
				</el-row>
				<el-row class="bpa-assigned-service-body" v-if="bookingpress_staffmembers_specialdays_details.length > 0">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<div class="bpa-card bpa-card__heading-row">
							<el-row type="flex">
								<el-col :xs="8" :sm="8" :md="8" :lg="8" :xl="8">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Date', 'bookingpress-appointment-booking' ); ?></h4>
									</div>
								</el-col>
								<el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Workhours', 'bookingpress-appointment-booking' ); ?></h4>
									</div>
								</el-col>
								<el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Breaks', 'bookingpress-appointment-booking' ); ?></h4>
									</div>
								</el-col>
								<el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
									<?php if ( $bpa_edit_special_days ) { ?>
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Action', 'bookingpress-appointment-booking' ); ?></h4>
									</div>
									<?php } ?>
								</el-col>
							</el-row>
						</div>
					</el-col>

					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="staffmember_special_day in bookingpress_staffmembers_specialdays_details">
						<div class="bpa-card bpa-card__body-row list-group-item">
							<el-row type="flex">
								<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
									<div class="bpa-card__item">
									<h4 class="bpa-card__item__heading is--body-heading">{{ staffmember_special_day.special_day_formatted_start_date }} - {{ staffmember_special_day.special_day_formatted_end_date }}</h4>
									</div>
								</el-col>
								<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="6">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading is--body-heading">( {{staffmember_special_day.formatted_start_time}} - {{staffmember_special_day.formatted_end_time}} )</h4>
									</div>
								</el-col>
								<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="6">
									<div class="bpa-card__item">
										<span v-if="staffmember_special_day.special_day_workhour != undefined && staffmember_special_day.special_day_workhour != ''">	
											<h4 class="bpa-card__item__heading is--body-heading" v-for="special_day_workhours in staffmember_special_day.special_day_workhour" v-if="special_day_workhours.formatted_start_time != undefined && special_day_workhours.formatted_start_time != '' && special_day_workhours.formatted_end_time != undefined && special_day_workhours.formatted_end_time != '' && special_day_workhours.start_time != '' && special_day_workhours.end_time != ''"> 
											( {{ special_day_workhours.formatted_start_time }} - {{special_day_workhours.formatted_end_time}} )
											</h4>
										</span>	
										<span v-else>-</span>	
									</div>
								</el-col>
								<el-col :xs="24" :sm="4" :md="4" :lg="4" :xl="4">
									<?php if ( $bpa_edit_special_days ) { ?>
									<div>
										<el-tooltip effect="dark" content="" placement="top" open-delay="300">
											<div slot="content">
												<span><?php esc_html_e( 'Edit', 'bookingpress-appointment-booking' ); ?></span>
											</div>
											<el-button class="bpa-btn bpa-btn--icon-without-box" @click="show_edit_special_day_div(staffmember_special_day.id, event)">
												<span class="material-icons-round">mode_edit</span>
											</el-button>
										</el-tooltip>										
										<el-tooltip effect="dark" content="" placement="top" open-delay="300">
											<div slot="content">
												<span><?php esc_html_e( 'Delete', 'bookingpress-appointment-booking' ); ?></span>
											</div>
											<el-popconfirm
												cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>' 
												confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-appointment-booking' ); ?>' 
												icon="false" 
												title="<?php esc_html_e( 'Are you sure you want to delete this Special days?', 'bookingpress-appointment-booking' ); ?>" 
												@confirm="bookingpress_delete_special_daysoff(staffmember_special_day.id)" 
												confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
												cancel-button-type="bpa-btn bpa-btn__small">
												<el-button slot="reference" type="text" class="bpa-btn bpa-btn--icon-without-box __danger">
													<span class="material-icons-round">delete</span>
												</el-button>
											</el-popconfirm>    
										</el-tooltip>
									</div>
									<?php } ?>
								</el-col>
							</el-row>
						</div>
					</el-col>
				</el-row>
			</div>
		</div>
	</div>
</el-main> 

<el-dialog id="days_off_add_modal" custom-class="bpa-dialog bpa-dailog__small bpa-dialog--days-off" title="" :visible.sync="days_off_add_modal" :close-on-press-escape="close_modal_on_esc" :modal="is_mask_display" @open="bookingpress_enable_modal" @close="bookingpress_disable_modal">
	<div class="bpa-dialog-heading">
		<el-row type="flex">
			<el-col :xs="12" :sm="12" :md="16" :lg="16" :xl="16">
				<h1 class="bpa-page-heading" v-if="edit_staffmember_dayoff == ''"><?php esc_html_e( 'Add Holiday', 'bookingpress-appointment-booking' ); ?></h1>
				<h1 class="bpa-page-heading" v-else><?php esc_html_e( 'Edit Holiday', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
		</el-row>
	</div>
	<div class="bpa-dialog-body">
		<el-container class="bpa-grid-list-container bpa-add-categpry-container">
			<div class="bpa-form-row">
				<el-row>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<el-form ref="staffmember_dayoff_form" :rules="rules_dayoff" :model="staffmember_dayoff_form" label-position="top">
							<el-row>							
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<el-form-item prop="dayoff_date">
										<template #label>
											<span class="bpa-form-label"><?php esc_html_e( 'Date:', 'bookingpress-appointment-booking' ); ?></span>
										</template>															
										<el-date-picker class="bpa-form-control bpa-form-control--date-picker" v-model="staffmember_dayoff_form.dayoff_date" type="date" format="<?php echo esc_html( $bookingpress_common_date_format ); ?>" placeholder="<?php esc_html_e( 'Select Date', 'bookingpress-appointment-booking' ); ?>" :picker-options="pickerOptions" @change="change_days_off_date($event)" > </el-date-picker>
									</el-form-item>
								</el-col>
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">									
									<el-form-item prop="dayoff_name">
										<template #label>
											<span class="bpa-form-label"><?php esc_html_e( 'Holiday Name:', 'bookingpress-appointment-booking' ); ?></span>
										</template>
										<el-input class="bpa-form-control" v-model="staffmember_dayoff_form.dayoff_name" id="dayoff_name" name="dayoff_name" placeholder="<?php esc_html_e( 'Enter holiday name', 'bookingpress-appointment-booking' ); ?>"></el-input>
									</el-form-item>
								</el-col>
							</el-row>
							<el-row class="bpa-do__repeat-yearly">
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<el-form-item>
										<el-checkbox v-model="staffmember_dayoff_form.dayoff_repeat"><span class="bpa-form-label"><?php esc_html_e( 'Repeat Yearly', 'bookingpress-appointment-booking' ); ?></span></el-checkbox>
									</el-form-item>
								</el-col>
							</el-row>											
						</el-form>
					</el-col>
				</el-row>
			</div>
		</el-container>
	</div>
	<div class="bpa-dialog-footer">
		<div class="bpa-hw-right-btn-group">
			<el-button class="bpa-btn bpa-btn__small" @click="closeStaffmemberDayoff"><?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?></el-button>
			<el-button class="bpa-btn bpa-btn__small bpa-btn--primary" @click="bookingpress_add_daysoff('staffmember_dayoff_form')" :disabled="staffmember_dayoff_form.is_disabled"><?php esc_html_e( 'Save', 'bookingpress-appointment-booking' ); ?></el-button>
		</div>
	</div>
</el-dialog>

<el-dialog id="special_days_add_modal" custom-class="bpa-dialog bpa-dailog__small bpa-dialog--special-days" title="" :visible.sync="special_days_add_modal" :close-on-press-escape="close_modal_on_esc" :modal="is_mask_display" @open="bookingpress_enable_modal" @close="bookingpress_disable_modal">
	<div class="bpa-dialog-heading">
		<el-row type="flex">
			<el-col :xs="12" :sm="12" :md="16" :lg="16" :xl="16">
				<h1 class="bpa-page-heading" v-if="edit_staffmember_special_day == ''"><?php esc_html_e( 'Add Special Days', 'bookingpress-appointment-booking' ); ?></h1>
				<h1 class="bpa-page-heading" v-else><?php esc_html_e( 'Edit Special Days', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
		</el-row>
	</div>
	<div class="bpa-dialog-body">
		<el-container class="bpa-grid-list-container bpa-add-categpry-container">
			<div class="bpa-form-row">
				<el-row>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<el-form ref="staffmember_special_day_form" :rules="rules_special_day" :model="staffmember_special_day_form" label-position="top">
							<el-row>
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<el-form-item prop="special_day_date">
										<template #label>
											<span class="bpa-form-label"><?php esc_html_e( 'Date:', 'bookingpress-appointment-booking' ); ?></span>
										</template>
										<el-date-picker class="bpa-form-control bpa-form-control--date-range-picker" v-model="staffmember_special_day_form.special_day_date" type="daterange" format="<?php echo esc_html( $bookingpress_common_date_format ); ?>" :picker-options="disablePastDates" placeholder="<?php esc_html_e( 'Select Date', 'bookingpress-appointment-booking' ); ?>" @change="change_special_day_date($event)" range-separator="<?php esc_html_e( 'To', 'bookingpress-appointment-booking' ); ?>" :popper-append-to-body="false" start-placeholder="<?php esc_html_e( 'Start date', 'bookingpress-appointment-booking' ); ?>" end-placeholder="<?php esc_html_e( 'End date', 'bookingpress-appointment-booking' ); ?>">
										</el-date-picker>
									</el-form-item>
								</el-col>
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<el-form-item prop="special_day_service">
										<template #label>
											<span class="bpa-form-label"><?php esc_html_e( 'Select Service', 'bookingpress-appointment-booking' ); ?></span>
										</template>
										<el-select class="bpa-form-control"  v-model="staffmember_special_day_form.special_day_service" name="special_day_service" multiple filterable collapse-tags  placeholder="<?php esc_html_e( 'Applied for all assigned services', 'bookingpress-appointment-booking' ); ?>"
											popper-class="bpa-el-select--is-with-modal">
											<el-option-group v-for="service_cat_data in bookingpress_services_list" :key="service_cat_data.category_name" :label="service_cat_data.category_name">
												<template v-if="service_data.service_id == 0" v-for="service_data in service_cat_data.category_services">
													<el-option :key="service_data.service_id" :label="service_data.service_name" :value="''" ></el-option>
												</template>
												<template v-else>
													<el-option :key="service_data.service_id" :label="service_data.service_name+' ('+service_data.service_price+' )'" :value="service_data.service_id"></el-option>
												</template>
											</el-option-group>
										</el-select>
									</el-form-item>
								</el-col>												
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<el-row type="flex" class="bpa-sd__time-selection">
										<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
											<el-form-item prop="start_time">												
												<template #label>
													<span class="bpa-form-label"><?php esc_html_e( 'Select Time', 'bookingpress-appointment-booking' ); ?></span>
												</template>
												<el-select  v-model="staffmember_special_day_form.start_time" name="start_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'Start Time', 'bookingpress-appointment-booking' ); ?>"> 
													<span slot="prefix" class="material-icons-round">access_time</span>
													<el-option v-for="work_timings in specialday_hour_list"  :label="work_timings.formatted_start_time" :value="work_timings.start_time" ></el-option >
												</el-select>
											</el-form-item>	
										</el-col>
										<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
											<el-form-item prop="end_time">
												<el-select v-model="staffmember_special_day_form.end_time" name="end_time" class="bpa-form-control bpa-form-control__left-icon"	placeholder="<?php esc_html_e( 'End Time', 'bookingpress-appointment-booking' ); ?>">
													<span slot="prefix" class="material-icons-round">access_time</span>
													<el-option v-for="work_timings in specialday_hour_list" :label="work_timings.formatted_end_time" :value="work_timings.end_time" v-if="
													work_timings.end_time > staffmember_special_day_form.start_time">
													</el-option>
												</el-select>
											</el-form-item>
										</el-col>
									</el-row>
								</el-col>
							</el-row>
							<el-row>
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<div class="bpa-sd__add-period-btn">
										<el-link class="bpa-sd__add-period-btn-link" @click="bookingpress_add_special_day_period()">
											<span class="material-icons-round">add_circle</span>
											<?php esc_html_e( 'Add Break', 'bookingpress-appointment-booking' ); ?>
										</el-link>
									</div>
								</el-col>
							</el-row>
							<el-row class="bpa-sd--add-period-row" v-for="special_day_workhours in staffmember_special_day_form.special_day_workhour">
								<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
									<div class="bpa-ts__item">
										<div class="bpa-ts__item-left">
											<el-row type="flex" class="bpa-sd__time-selection">
												<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
													<el-form-item>											
														<template #label>
															<span class="bpa-form-label"><?php esc_html_e( 'Select Time', 'bookingpress-appointment-booking' ); ?></span>
														</template>
														<el-select v-model="special_day_workhours.start_time" name ="start_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'Start Time', 'bookingpress-appointment-booking' ); ?>"> 
															<span slot="prefix" class="material-icons-round">access_time</span>
															<el-option v-for="work_timings in specialday_break_hour_list" :label="work_timings.formatted_start_time" :value="work_timings.start_time" v-if="work_timings.start_time > staffmember_special_day_form.start_time && work_timings.start_time < staffmember_special_day_form.end_time"></el-option >
														</el-select>
													</el-form-item>	
												</el-col>
												<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
													<el-form-item>
														<el-select v-model="special_day_workhours.end_time" name ="end_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'End Time', 'bookingpress-appointment-booking' ); ?>">
															<span slot="prefix" class="material-icons-round">access_time</span>
															<el-option v-for="work_timings in specialday_break_hour_list" :label="work_timings.formatted_start_time" :value="work_timings.start_time" v-if="((work_timings.start_time > staffmember_special_day_form.start_time && work_timings.start_time < staffmember_special_day_form.end_time) && (work_timings.start_time > special_day_workhours.start_time))">
															</el-option>
														</el-select>
													</el-form-item>
												</el-col>
											</el-row>
										</div>
										<div class="bpa-ts__item-right">
											<div class="bpa-sd__add-period-btn">
												<el-link class="bpa-sd__add-period-btn-link"  @click="bookingpress_remove_special_day_period(special_day_workhours.id)">
													<span class="material-icons-round">remove_circle</span>
												</el-link>
											</div>
										</div>
									</div>
								</el-col>
							</el-row>
						</el-form>
					</el-col>
				</el-row>
			</div>
		</el-container>
	</div>
	<div class="bpa-dialog-footer">
		<div class="bpa-hw-right-btn-group">
			<el-button class="bpa-btn bpa-btn__small bpa-btn--primary" @click="addStaffmemberSpecialday('staffmember_special_day_form')" :disabled="staffmember_special_day_form.is_disabled"><?php esc_html_e( 'Save', 'bookingpress-appointment-booking' ); ?></el-button>
			<el-button class="bpa-btn bpa-btn__small" @click="close_special_days_func"><?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?></el-button>
		</div>
	</div>
</el-dialog>