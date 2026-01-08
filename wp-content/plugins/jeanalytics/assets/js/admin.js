/**
 * JE Analytics - 管理后台脚本
 */

(function($) {
    'use strict';

    const JEA = {
        // 图表实例
        charts: {},

        // 当前选择的日期范围
        currentRange: '7days',

        // 图表颜色 - 科技风格
        colors: {
            primary: '#3b82f6',
            secondary: '#10b981',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            purple: '#8b5cf6',
            pink: '#ec4899',
            teal: '#14b8a6',
            gradient: {
                primary: ['rgba(59, 130, 246, 0.5)', 'rgba(59, 130, 246, 0)'],
                secondary: ['rgba(16, 185, 129, 0.5)', 'rgba(16, 185, 129, 0)'],
                success: ['rgba(16, 185, 129, 0.5)', 'rgba(16, 185, 129, 0)'],
            }
        },

        // 初始化
        init: function() {
            this.bindEvents();
            this.loadDashboardData();
            this.initCharts();

            // 如果是实时页面,启动自动刷新
            if ($('.jea-realtime-container').length) {
                this.startRealtimeRefresh();
            }
        },

        // 绑定事件
        bindEvents: function() {
            const self = this;

            // 日期范围选择
            $(document).on('click', '.jea-date-range button', function() {
                const range = $(this).data('range');
                self.currentRange = range;

                $('.jea-date-range button').removeClass('active');
                $(this).addClass('active');

                self.loadDashboardData();
            });

            // 导出按钮
            $(document).on('click', '.jea-export-item', function() {
                const type = $(this).data('type');
                const format = $(this).data('format');
                self.exportData(type, format);
            });

            // 刷新实时数据
            $(document).on('click', '.jea-refresh-realtime', function() {
                self.loadRealtimeData();
            });
        },

        // 加载仪表板数据
        loadDashboardData: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_dashboard_data',
                    nonce: jeaAdmin.nonce,
                    range: this.currentRange
                },
                beforeSend: function() {
                    $('.jea-stats-grid').addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        self.updateStats(response.data.overview);
                        self.updateTopPages(response.data.top_pages);
                        self.updateReferrers(response.data.referrers);
                        self.updateDevices(response.data.devices);
                        self.updateBrowsers(response.data.browsers);
                        self.updateCountries(response.data.countries);
                        self.updateHourlyChart(response.data.hourly);
                        self.loadChartData();

                        // 加载新增数据模块
                        self.loadSearchEngines();
                        self.loadGeoStats();
                        self.loadDeviceBrands();
                        self.loadRecentVisitors();
                    }
                },
                complete: function() {
                    $('.jea-stats-grid').removeClass('loading');
                }
            });
        },

        // 加载搜索引擎数据
        loadSearchEngines: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_search_engines',
                    nonce: jeaAdmin.nonce,
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success) {
                        self.updateSearchEngines(response.data);
                    }
                }
            });
        },

        // 加载地理位置统计
        loadGeoStats: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_geo_stats',
                    nonce: jeaAdmin.nonce,
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success) {
                        self.updateGeoStats(response.data);
                    }
                }
            });
        },

        // 加载设备品牌数据
        loadDeviceBrands: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_device_brands',
                    nonce: jeaAdmin.nonce,
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success) {
                        self.updateDeviceBrands(response.data);
                    }
                }
            });
        },

        // 加载最近访客数据
        loadRecentVisitors: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_recent_visitors',
                    nonce: jeaAdmin.nonce,
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success) {
                        self.updateRecentVisitors(response.data);
                    }
                }
            });
        },

        // 更新搜索引擎列表
        updateSearchEngines: function(engines) {
            const $container = $('#search-engines-list');
            if (!$container.length) return;

            if (!engines || engines.length === 0) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">🔍</div><div class="jea-empty-title">暂无搜索引擎数据</div></div>');
                return;
            }

            let html = '<table class="jea-table"><thead><tr><th>搜索引擎</th><th>访问量</th><th>链接</th></tr></thead><tbody>';

            engines.forEach(function(engine) {
                html += '<tr>';
                html += '<td><div class="jea-engine-name">';
                html += '<span class="jea-engine-icon">' + (engine.engine_icon || '🔍') + '</span>';
                html += '<span>' + (engine.engine_name || engine.referrer_domain || '未知') + '</span>';
                html += '</div></td>';
                html += '<td><strong>' + parseInt(engine.count).toLocaleString() + '</strong></td>';
                html += '<td>';
                if (engine.engine_url) {
                    html += '<a href="' + engine.engine_url + '" target="_blank" class="jea-link-btn">访问 →</a>';
                } else {
                    html += '-';
                }
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $container.html(html);
        },

        // 更新地理位置统计
        updateGeoStats: function(data) {
            const $container = $('#geo-stats-list');
            if (!$container.length) return;

            if (!data || (!data.countries?.length && !data.regions?.length && !data.cities?.length)) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">🌍</div><div class="jea-empty-title">暂无地理数据</div></div>');
                return;
            }

            let html = '<div class="jea-geo-tabs">';
            html += '<button class="jea-geo-tab active" data-tab="countries">国家/地区</button>';
            html += '<button class="jea-geo-tab" data-tab="regions">省份</button>';
            html += '<button class="jea-geo-tab" data-tab="cities">城市</button>';
            html += '</div>';

            // 国家榜单
            html += '<div class="jea-geo-content active" id="geo-countries">';
            if (data.countries && data.countries.length > 0) {
                html += '<div class="jea-ranking-list">';
                data.countries.forEach(function(item, index) {
                    const flag = item.country_code ?
                        String.fromCodePoint(...item.country_code.toUpperCase().split('').map(c => 127397 + c.charCodeAt(0))) : '🌍';
                    html += '<div class="jea-ranking-item">';
                    html += '<span class="jea-ranking-num">' + (index + 1) + '</span>';
                    html += '<span class="jea-ranking-flag">' + flag + '</span>';
                    html += '<span class="jea-ranking-name">' + (item.country || '未知') + '</span>';
                    html += '<span class="jea-ranking-count">' + parseInt(item.count).toLocaleString() + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            } else {
                html += '<div class="jea-empty-small">暂无数据</div>';
            }
            html += '</div>';

            // 省份榜单
            html += '<div class="jea-geo-content" id="geo-regions">';
            if (data.regions && data.regions.length > 0) {
                html += '<div class="jea-ranking-list">';
                data.regions.forEach(function(item, index) {
                    html += '<div class="jea-ranking-item">';
                    html += '<span class="jea-ranking-num">' + (index + 1) + '</span>';
                    html += '<span class="jea-ranking-icon">📍</span>';
                    html += '<span class="jea-ranking-name">' + (item.region || '未知') + '</span>';
                    html += '<span class="jea-ranking-count">' + parseInt(item.count).toLocaleString() + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            } else {
                html += '<div class="jea-empty-small">暂无数据</div>';
            }
            html += '</div>';

            // 城市榜单
            html += '<div class="jea-geo-content" id="geo-cities">';
            if (data.cities && data.cities.length > 0) {
                html += '<div class="jea-ranking-list">';
                data.cities.forEach(function(item, index) {
                    html += '<div class="jea-ranking-item">';
                    html += '<span class="jea-ranking-num">' + (index + 1) + '</span>';
                    html += '<span class="jea-ranking-icon">🏙️</span>';
                    html += '<span class="jea-ranking-name">' + (item.city || '未知') + '</span>';
                    html += '<span class="jea-ranking-count">' + parseInt(item.count).toLocaleString() + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            } else {
                html += '<div class="jea-empty-small">暂无数据</div>';
            }
            html += '</div>';

            $container.html(html);

            // 绑定tab切换事件
            $container.find('.jea-geo-tab').on('click', function() {
                const tab = $(this).data('tab');
                $container.find('.jea-geo-tab').removeClass('active');
                $(this).addClass('active');
                $container.find('.jea-geo-content').removeClass('active');
                $container.find('#geo-' + tab).addClass('active');
            });
        },

        // 更新设备品牌列表
        updateDeviceBrands: function(brands) {
            const $container = $('#device-brands-list');
            if (!$container.length) return;

            if (!brands || brands.length === 0) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">📱</div><div class="jea-empty-title">暂无设备品牌数据</div></div>');
                return;
            }

            const deviceIcons = {
                'desktop': '🖥️',
                'mobile': '📱',
                'tablet': '📱'
            };

            const brandIcons = {
                'Apple': '🍎',
                'Samsung': '📱',
                'Huawei': '📱',
                'Xiaomi': '📱',
                'OPPO': '📱',
                'vivo': '📱',
                'OnePlus': '📱',
                'Google': '📱',
                'Sony': '📱',
                'LG': '📱'
            };

            let html = '<table class="jea-table"><thead><tr><th>设备类型</th><th>品牌</th><th>型号</th><th>系统</th><th>访问量</th></tr></thead><tbody>';

            brands.forEach(function(device) {
                const typeIcon = deviceIcons[device.device_type] || '📱';
                const brandIcon = brandIcons[device.device_brand] || '';

                html += '<tr>';
                html += '<td><span class="jea-device-type">' + typeIcon + ' ' + (device.device_type === 'mobile' ? '手机' : device.device_type === 'tablet' ? '平板' : '桌面') + '</span></td>';
                html += '<td><strong>' + brandIcon + ' ' + (device.device_brand || '-') + '</strong></td>';
                html += '<td>' + (device.device_model || '-') + '</td>';
                html += '<td><span class="jea-os-badge">' + (device.os || '-') + (device.os_version ? ' ' + device.os_version : '') + '</span></td>';
                html += '<td><strong>' + parseInt(device.count).toLocaleString() + '</strong></td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $container.html(html);
        },

        // 更新最近访客列表
        updateRecentVisitors: function(visitors) {
            const $container = $('#recent-visitors-list');
            if (!$container.length) return;

            if (!visitors || visitors.length === 0) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">👥</div><div class="jea-empty-title">暂无访客数据</div></div>');
                return;
            }

            let html = '<table class="jea-table jea-visitors-table"><thead><tr>';
            html += '<th>IP地址</th>';
            html += '<th>地理位置</th>';
            html += '<th>设备/浏览器</th>';
            html += '<th>访问页面</th>';
            html += '<th>访问时间</th>';
            html += '</tr></thead><tbody>';

            visitors.forEach(function(visitor) {
                const flag = visitor.country_code ?
                    String.fromCodePoint(...visitor.country_code.toUpperCase().split('').map(c => 127397 + c.charCodeAt(0))) : '🌍';

                const location = [visitor.country, visitor.region, visitor.city]
                    .filter(Boolean)
                    .join(' › ') || '未知';

                const deviceInfo = [
                    visitor.device_type === 'mobile' ? '📱' : visitor.device_type === 'tablet' ? '📱' : '🖥️',
                    visitor.device_brand,
                    visitor.browser
                ].filter(Boolean).join(' ');

                const visitTime = new Date(visitor.created_at).toLocaleString('zh-CN');
                const pageTitle = visitor.page_title || visitor.page_url || '-';

                html += '<tr>';
                html += '<td><code class="jea-ip-address">' + (visitor.ip_address || '-') + '</code></td>';
                html += '<td><span class="jea-location">' + flag + ' ' + location + '</span></td>';
                html += '<td><span class="jea-device-info">' + deviceInfo + '</span></td>';
                html += '<td><span class="jea-page-title" title="' + pageTitle + '">' + (pageTitle.length > 30 ? pageTitle.substr(0, 30) + '...' : pageTitle) + '</span></td>';
                html += '<td><span class="jea-visit-time">' + visitTime + '</span></td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $container.html(html);
        },

        // 更新统计数字
        updateStats: function(overview) {
            // 访客数
            this.animateValue($('#stat-visitors'), overview.visitors.value);
            this.updateChange($('#stat-visitors-change'), overview.visitors.change);

            // 浏览量
            this.animateValue($('#stat-pageviews'), overview.pageviews.value);
            this.updateChange($('#stat-pageviews-change'), overview.pageviews.change);

            // 会话数
            this.animateValue($('#stat-sessions'), overview.sessions.value);
            this.updateChange($('#stat-sessions-change'), overview.sessions.change);

            // 跳出率
            $('#stat-bounce-rate').text(overview.bounce_rate.value + '%');
            this.updateChange($('#stat-bounce-change'), overview.bounce_rate.change, true);

            // 平均时长
            $('#stat-avg-duration').text(this.formatDuration(overview.avg_duration.value));
            this.updateChange($('#stat-duration-change'), overview.avg_duration.change);

            // 页面/会话
            $('#stat-pages-per-session').text(overview.pages_per_session.value);
            this.updateChange($('#stat-pages-change'), overview.pages_per_session.change);
        },

        // 数字动画
        animateValue: function($el, value) {
            const current = parseInt($el.text().replace(/,/g, '')) || 0;
            const duration = 500;
            const steps = 20;
            const increment = (value - current) / steps;
            let step = 0;

            const timer = setInterval(function() {
                step++;
                const newValue = Math.round(current + (increment * step));
                $el.text(newValue.toLocaleString());

                if (step >= steps) {
                    clearInterval(timer);
                    $el.text(value.toLocaleString());
                }
            }, duration / steps);
        },

        // 更新变化百分比
        updateChange: function($el, change, inverse) {
            const isUp = inverse ? change < 0 : change >= 0;
            const icon = isUp ? '↑' : '↓';
            const className = isUp ? 'up' : 'down';

            $el.removeClass('up down neutral')
               .addClass(change === 0 ? 'neutral' : className)
               .html(icon + ' ' + Math.abs(change) + '%');
        },

        // 格式化时长
        formatDuration: function(seconds) {
            if (seconds < 60) {
                return seconds + '秒';
            } else if (seconds < 3600) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return m + '分' + (s > 0 ? s + '秒' : '');
            } else {
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                return h + '时' + (m > 0 ? m + '分' : '');
            }
        },

        // 初始化图表
        initCharts: function() {
            // 设置Chart.js默认配置
            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            Chart.defaults.color = '#64748b';
        },

        // 加载图表数据
        loadChartData: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_chart_data',
                    nonce: jeaAdmin.nonce,
                    chart: 'visitors',
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success) {
                        self.renderMainChart(response.data);
                    }
                }
            });
        },

        // 渲染主图表
        renderMainChart: function(data) {
            const ctx = document.getElementById('mainChart');
            if (!ctx) return;

            // 销毁旧图表
            if (this.charts.main) {
                this.charts.main.destroy();
            }

            const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient1.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
            gradient1.addColorStop(1, 'rgba(99, 102, 241, 0)');

            const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient2.addColorStop(0, 'rgba(14, 165, 233, 0.3)');
            gradient2.addColorStop(1, 'rgba(14, 165, 233, 0)');

            this.charts.main = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: '访客',
                            data: data.datasets.visitors,
                            borderColor: this.colors.primary,
                            backgroundColor: gradient1,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: this.colors.primary,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        },
                        {
                            label: '浏览量',
                            data: data.datasets.pageviews,
                            borderColor: this.colors.secondary,
                            backgroundColor: gradient2,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: this.colors.secondary,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                },
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return (value / 1000) + 'k';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        },

        // 更新热门页面
        updateTopPages: function(pages) {
            const $container = $('#top-pages-list');
            if (!$container.length) return;

            if (!pages || pages.length === 0) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">📄</div><div class="jea-empty-title">暂无数据</div></div>');
                return;
            }

            let html = '<table class="jea-table"><thead><tr><th>页面</th><th>浏览量</th><th>访客</th></tr></thead><tbody>';

            pages.forEach(function(page) {
                const path = page.page_url.replace(/https?:\/\/[^\/]+/, '') || '/';
                const title = page.page_title || path;

                html += '<tr>';
                html += '<td><div class="jea-page-url"><a href="' + page.page_url + '" target="_blank" title="' + title + '">' + (title.length > 40 ? title.substr(0, 40) + '...' : title) + '</a></div></td>';
                html += '<td><strong>' + parseInt(page.views).toLocaleString() + '</strong></td>';
                html += '<td>' + parseInt(page.visitors).toLocaleString() + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $container.html(html);
        },

        // 更新来源
        updateReferrers: function(referrers) {
            const $container = $('#referrers-list');
            if (!$container.length) return;

            if (!referrers || referrers.length === 0) {
                $container.html('<div class="jea-empty"><div class="jea-empty-icon">🔗</div><div class="jea-empty-title">暂无数据</div></div>');
                return;
            }

            const total = referrers.reduce((sum, r) => sum + parseInt(r.sessions), 0);
            const colors = [this.colors.primary, this.colors.secondary, this.colors.success, this.colors.warning, this.colors.purple];

            let html = '<div class="jea-pie-legend">';

            referrers.forEach(function(ref, index) {
                const percent = total > 0 ? ((ref.sessions / total) * 100).toFixed(1) : 0;
                const color = colors[index % colors.length];
                const icon = {
                    'direct': '🔗',
                    'search': '🔍',
                    'social': '📱',
                    'referral': '🌐',
                    'email': '📧'
                }[ref.referrer_type] || '🔗';

                html += '<div class="jea-pie-legend-item">';
                html += '<div class="jea-pie-legend-label">';
                html += '<span class="jea-pie-legend-color" style="background:' + color + '"></span>';
                html += '<span class="jea-pie-legend-text">' + icon + ' ' + ref.label + '</span>';
                html += '</div>';
                html += '<div class="jea-pie-legend-value">' + parseInt(ref.sessions).toLocaleString() + '<span class="jea-pie-legend-percent">(' + percent + '%)</span></div>';
                html += '</div>';
            });

            html += '</div>';
            $container.html(html);
        },

        // 更新设备分布
        updateDevices: function(devices) {
            const $container = $('#devices-chart');
            if (!$container.length || !devices || devices.length === 0) return;

            const ctx = document.getElementById('devicesChart');
            if (!ctx) return;

            if (this.charts.devices) {
                this.charts.devices.destroy();
            }

            const labels = devices.map(d => d.label);
            const data = devices.map(d => parseInt(d.count));
            const colors = [this.colors.primary, this.colors.secondary, this.colors.success];

            this.charts.devices = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16,
                                font: {
                                    size: 12,
                                }
                            }
                        }
                    }
                }
            });
        },

        // 更新浏览器分布
        updateBrowsers: function(browsers) {
            const $container = $('#browsers-list');
            if (!$container.length) return;

            if (!browsers || browsers.length === 0) {
                $container.html('<div class="jea-empty">暂无数据</div>');
                return;
            }

            const total = browsers.reduce((sum, b) => sum + parseInt(b.count), 0);
            let html = '';

            browsers.slice(0, 5).forEach(function(browser) {
                const percent = total > 0 ? ((browser.count / total) * 100).toFixed(1) : 0;
                const icon = {
                    'Chrome': '🌐',
                    'Firefox': '🦊',
                    'Safari': '🧭',
                    'Edge': '🌊',
                    'Opera': '🔴'
                }[browser.browser] || '🌐';

                html += '<div class="jea-progress-item">';
                html += '<div class="jea-progress-header">';
                html += '<span class="jea-progress-label">' + icon + ' ' + browser.browser + '</span>';
                html += '<span class="jea-progress-value">' + percent + '%</span>';
                html += '</div>';
                html += '<div class="jea-progress-bar"><div class="jea-progress-fill primary" style="width:' + percent + '%"></div></div>';
                html += '</div>';
            });

            $container.html(html);
        },

        // 更新国家分布
        updateCountries: function(countries) {
            const $container = $('#countries-list');
            if (!$container.length) return;

            if (!countries || countries.length === 0) {
                $container.html('<div class="jea-empty">暂无数据</div>');
                return;
            }

            let html = '<div class="jea-country-list">';

            countries.forEach(function(country) {
                const flag = country.country_code ?
                    String.fromCodePoint(...country.country_code.toUpperCase().split('').map(c => 127397 + c.charCodeAt(0))) : '🌍';

                html += '<div class="jea-country-item">';
                html += '<span class="jea-country-flag">' + flag + '</span>';
                html += '<span class="jea-country-name">' + (country.country || 'Unknown') + '</span>';
                html += '<span class="jea-country-count">' + parseInt(country.count).toLocaleString() + '</span>';
                html += '</div>';
            });

            html += '</div>';
            $container.html(html);
        },

        // 更新小时分布图
        updateHourlyChart: function(hourly) {
            const $container = $('#hourly-chart');
            if (!$container.length || !hourly) return;

            const max = Math.max(...hourly) || 1;

            let barsHtml = '<div class="jea-hourly-chart">';
            hourly.forEach(function(value, hour) {
                const height = Math.max(4, (value / max) * 100);
                barsHtml += '<div class="jea-hourly-bar" style="height:' + height + '%" data-value="' + hour + '时: ' + value + '次"></div>';
            });
            barsHtml += '</div>';

            let labelsHtml = '<div class="jea-hourly-labels">';
            for (let i = 0; i < 24; i += 4) {
                labelsHtml += '<span class="jea-hourly-label">' + i + ':00</span>';
            }
            labelsHtml += '</div>';

            $container.html(barsHtml + labelsHtml);
        },

        // 实时数据刷新
        startRealtimeRefresh: function() {
            const self = this;
            this.loadRealtimeData();

            setInterval(function() {
                self.loadRealtimeData();
            }, 30000); // 30秒刷新一次
        },

        // 加载实时数据
        loadRealtimeData: function() {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_get_realtime_data',
                    nonce: jeaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateRealtimeDisplay(response.data);
                    }
                }
            });
        },

        // 更新实时显示
        updateRealtimeDisplay: function(data) {
            // 更新总数
            $('#realtime-count').text(data.total);

            // 更新访客列表
            const $list = $('#realtime-visitors');
            if (!$list.length) return;

            if (data.visitors.length === 0) {
                $list.html('<div class="jea-empty"><div class="jea-empty-icon">👥</div><div class="jea-empty-title">当前没有在线访客</div></div>');
                return;
            }

            let html = '';
            data.visitors.forEach(function(visitor) {
                const flag = visitor.country_code ?
                    String.fromCodePoint(...visitor.country_code.toUpperCase().split('').map(c => 127397 + c.charCodeAt(0))) : '🌍';

                const path = visitor.page_url.replace(/https?:\/\/[^\/]+/, '') || '/';
                const time = new Date(visitor.last_activity).toLocaleTimeString();

                html += '<div class="jea-visitor-item">';
                html += '<span class="jea-visitor-flag">' + flag + '</span>';
                html += '<div class="jea-visitor-info">';
                html += '<div class="jea-visitor-page">' + (visitor.page_title || path) + '</div>';
                html += '<div class="jea-visitor-meta">';
                html += '<span>' + (visitor.city || visitor.country || 'Unknown') + '</span>';
                html += '<span>' + visitor.browser + '</span>';
                html += '<span>' + visitor.device_type + '</span>';
                html += '</div>';
                html += '</div>';
                html += '<span class="jea-visitor-time">' + time + '</span>';
                html += '</div>';
            });

            $list.html(html);

            // 更新设备分布
            if (data.by_device) {
                const total = Object.values(data.by_device).reduce((a, b) => a + b, 0);
                let deviceHtml = '';

                Object.entries(data.by_device).forEach(([type, count]) => {
                    const label = { desktop: '桌面', mobile: '移动', tablet: '平板' }[type] || type;
                    const percent = total > 0 ? Math.round((count / total) * 100) : 0;

                    deviceHtml += '<div class="jea-progress-item">';
                    deviceHtml += '<div class="jea-progress-header">';
                    deviceHtml += '<span class="jea-progress-label">' + label + '</span>';
                    deviceHtml += '<span class="jea-progress-value">' + count + '</span>';
                    deviceHtml += '</div>';
                    deviceHtml += '<div class="jea-progress-bar"><div class="jea-progress-fill primary" style="width:' + percent + '%"></div></div>';
                    deviceHtml += '</div>';
                });

                $('#realtime-devices').html(deviceHtml);
            }

            // 更新热门页面
            if (data.by_page && data.by_page.length > 0) {
                let pageHtml = '';

                data.by_page.slice(0, 5).forEach(function(page) {
                    const path = page.url.replace(/https?:\/\/[^\/]+/, '') || '/';

                    pageHtml += '<div class="jea-visitor-item">';
                    pageHtml += '<div class="jea-visitor-info">';
                    pageHtml += '<div class="jea-visitor-page">' + (page.title || path) + '</div>';
                    pageHtml += '</div>';
                    pageHtml += '<span class="jea-badge jea-badge-primary">' + page.count + '</span>';
                    pageHtml += '</div>';
                });

                $('#realtime-pages').html(pageHtml);
            }
        },

        // 导出数据
        exportData: function(type, format) {
            const self = this;

            $.ajax({
                url: jeaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jea_export_data',
                    nonce: jeaAdmin.nonce,
                    type: type,
                    format: format,
                    range: this.currentRange
                },
                success: function(response) {
                    if (response.success && response.data.url) {
                        window.location.href = response.data.url;
                    } else {
                        alert('导出失败，请重试');
                    }
                },
                error: function() {
                    alert('导出失败，请重试');
                }
            });
        }
    };

    // 初始化
    $(document).ready(function() {
        if ($('.jea-wrap').length) {
            JEA.init();
        }
    });

})(jQuery);
