(function () {
    angular.module('AutoshipApp', [])

        .controller('AutoshipSchedulesCtrl', ['$scope', '$http', '$interval', function ($scope, $http, $interval) {

            var changeInterval = null;
            var changeIntervalLength = 700;

            function getTimeForServer(dateString) {
                var localDate = new Date();
                var offset = localDate.getTimezoneOffset() * 60 * 1000;
                var orderTime = new Date(dateString).getTime();
                return new Date(orderTime - offset); // set
            }

            function getDateForBrowser(dateString) {
                var localDate = new Date();
                var offset = localDate.getTimezoneOffset() * 60 * 1000;
                var orderTime = new Date(dateString).getTime();
                return new Date(orderTime + offset); // set
            }

            function getScheduleRequest(schedule, successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_schedule&schedule_id=' + schedule.id).then(successCallback, failureCallback);
            }

            function getSchedulesRequest(successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_schedules&customer_id=' + $scope.customerId).then(successCallback, failureCallback);
            }

            function getAvailableProductsRequest(schedule, successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_available_products&schedule_id=' + schedule.id).then(successCallback, failureCallback);
            }

            function getAvailableFrequenciesRequest(schedule, successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_available_frequencies&schedule_id=' + schedule.id).then(successCallback, failureCallback);
            }

            function deleteScheduleRequest(schedule, successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_delete_schedule&schedule_id=' + schedule.id).then(successCallback, failureCallback);
            }

            function applyScheduleCouponRequest(schedule, coupon, successCallback, failureCallback) {
                var data = { "coupon": coupon };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function updateScheduleNextOrderDateRequest(schedule, nextOrderDate, successCallback, failureCallback) {
                var data = { "next_order_date": '' + nextOrderDate.getFullYear() + '-' + (nextOrderDate.getMonth() + 1) + '-' + (nextOrderDate.getDate()) };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function updateScheduleFrequencyRequest(schedule, frequency, successCallback, failureCallback) {
                var data = { "autoship_frequency": frequency };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function updateSchedulePaymentMethodRequest(schedule, paymentTokenId, successCallback, failureCallback) {
                var data = { "payment_token_id": paymentTokenId };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function updateScheduleShippingMethodRequest(schedule, shippingMethodId, successCallback, failureCallback) {
                var data = { "shipping_method_id": shippingMethodId };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function updateScheduleAutoshipStatusRequest(schedule, autoship_status, successCallback, failureCallback) {
                var data = { "autoship_status": autoship_status };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule&schedule_id=' + schedule.id, data).then(successCallback, failureCallback);
            }

            function addScheduleItemRequest(schedule, product, successCallback, failureCallback) {
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_add_schedule_item&schedule_id=' + schedule.id, product).then(successCallback, failureCallback);
            }

            function updateScheduleItemQuantityRequest(item, quantity, successCallback, failureCallback) {
                var data = { "qty": quantity };
                $http.post(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_update_schedule_item&schedule_item_id=' + item.id, data).then(successCallback, failureCallback);
            }

            function deleteScheduleItemRequest(item, successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_delete_schedule_item&schedule_item_id=' + item.id).then(successCallback, failureCallback);
            }

            function getPaymentMethodsRequest(successCallback, failureCallback) {
                $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_payment_methods&customer_id=' + $scope.customerId).then(successCallback, failureCallback);
            }

            var cartRequests = [];
            var cartRequestIsLoading = false;
            function getCartRequest(schedule, successCallback, failureCallback) {
                if (!cartRequestIsLoading) {
                    cartRequestIsLoading = true;
                    $http.get(WC_AUTOSHIP_AJAX_URL + '?action=wc_autoship_schedules_get_cart&schedule_id=' + schedule.id).then(function (response) {
                        successCallback(response);
                        cartRequestIsLoading = false;
                        if (cartRequests.length > 0) {
                            var nextCartRequest = cartRequests.pop();
                            getCartRequest(nextCartRequest.schedule, nextCartRequest.successCallback, nextCartRequest.failureCallback);
                        }
                    },
                    failureCallback);
                } else {
                    // Hmm do we need to push both callbacks now?
                    cartRequests.push({"schedule": schedule, "successCallback": successCallback, "failureCallback": failureCallback});
                }

            }

            $scope.customerId = 0;
            $scope.schedules = [];
            $scope.paymentMethods = [];
            $scope.alerts = [];

            $scope.deleteSchedule = function (schedule) {
                schedule.isLoading = true;
                deleteScheduleRequest(schedule, function success(response) {
                    $scope.addAlert($scope.translate("Schedule deleted"), false);
                    angular.forEach($scope.schedules, function (scopeSchedule, s) {
                        if (scopeSchedule.id == schedule.id) {
                            $scope.schedules.splice(s, 1);
                            schedule.isLoading = false;
                            return false;
                        }
                    });
                },
                function () {
                    $scope.addAlert($scope.translate("There was an error deleting schedule @schedule_id", { 'schedule_id': schedule.id }), true);
                    schedule.isLoading = false
                });
            };

            $scope.getTotalItems = function (schedule) {
                var total = 0;
                for (item in schedule.items) {
                    if (null == schedule.items[item].qty) {
                        continue;
                    }
                    total += parseInt(schedule.items[item].qty);
                }
                return total;
            };

            $scope.removeItem = function (schedule, item) {
                deleteScheduleItemRequest(item, function success(response) {
                        $scope.addAlert($scope.translate('Item removed'), false);
                        angular.forEach(schedule.items, function (scheduleItem, i) {
                        if (scheduleItem.id == item.id) {
                            schedule.items.splice(i, 1);
                            return false;
                        }
                    });
                    $scope.initSchedule(schedule);
                },
                function failure(response){
                    $scope.addAlert($scope.translate("There was an error removing item '@product_title' from schedule '@schedule_id'. Error: @response_status @response_status_text", { 'product_title': item.product_title, 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                });
            };

            $scope.addItem = function (schedule) {
                var itemExists = false;
                angular.forEach(schedule.items, function (item, i) {
                    if (item.product_id == schedule.product_to_add.product_id && item.variation_id == schedule.product_to_add.variation_id) {
                        itemExists = true;
                        updateScheduleItemQuantityRequest({"id": item.id}, item.qty + 1,
                            function success(response) {
                                $scope.addAlert($scope.translate('Quantity updated'), false);
                                for (var d in response.data) {
                                    item[d] = response.data[d];
                                }
                                $scope.initSchedule(schedule);

                            },
                            function failure(response) {
                                $scope.addAlert($scope.translate("There was an error updating the quantity for the item '@product_title'. Error: @response_status @response_status_text", { 'product_title': item.product_title, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                            });
                        return false;
                    }
                });
                if (!itemExists) {
                    addScheduleItemRequest(schedule, schedule.product_to_add, function success(response) {
                        $scope.addAlert($scope.translate("Item added"), false);
                        schedule.items.push(response.data);
                        $scope.initSchedule(schedule);
                    },
                    function failure(response) {
                        $scope.addAlert($scope.translate("There was an error adding the item to schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                    });
                }
                schedule.product_to_add = '';
            };

            $scope.updateScheduleFrequency = function (schedule, frequency) {
                schedule.isLoading = true;
                updateScheduleFrequencyRequest(schedule, frequency, function success(successResponse) {
                    $scope.addAlert($scope.translate("Schedule frequency updated."), false);
                    schedule.isLoading = false;
                    schedule.autoship_frequency = successResponse.data.autoship_frequency.toString();
                },
                function failure(response) {
                    schedule.isLoading = false;
                    $scope.addAlert($scope.translate("There was an error updating the Autoship frequency for '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                });
            };

            $scope.updateNextOrderDate = function (schedule) {
                if (schedule.next_order_date_object) {
                    clearInterval(changeInterval);
                    changeInterval = setTimeout(function () {
                        schedule.isLoading = true;
                        updateScheduleNextOrderDateRequest(schedule, schedule.next_order_date_object , function success(response) {
                            $scope.addAlert($scope.translate("Next order date updated"), false);
                            var nextOrderTime = new Date(response.data.next_order_date + ' 00:00:00');
                            schedule.next_order_date_object = nextOrderTime; // set offset here
                            schedule.next_order_date = response.data.next_order_date;
                            schedule.isLoading = false;
                        },
                        function failure(response) {
                            $scope.addAlert($scope.translate("There was an error updating the next order date for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);

                        });
                    }, changeIntervalLength);
                }
            };

            $scope.updateShippingMethod = function (schedule) {
                updateScheduleShippingMethodRequest(schedule, schedule.shipping_method.id, function success(response) {
                    schedule.shipping_method_id = response.data.shipping_method_id;
                    $scope.addAlert($scope.translate("Shipping method updated"), false);
                    $scope.initSchedule(schedule);
                },
                function failure(response) {
                    $scope.addAlert($scope.translate("There was an error updating the shipping method for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                });
            };

            $scope.updatePaymentMethod = function (schedule) {
                updateSchedulePaymentMethodRequest(
                    schedule,
                    schedule.payment_method.id,
                    function success(response) {
                        var methodIndex = $scope.paymentMethods.findIndex(function(method) { return method.id == response.data.payment_token_id; });
                        var methodName = $scope.paymentMethods[methodIndex].display_name;
                        $scope.addAlert($scope.translate("Payment method updated"), false);
                        schedule.payment_token_id = response.data.payment_token_id;
                        $scope.initSchedule(schedule);
                    },
                    function failure(response) {
                        $scope.addAlert($scope.translate("There was an error updating payment method for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                    }
                );
            };

            $scope.applyCoupon = function (schedule) {
                applyScheduleCouponRequest(
                    schedule,
                    schedule.coupon,
                    function success(response) {
                        if (null != schedule.coupon && '' != schedule.coupon) {
                            $scope.addAlert($scope.translate("Coupon '@coupon' was applied to schedule", { 'coupon': schedule.coupon }), false);
                        }
                        $scope.initSchedule(schedule);
                    },
                    function failure(response) {
                        $scope.addAlert($scope.translate("There was an error applying coupon '@coupon' to schedule '@schedule_id'. Error: @response_status @response_status_text", { 'coupon': schedule.coupon, 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                    }
                );
            };

            $scope.filterItemQuantity = function (item) {
                if (null == item.qty) {
                    // User is typing
                    return;
                }
                if (parseInt(item.qty) < 1) {
                    item.qty = 1;
                }
            };

            $scope.updateItemQuantity = function (schedule, item) {
                clearTimeout(changeInterval);
                changeInterval = setTimeout(function () {
                    $scope.filterItemQuantity(item);
                    if (null == item.qty) {
                        // User is typing
                        return;
                    }

                    updateScheduleItemQuantityRequest(item, item.qty,
                        function success(response) {
                            $scope.addAlert($scope.translate('Quantity updated'), false);
                            for (var d in response.data) {
                                item[d] = response.data[d];
                            }
                            $scope.initSchedule(schedule);
                        },
                        function failure(response) {
                            $scope.addAlert($scope.translate("There was an error updating the quantity for the item '@product_title'. Error: @response_status @response_status_text", { 'product_title': item.product_title, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                        });

                }, changeIntervalLength);
            };

            $scope.toggleStatus = function (schedule) {
                schedule.autoship_status = parseInt(schedule.autoship_status);
                var new_autoship_status = (schedule.autoship_status + 1) % 2;
                schedule.isLoading = true;
                updateScheduleAutoshipStatusRequest(schedule, new_autoship_status, function success(response) {
                    var status = response.data.autoship_status ? "resumed" : "paused";
                    $scope.addAlert($scope.translate("Schedule @status", { 'status': status }), false);
                    schedule.autoship_status = response.data.autoship_status;
                    schedule.isLoading = false;
                },
                function failure(response) {
                    $scope.addAlert($scope.translate("There was an error changing the status of schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText }), true);
                    schedule.isLoading = false;
                });
            };

            $scope.toggleScheduleView = function (schedule) {
                schedule.isVisible = ! schedule.isVisible;
            };

            $scope.addAlert = function (message, isError) {
                var time = (new Date()).getTime();
                var random = Math.floor(Math.random() * 100000);
                var id = '' + time + '-' + random;
                var newAlert = { 'id': id, 'message': message, 'isError': isError };
                $scope.alerts.push(newAlert);
                var dismissInterval = isError ? 10000 : 6000;
                $interval(function () {
                    $scope.removeAlert(newAlert);
                }, dismissInterval, 1);
            };

            $scope.removeAlert = function (alert) {
                var index = $scope.alerts.findIndex(function (element) {
                    return (element.id == alert.id);
                });
                if (index > -1) {
                    $scope.alerts.splice(index, 1);
                }
            };

            $scope.translate = function (message, parameters) {
                var translatedMessage = null;
                if ('undefined' == WC_AUTOSHIP_SCHEDULES_MESSAGES[message] || null == WC_AUTOSHIP_SCHEDULES_MESSAGES[message]) {
                    translatedMessage = message;
                } else {
                    translatedMessage = WC_AUTOSHIP_SCHEDULES_MESSAGES[message];
                }
                if (null != parameters) {
                    for (var p in parameters) {
                        translatedMessage = translatedMessage.replace('@' + p, parameters[p]);
                    }
                }
                return translatedMessage;
            };

            $scope.flagSchedule = function (schedule, message) {
                schedule.hasError = true;
                schedule.errorMessage = message;
            };

            $scope.unflagSchedule = function (schedule) {
                schedule.hasError = false;
                schedule.errorMessage = null;
            }

            $scope.initSchedule = function (schedule) {
                schedule.isLoading = true;
                $scope.unflagSchedule(schedule);
                // Set dates
                // Account for timezone offset
                var localDate = new Date();
                var offset = localDate.getTimezoneOffset() * 60 * 1000;
                var lastOrderTime = new Date(schedule.last_order_date).getTime();
                var nextOrderTime = new Date(schedule.next_order_date).getTime();
                schedule.last_order_date_object = new Date(lastOrderTime + offset);
                schedule.next_order_date_object = new Date(nextOrderTime + offset); // set offset here

                var methodExists = false;
                // Assign payment method to schedule
                angular.forEach($scope.paymentMethods, function (method) {

                    if (method.id == schedule.payment_token_id) {
                        schedule.payment_method = method;
                        methodExists = true;
                    }
                });

                // Did we find one?
                if (!methodExists) {
                    var errorMessage = $scope.translate("Invalid payment method. Please select a valid payment method.");
                    $scope.addAlert(errorMessage, true);
                    $scope.flagSchedule(schedule, errorMessage);
                }

                // Assign available products to schedule
                getAvailableProductsRequest(schedule, function success(productsResponse) {
                    schedule.available_products = productsResponse.data;
                },
                function failure(response) {
                    var errorMessage = $scope.translate("There was an error retrieving available products for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText });
                    $scope.addAlert(errorMessage, true);
                    $scope.flagSchedule(schedule, errorMessage);
                });

                getAvailableFrequenciesRequest(schedule, function success(frequenciesResponse){
                    if (frequenciesResponse.data.length < 1) {
                        var errorMessage = $scope.translate("There are no frequencies available for this schedule due to conflicting product settings.");
                        $scope.addAlert(errorMessage);
                        $scope.flagSchedule(schedule, errorMessage);
                    }
                    schedule.available_frequencies = frequenciesResponse.data;
                    var existingFrequency = schedule.available_frequencies.find(function (f) {
                        return schedule.autoship_frequency == f.frequency;
                    });
                    if (null == existingFrequency) {
                        schedule.available_frequencies.push({
                            'frequency': schedule.autoship_frequency,
                            'title': $scope.translate('Every @frequency Days', { frequency: schedule.autoship_frequency })
                        });
                    }
                }, function failure(response) {
                    var errorMessage = $scope.translate("There was an error retrieving available frequencies for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText });
                    $scope.addAlert(errorMessage, true);
                    $scope.flagSchedule(schedule, errorMessage);
                });

                // Get cart
                getCartRequest(schedule, function success(cartResponse) {
                    // Set shipping methods
                    schedule.available_shipping_methods = cartResponse.data.shipping_methods;
                    var shippingMethodExists = false;
                    angular.forEach(schedule.available_shipping_methods, function (method) {
                        if (method.id == schedule.shipping_method_id) {
                            schedule.shipping_method = method;
                            shippingMethodExists = true;
                        }
                    });
                    if (!shippingMethodExists) {
                        var errorMessage = null;
                        if (schedule.available_shipping_methods.length > 0) {
                            errorMessage = $scope.translate("Invalid shipping method. Please select a valid shipping method.");
                        } else {
                            errorMessage = $scope.translate("There are no shipping methods available for this order. Do you have a valid Shipping Address in your account?");
                        }
                        $scope.addAlert(errorMessage, true);
                        $scope.flagSchedule(schedule, errorMessage);
                    }
                    // Set totals
                    schedule.total = cartResponse.data.total;
                    schedule.subtotal = cartResponse.data.subtotal;
                    schedule.discount_total = cartResponse.data.discount_total;
                    schedule.tax_total = cartResponse.data.tax_total;
                    schedule.shipping_total = cartResponse.data.shipping_total;
                    schedule.shipping_tax = cartResponse.data.shipping_tax;
                    schedule.isLoading = false;
                    // Validate coupon
                    if (null != schedule.coupon && '' != schedule.coupon && 'undefined' != typeof(cartResponse.data.coupons)) {
                        if (cartResponse.data.coupons.indexOf(schedule.coupon) < 0) {
                            var errorMessage = $scope.translate("The coupon '@coupon' is not valid for your order", { 'coupon': schedule.coupon });
                            $scope.addAlert(errorMessage, true);
                            $scope.flagSchedule(schedule, errorMessage);
                        }
                    }
                },
                function failure(response) {
                    var errorMessage = $scope.translate("There was an error retrieving the cart for schedule '@schedule_id'. Error: @response_status @response_status_text", { 'schedule_id': schedule.id, 'response_status': response.status, 'response_status_text': response.statusText });
                    $scope.addAlert(errorMessage, true);
                    $scope.flagSchedule(schedule, errorMessage);
                });

                // initWcAutoshipDatepicker();
            };

            $scope.init = function (customerId) {
                // Set customer id
                $scope.customerId = parseInt(customerId);
                // Get payment methods
                getPaymentMethodsRequest(
                    function (paymentMethodsResponse) {
                        $scope.paymentMethods = paymentMethodsResponse.data;

                        // Get schedules
                        getSchedulesRequest(
                            function success(schedulesResponse) {
                                $scope.schedules = schedulesResponse.data;
                                angular.forEach( $scope.schedules, function (schedule) {
                                    schedule.isVisible = false;
                                    $scope.initSchedule(schedule);
                                });
                                // If they only have one schedule, open it
                                if ($scope.schedules.length === 1) {
                                    $scope.schedules[0].isVisible = true;
                                }
                            },
                            function failure(response) {
                                $scope.addAlert($scope.translate("There was an error retrieving your schedules. Error: @response_status @response_status_text", { 'response_status': response.status, 'response_status_text': response.statusText }), true);
                            }
                        );
                    },
                    function (response) {
                        $scope.addAlert($scope.translate("There was an error retrieving your payment methods. Error: @response_status @response_status_text", { 'response_status': response.status, 'response_status_text': response.statusText }), true);
                    }
                );
            };

        }]);
})();
