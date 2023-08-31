<el-main class="bpa-main-listing-card-container bpa-default-card" id="all-page-main-container">
	<?php if(current_user_can('administrator'))  { ?>
	<div class="bpa-unlicense-warning-belt" v-if="typeof is_licence_activated != 'undefined' && is_licence_activated != ''">
		<p><span class="material-icons-round">info</span><?php esc_html_e('It seems that your BookingPress License is not active, to activate the License, click','bookingpress-appointment-booking'); ?> <a href="#"><?php esc_html_e('here','bookingpress-appointment-booking'); ?></a>.</p>		
	</div>	
	<?php } ?>
	<el-row type="flex" class="bpa-mlc-head-wrap">
		<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12" class="bpa-mlc-left-heading">
			<h1 class="bpa-page-heading"><?php esc_html_e( 'Locations', 'bookingpress-appointment-booking' ); ?></h1>
		</el-col>
		<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
			<div class="bpa-hw-right-btn-group">
				<el-button class="bpa-btn bpa-btn--primary" @click="open_location_modal()"> 
					<span class="material-icons-round">add</span> 
					<?php esc_html_e( 'Add New', 'bookingpress-appointment-booking' ); ?>
				</el-button>
				<el-button class="bpa-btn" @click="openNeedHelper">
					<span class="material-icons-round">help</span>
						<?php esc_html_e( 'Need help?', 'bookingpress-appointment-booking' ); ?>
				</el-button>				
				<el-button class="bpa-btn" @click="open_feature_request_url">
					<span class="material-icons-round">lightbulb</span>
					<?php esc_html_e( 'Feature Requests', 'bookingpress-appointment-booking' ); ?>
				</el-button>
			</div>
		</el-col>
	</el-row>
	<div class="bpa-back-loader-container" id="bpa-page-loading-loader">
		<div class="bpa-back-loader"></div>
	</div>
	<div id="bpa-main-container">	
		<el-row type="flex" v-if="items.length == 0">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<div class="bpa-data-empty-view">
					<div class="bpa-ev-left-vector">
						<picture>
							<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
							<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
						</picture>
					</div>
					<div class="bpa-ev-right-content">
						<h4><?php esc_html_e( 'No Record Found!', 'bookingpress-appointment-booking' ); ?></h4>
						
						<el-button class="bpa-btn bpa-btn--primary bpa-btn__medium" @click="open_location_modal()"> 
							<span class="material-icons-round">add</span> 
							<?php esc_html_e( 'Add New', 'bookingpress-appointment-booking' ); ?>
						</el-button>
					</div>
				</div>
			</el-col>
		</el-row>
		<el-row v-if="items.length > 0">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<el-container class="bpa-table-container">
					<div class="bpa-back-loader-container" v-if="is_display_loader == '1'">
						<div class="bpa-back-loader">
							<svg viewBox="0 0 61 90">
								<path d="M50.334 33.0584C50.334 33.0584 53.6673 29.7642 53.6673 19.8819C53.6673 3.41131 43.6673 0.117188 30.334 0.117188H0.333984V69.2937H20.334V89.0584H30.334V69.2937C41.8895 69.2937 50.0006 69.2937 56.0006 61.9368C58.8895 58.4231 60.334 53.9211 60.334 49.3094C60.334 46.8937 60.0006 44.478 59.4451 42.9407C58.0006 39.2074 57.0006 36.3525 50.334 33.0584ZM40.334 59.4113C37.0006 59.4113 30.334 59.4113 30.334 59.4113C30.334 59.4113 30.334 56.1172 30.334 49.529C30.334 44.0388 34.7784 39.6466 40.334 39.6466C45.6673 39.6466 50.334 43.929 50.334 49.529C50.334 55.3486 45.4451 59.4113 40.334 59.4113ZM32.2229 32.1799C30.5562 32.9486 28.8895 33.9368 27.334 35.0348C23.6673 37.7799 20.334 41.7329 20.334 49.529V59.4113H10.334V9.99954H30.334C38.334 9.99954 43.6673 13.2937 43.6673 19.8819C43.6673 25.9211 39.1118 28.776 32.2229 32.1799Z"/>
							</svg>
						</div>
					</div>
					<el-table ref="multipleTable" :data="items" @selection-change="handleSelectionChange" style="width: 100%">
						<el-table-column  type="selection"></el-table-column>															
						<el-table-column  prop="location_name" label="<?php esc_html_e( 'Location', 'bookingpress-appointment-booking' ); ?>" sortable></el-table-column>
						<el-table-column  prop="location_map" label="<?php esc_html_e( 'Map', 'bookingpress-appointment-booking' ); ?>">	
							<template slot-scope="scope">
							<el-image v-bind:src="scope.row.location_map+'&key='+bookingpress_google_map_api_key">
								<div slot="error" class="image-slot">
									<i class="el-icon-picture-outline"></i>
								</div>
							</el-image>
								<div class="bpa-table-actions-wrap">
									<div class="bpa-table-actions">
										<el-button class="bpa-btn bpa-btn--icon-without-box" @click.native.prevent="editLocationDetails(scope.$index, scope.row)">
											<span class="material-icons-round">mode_edit</span>
										</el-button>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-appointment-booking' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to delete?', 'bookingpress-appointment-booking' ); ?>" 
											@confirm="deleteLocation(scope.$index, scope.row)" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small">
											<el-button type="text" slot="reference" class="bpa-btn bpa-btn--icon-without-box __danger">
												<span class="material-icons-round">delete</span>
											</el-button>
										</el-popconfirm>
									</div>
								</div>
							</template>						
						</el-table-column>
					-->
					</el-table>				
				</el-container>
			</el-col>
		</el-row>
		<el-row class="bpa-pagination" type="flex" v-if="items.length > 0"> <!-- Pagination -->
			<el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" >
				<div class="bpa-pagination-left">
					<p><?php esc_html_e( 'Displaying', 'bookingpress-appointment-booking' ); ?> <strong><u>{{ items.length }}</u></strong><p><?php esc_html_e( 'out of', 'bookingpress-appointment-booking' ); ?></p><strong>{{ totalItems }}</strong></p>
					<div class="bpa-pagination-per-page">
						<p><?php esc_html_e( 'Displaying Per Page', 'bookingpress-appointment-booking' ); ?></p>
						<el-select v-model="pagination_length_val" placeholder="Select" @change="changePaginationSize($event)" class="bpa-form-control" popper-class="bpa-pagination-dropdown">
							<el-option v-for="item in pagination_val" :key="item.text" :label="item.text" :value="item.value"></el-option>
						</el-select>
					</div>
				</div>
			</el-col>
			<el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" class="bpa-pagination-nav">
				<el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="currentPage" layout="prev, pager, next" :total="totalItems" :page-sizes="pagination_length" :page-size="perPage"></el-pagination>
			</el-col>
			<el-container v-if="multipleSelection.length > 0" class="bpa-default-card bpa-bulk-actions-card">
				<el-button class="bpa-btn bpa-btn--icon-without-box bpa-bac__close-icon" @click="closeBulkAction">
					<span class="material-icons-round">close</span>
				</el-button>
				<el-row type="flex" class="bpa-bac__wrapper">
					<el-col class="bpa-bac__left-area" :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
						<span class="material-icons-round">check_circle</span>
						<p>{{ multipleSelection.length }}<?php esc_html_e( ' Items Selected', 'bookingpress-appointment-booking' ); ?></p>
					</el-col>
					<el-col class="bpa-bac__right-area" :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
						<el-select class="bpa-form-control" v-model="bulk_action" placeholder="<?php esc_html_e( 'Select', 'bookingpress-appointment-booking' ); ?>">
							<el-option v-for="item in bulk_options" :key="item.value" :label="item.label" :value="item.value"></el-option>
						</el-select>
						<el-button @click="bulk_actions" class="bpa-btn bpa-btn--primary bpa-btn__medium">
							<?php esc_html_e( 'Go', 'bookingpress-appointment-booking' ); ?>
						</el-button>
					</el-col>
				</el-row>
			</el-container>		
		</el-row>
	</div>
