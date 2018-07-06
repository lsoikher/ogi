var wcAutoshipAnalyticsReportsEndpoint = 'https://analytics.wooautoship.com:9000/api/Reports';

function getReport(reportName, siteUrl, licenseKey, onLoad, onError) {
    var xhr = new XMLHttpRequest();
    var url = wcAutoshipAnalyticsReportsEndpoint + '/' + reportName + '?siteUrl=' + encodeURIComponent(siteUrl) + '&licenseKey=' + encodeURIComponent(licenseKey);
    xhr.open('GET', url, true);
    xhr.responseType = 'json';

    xhr.onload = function(e) {
        if (this.status == 200) {
            onLoad(this.response);
        } else {
            onError(this);
        }
    };

    xhr.send();
}

function getOrderCount(siteUrl, licenseKey, onLoad) {
    var xhr = new XMLHttpRequest();
    var url = wcAutoshipAnalyticsReportsEndpoint + '/DashboardReport/OrderCount?siteUrl=' + encodeURIComponent(siteUrl) + '&licenseKey=' + encodeURIComponent(licenseKey);
    xhr.open('GET', url, true);
    xhr.responseType = 'json';

    xhr.onload = function(e) {
        if (this.status == 200) {
            onLoad(this.response);
        } else {
            onLoad(0);
        }
    };

    xhr.send();
}

function loadReport(elementId, reportData) {
    var reportElement = document.getElementById(elementId);
    reportElement.setAttribute('powerbi-access-token', reportData.AccessToken);
    reportElement.setAttribute('powerbi-embed-url', reportData.Report.embedUrl);

    // For complete list of embed configuration see the following wiki page
    // https://github.com/Microsoft/PowerBI-JavaScript/wiki/Embed-Configuration-Details
    var reportConfig = {
        settings: {
            filterPaneEnabled: false,
            navContentPaneEnabled: false
        }
    };

    // Embed report
    // https://microsoft.github.io/PowerBI-JavaScript/classes/_src_service_.service.html#embed
    var report = powerbi.embed(reportElement, reportConfig);

    // Date filters
    var startDateElement = document.getElementById('pbi-start-date');
    var endDateElement = document.getElementById('pbi-end-date');
    if (null == startDateElement || null == endDateElement) {
        return;
    }
    var applyDateFilters = function () {
        var conditions = [];
        if (null != startDateElement.value && startDateElement.value.length > 0) {
            conditions.push({
                operator: "GreaterThanOrEqual",
                value: (new Date(startDateElement.value)).toJSON()
            });
        }
        if (null != endDateElement.value && endDateElement.value.length > 0) {
            conditions.push({
                operator: "LessThanOrEqual",
                value: (new Date(endDateElement.value)).toJSON()
            });
        }
        if (conditions.length < 1) {
            report.removeFilters();
            return;
        }
        var filter = {
            $schema: "http://powerbi.com/product/schema#advanced",
            target: {
                table: "Orders",
                column: "DateCreated"
            },
            logicalOperator: "AND",
            conditions: conditions
        };
        report.setFilters([filter])
            .catch(function (errors) {
            // Handle error
            console.log(errors);
        });
    };
    startDateElement.addEventListener('change', applyDateFilters);
    endDateElement.addEventListener('change', applyDateFilters);
}

document.addEventListener('DOMContentLoaded', function () {
    var dashboardReportElement = document.getElementById('pbi-report-dashboard');
    if (null != dashboardReportElement) {
        getOrderCount(dashboardReportElement.getAttribute('data-site-url'), dashboardReportElement.getAttribute('data-license-key'), function (orderCount) {
            if (orderCount > 0) {
                getReport('DashboardReport', dashboardReportElement.getAttribute('data-site-url'), dashboardReportElement.getAttribute('data-license-key'),
                    function (reportData) {
                        loadReport('pbi-report-dashboard', reportData);
                    },
                    function (xhr) {
                        alert('There was an error loading this report!');
                        dashboardReportElement.style.display = "none";
                        document.getElementById('pbi-report-filters').style.display = "none";
                        document.getElementById('pbi-report-no-data').style.display = "block";
                    }
                );
            } else {
                alert('There was an error loading this report!');
                dashboardReportElement.style.display = "none";
                document.getElementById('pbi-report-filters').style.display = "none";
                document.getElementById('pbi-report-no-data').style.display = "block";
            }
        });
    }

    var ordersWidgetReportElement = document.getElementById('pbi-report-orders-widget');
    if (null != ordersWidgetReportElement) {
        getOrderCount(ordersWidgetReportElement.getAttribute('data-site-url'), ordersWidgetReportElement.getAttribute('data-license-key'), function (orderCount) {
            if (orderCount > 0) {
                getReport('OrdersWidget', ordersWidgetReportElement.getAttribute('data-site-url'), ordersWidgetReportElement.getAttribute('data-license-key'),
                    function (reportData) {
                        loadReport('pbi-report-orders-widget', reportData);
                    },
                    function (xhr) {
                        ordersWidgetReportElement.style.display = "none";
                        document.getElementById('pbi-report-orders-widget-no-data').style.display = "block";
                    }
                );
            } else {
                ordersWidgetReportElement.style.display = "none";
                document.getElementById('pbi-report-orders-widget-no-data').style.display = "block";
            }
        });
    }
});