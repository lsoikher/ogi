<div class="wc-autoship-container">
	<?php do_action( 'wc_autoship_before_autoship_schedules', $customer->ID ); ?>

	<div ng-app="AutoshipApp">
		<div ng-controller="AutoshipSchedulesCtrl" ng-init="init(<?php echo esc_attr( json_encode( ! empty( $_GET['customer_id'] ) ? $_GET['customer_id'] : get_current_user_id() ) ); ?>)">

			<div class="row" ng-repeat="schedule in schedules">
				<div class="col-xs-12">
					<!--HEADER-->

					<div class="wc-autoship-schedule panel panel-default" ng-class="{ 'schedule-error': schedule.hasError }">

						<div class="panel-heading" ng-class="schedule.autoship_status ? 'active-schedule' : ''">
							<div class="row">
								<div class="col-xs-2">
									<button type="button" ng-click="toggleStatus(schedule)" class="btn" ng-class="{ 'btn-success': schedule.autoship_status >= 1, 'btn-danger': schedule.autoship_status <= 0 }">
										<i ng-class="{ 'glyphicon glyphicon-play': schedule.autoship_status >= 1, 'glyphicon glyphicon-pause': schedule.autoship_status <= 0 }"></i>
									</button>
								</div>
								<div class="col-xs-10">
									<div>
										({{getTotalItems(schedule)}}) <?php echo __( 'Items Ship ', 'wc-autoship' ) ?>
										<select
											ng-model="schedule.autoship_frequency"
											ng-change="updateScheduleFrequency(schedule, schedule.autoship_frequency)"
											ng-show="schedule.available_frequencies.length"
											class="autoship-frequency-select">
											<option
												ng-repeat="item in schedule.available_frequencies"
												value="{{item.frequency}}">
												{{item.title}}
											</option>
										</select>
										<span ng-hide="schedule.available_frequencies.length"><?php echo __( 'Every {{schedule.autoship_frequency}} Days', 'wc-autoship' ); ?></span>
										<i ng-click="toggleScheduleView(schedule)" class="schedule-status pull-right"
										   ng-class="schedule.isVisible == null || !schedule.isVisible ? 'glyphicon glyphicon-menu-down' : 'glyphicon glyphicon-menu-up'"></i>
									</div>
									<div>
										<div><span ng-show="schedule.autoship_status === 1 || schedule.autoship_status === '1'"><?php echo __( 'Next ship date:', 'wc-autoship' ); ?> <input type="date" ng-model="schedule.next_order_date_object" min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" ng-change="updateNextOrderDate(schedule)" class="autoship-input-date wc-autoship-datepicker" /></span></div>
										<div><span ng-show="schedule.autoship_status !== 1 && schedule.autoship_status !== '1'"><?php echo __( 'Paused. Hit <i class="glyphicon glyphicon-pause text-danger"></i> to resume', 'wc-autoship' ); ?></span></div>
									</div>
								</div>
							</div>
							<div class="row" ng-show="null != schedule.errorMessage && '' != schedule.errorMessage">
								<div class="col-xs-10 col-xs-offset-2"><small><span class="glyphicon glyphicon-warning-sign"></span> {{schedule.errorMessage}}</small></div>
							</div>
						</div>
						<div class="panel-body" ng-show="schedule.isVisible">

							<div class="row margin-default-bottom">
								<div class="col-xs-2"></div>
								<div class="col-xs-4 col-md-5">
									<?php echo __( 'Item:', 'wc-autoship' ); ?>
								</div>
								<div class="col-xs-3 col-md-2">
									<?php echo __( 'Qty:', 'wc-autoship' ); ?>
								</div>
								<div class="col-xs-3 text-right">
									<?php echo __( 'Total:', 'wc-autoship' ); ?>
								</div>
							</div>

							<div class="row margin-default-bottom" ng-repeat="item in schedule.items">
								<div class="col-xs-2">
									<button type="button" class="btn btn-default btn-xs visible-xs" ng-click="removeItem(schedule, item)"><i
											class="glyphicon glyphicon-remove"></i></button>
									<button type="button" class="btn btn-default btn-sm hidden-xs" ng-click="removeItem(schedule, item)"><i
											class="glyphicon glyphicon-remove"></i></button>
								</div>
								<div class="hidden-xs hidden-sm col-md-1 align-middle">
									<img class="wc-autoship-schedule-item-thumbnail" src="{{item.product_thumbnail}}" />
								</div>
								<div class="col-xs-4 align-middle">
									<a class="" href="{{item.product_url}}">{{item.product_title}}</a>
								</div>
								<div class="col-xs-3 col-md-2">
									<input class="qty" type="number" ng-value="item.qty" ng-model="item.qty"
										   min="1" ng-keyup="filterItemQuantity(item);" ng-change="updateItemQuantity(schedule, item);"/>

								</div>
								<div class="col-xs-3 text-right">
									{{(item.line_price_formatted)}}
								</div>
							</div>

							<div class="row margin-default-y">
								<div class="col-xs-2">
									<button type="button" class="btn btn-default btn-xs visible-xs"><i class="glyphicon glyphicon-plus"></i></button>
									<button type="button" class="btn btn-default btn-sm hidden-xs"><i class="glyphicon glyphicon-plus"></i></button>
								</div>
								<div class="col-xs-10">
									<!--ADD ITEM LIST-->
									<select class="form-control" ng-model="schedule.product_to_add"
											ng-options="(product.title + ' ' + ' (' + product.price_formatted + ')' ) for product in schedule.available_products"
											ng-change="addItem(schedule)">
										<option value=''><?php echo __( 'Add item...', 'wc-autoship' ); ?></option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12"><hr /></div>
							</div>

							<div class="row">
								<!--SET PAYMENT METHOD-->
								<div class="col-xs-12 col-md-6 margin-default-bottom">
									<label><?php echo __( 'Payment Method', 'wc-autoship' ); ?></label>
									<select class="form-control" ng-model="schedule.payment_method" ng-change="updatePaymentMethod(schedule)"
											ng-options="method.display_name for method in paymentMethods track by method.id">
									</select>
								</div>
								<!--SET SHIPPING METHOD-->
								<div class="col-xs-12 col-md-6 margin-default-bottom">
									<label><?php echo __( 'Shipping Method', 'wc-autoship' ); ?></label>
									<select class="form-control" ng-model="schedule.shipping_method" ng-change="updateShippingMethod(schedule)"
											ng-options="(method.label + ' (' + method.cost + ')') for method in schedule.available_shipping_methods track by method.id">
									</select>
								</div>
							</div>

							<?php if ( 'no' != $enable_coupon_field ): ?>
								<div class="row">
									<div class="col-xs-12"><label><?php echo __( 'Coupon Code', 'wc-autoship' ); ?></label></div>
								</div>
								<div class="row margin-default-bottom">
									<div class="col-xs-12">
										<div class="input-group">
											<input placeholder="<?php echo __( 'Enter code...', 'wc-autoship' ); ?>" type="text" class="form-control"
												   ng-model="schedule.coupon"/>
											<div class="input-group-btn">
												<button type="button" class="btn btn-primary btn-block" ng-click="applyCoupon(schedule)"><?php echo __( 'Apply', 'wc-autoship' ); ?></button>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>

						</div>

						<div class="wc-autoship-schedule-totals panel-footer" ng-show="schedule.isVisible">
							<!--TOTALS-->
							<div class="row">
								<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Subtotal:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">{{schedule.subtotal}}</div>
							</div>
							<div ng-show="schedule.discount_total != null && schedule.discount_total != ''">
								<div class="row">
									<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Discounts:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">({{schedule.discount_total}})</div>
								</div>
							</div>
							<div ng-show="schedule.shipping_total != null && schedule.shipping_total != ''">
								<div class="row">
									<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Shipping:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">{{schedule.shipping_total}}</div>
								</div>
							</div>
							<div ng-show="schedule.shipping_tax != null && schedule.shipping_tax != ''">
								<div class="row">
									<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Shipping tax:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">{{schedule.shipping_tax}}</div>
								</div>
							</div>
							<div ng-show="schedule.tax_total != null && schedule.tax_total != ''">
								<div class="row">
									<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Tax:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">{{schedule.tax_total}}</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-md-8 text-left"><?php echo __( 'Total:', 'wc-autoship' ); ?> </div> <div class="col-xs-6 col-md-4 text-right">{{schedule.total}}</div>
							</div>
							<div class="row margin-double-top margin-default-bottom">
								<div class="col-xs-12 text-center">
									<button class="btn btn-danger" ng-click="deleteSchedule(schedule)">
										<i class="glyphicon glyphicon-trash"></i>
										<?php echo __( 'Delete Schedule', 'wc-autoship' ); ?>
									</button>
								</div>
							</div>
						</div>
						<div class="loading" ng-if="schedule.isLoading"></div>
					</div>
				</div>
			</div>

			<div id="wc-autoship-schedules-alerts">
				<div ng-repeat="alert in alerts" ng-class="{ 'alert-danger': alert.isError, 'alert-success': !alert.isError }" class="alert alert-dismissable">{{ alert.message }}<a href="" ng-click="removeAlert(alert)" class="close" aria-label="close">&times;</a></div>
			</div>

		</div>
	</div>

	<?php do_action( 'wc_autoship_after_autoship_schedules', $customer->ID ); ?>
</div>