</el-main>
<el-dialog id="calendar_appointment_modal" custom-class="bpa-dialog bpa-dialog--fullscreen" modal-append-to-body=false title="" :visible.sync="open_location_popup" :before-close="closeLocationModal" fullscreen=true  :close-on-press-escape="close_modal_on_esc">
	<div class="bpa-dialog-heading">
		<el-row type="flex">
			<el-col :xs="12" :sm="12" :md="16" :lg="16" :xl="16">
				<h1 class="bpa-page-heading" v-if="location.location_id == '0'"><?php esc_html_e( 'Add Location', 'bookingpress-appointment-booking' ); ?></h1>
				<h1 class="bpa-page-heading" v-else><?php esc_html_e( 'Edit Location', 'bookingpress-appointment-booking' ); ?></h1>
			</el-col>
			<el-col :xs="12" :sm="12" :md="7" :lg="7" :xl="7" class="bpa-dh__btn-group-col">
				<el-button class="bpa-btn bpa-btn--primary" :class="(is_display_save_loader == '1') ? 'bpa-btn--is-loader' : ''" @click="saveLocationDetails()" >
				  <span class="bpa-btn__label"><?php esc_html_e( 'Save', 'bookingpress-appointment-booking' ); ?></span>
				  <div class="bpa-btn--loader__circles">				    
					  <div></div>
					  <div></div>
					  <div></div>
				  </div>
				</el-button>
				<el-button class="bpa-btn" @click="closeLocationModal()"><?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?></el-button>
			</el-col>
		</el-row>
	</div>
	<div class="bpa-dialog-body">
		<div class="bpa-form-row">
			<el-row>
				<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
					<div class="bpa-default-card bpa-db-card">
						<el-form ref="location" :rules="rules" :model="location" label-position="top" @submit.native.prevent>
							<div class="bpa-form-body-row">
								<el-row :gutter="24">
									<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
										<el-form-item prop="location_name">
											<template #label>												
												<span class="bpa-form-label"><?php esc_html_e( 'Name', 'bookingpress-appointment-booking' ); ?></span>
											</template>
											<el-input class="bpa-form-control" v-model="location.location_name" name="location_name" placeholder="<?php esc_html_e( 'Location Name', 'bookingpress-appointment-booking' ); ?>" ></el-input>
										</el-form-item>
									</el-col>									
									<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
										<el-form-item prop="location_address">
											<template #label>												
												<span class="bpa-form-label"><?php esc_html_e( 'Address', 'bookingpress-appointment-booking' ); ?></span>
											</template>
											<el-input class="bpa-form-control" v-model="location.location_address" name="location_address" placeholder="<?php esc_html_e( 'Location Address', 'bookingpress-appointment-booking' ); ?>"></el-input>
										</el-form-item>
									</el-col>
									<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
										<el-form-item prop="location_map_marker">
											<template #label>												
												<span class="bpa-form-label"><?php esc_html_e( 'Pin Icon', 'bookingpress-appointment-booking' ); ?></span>
											</template>
											<el-select class="bpa-form-control" v-model="location.location_map_marker">
												<el-option v-for="item in location_map_markers":value="item.file_value"><img :src="item.file_name" /><span class="bookingpress_pin_icon_span_styles">{{ item.file_value }}</span>
												</el-option>
											</el-select>
										</el-form-item>
									</el-col>									
								</el-row>
							</div>	
							<div class="bpa-form-body-row">
								<el-row :gutter="24">											
									<el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="16">
										<el-row :gutter="24">						
											<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
												<label><?php esc_html_e( 'Map', 'bookingpress-appointment-booking' ); ?>:</label>
												<div id="map"></div>
											</el-col>								
										</el-row>										
										<el-row :gutter="24">						
											<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
												<div class="location_lat_long_div_styles">
													<label><i class="el-icon-location"></i></label>
													<label @click="displayCustomLocation"><?php esc_html_e( 'This is not the right address', 'bookingpress-appointment-booking' ); ?>?</label>
												</div>
											</el-col>
										</el-row>					
										<el-row :gutter="24" v-if="location.location_custom_location == 1">
											<el-col :xs="12" :sm="12" :md="12" :lg="13" :xl="12">
												<el-input-number v-model="location.location_custom_lat" class="bpa-form-control bpa-form-control--number" :precision="7" step-strictly></el-input-number>
											</el-col>
											<el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
												<el-input-number v-model="location.location_custom_long" class="bpa-form-control bpa-form-control--number" :precision="7" step-strictly></el-input-number>
											</el-col>
										</el-row>					
										</el-col>		
									</el-col>									
									<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">																		
										<el-row :gutter="24">		
											<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
												<el-form-item prop="phone">
													<template #label>
														<span class="bpa-form-label"><?php esc_html_e( 'Phone', 'bookingpress-appointment-booking' ); ?></span>
													</template>
													<el-input class="bpa-form-control" v-model="location.location_phone" placeholder="" >
														<el-select slot="prepend" class="bpa-form-control__country-dropdown" placeholder="IN" v-model="location.location_phone_country">
															<el-option v-for="countries in phone_countries_details" :value="countries.code" :label="countries.emoji">{{ countries.emoji+' '+countries.name }}</el-option>
														</el-select>
													</el-input>											
												</el-form-item>
											</el-col>
										</el-row>
									</el-col>
								</el-row>
							</div>	
							<div class="bpa-form-body-row">
								<el-row :gutter="24">																						
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
										<el-form-item prop="location_description">
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Description', 'bookingpress-appointment-booking' ); ?></span>
											</template>
											<el-input class="bpa-form-control" type="textarea" :rows="10" v-model="location.location_description"></el-input>
										</el-form-item>
									</el-col>
								</el-row>	
							</div>
						</el-form>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
</el-dialog>